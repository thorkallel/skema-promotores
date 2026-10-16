<?php
/**
 * Antispam de servidor para Contact Form 7.
 *
 * Cubre los tres formularios (contacto, ofrece tu lote y landings) porque
 * se engancha a los filtros de CF7, no a cada shortcode.
 *
 * El honeypot del plugin guarda un hash aleatorio en el HTML y un transient
 * de una hora. Con la caché de página de WP Rocket ese hash queda viejo y
 * marca envíos reales como spam. Por eso el señuelo de este archivo es HTML
 * fijo, y la marca de tiempo la escribe JavaScript.
 *
 * @package theme_skema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPCF7_Submission' ) ) {
	return;
}

define( 'SKEMA_CF7_TS_FIELD', '_skema_ts' );
define( 'SKEMA_CF7_HP_FIELD', 'empresa-url' );
define( 'SKEMA_CF7_MIN_SECONDS', 4 );
define( 'SKEMA_CF7_MAX_AGE', 2 * DAY_IN_SECONDS );
define( 'SKEMA_CF7_CLOCK_SKEW', 120 );
define( 'SKEMA_CF7_MAX_LINKS', 1 );
define( 'SKEMA_CF7_RATE_LIMIT', 3 );
define( 'SKEMA_CF7_RATE_WINDOW', 10 * MINUTE_IN_SECONDS );

add_filter( 'wpcf7_form_hidden_fields', 'theme_skema_cf7_add_timestamp_field' );
add_filter( 'wpcf7_form_elements', 'theme_skema_cf7_append_honeypot' );
add_action( 'wp_footer', 'theme_skema_cf7_print_timestamp_script', 100 );
add_filter( 'wpcf7_spam', 'theme_skema_cf7_filter_spam', 20, 2 );
add_filter( 'wpcf7_validate_tel', 'theme_skema_cf7_validate_celular', 20, 2 );
add_filter( 'wpcf7_validate_tel*', 'theme_skema_cf7_validate_celular', 20, 2 );

/**
 * Campo oculto vacío. El navegador lo rellena; la caché no puede congelar un timestamp de PHP.
 *
 * @param array $fields Campos ocultos de CF7.
 * @return array
 */
function theme_skema_cf7_add_timestamp_field( $fields ) {
	$fields[ SKEMA_CF7_TS_FIELD ] = '';
	return $fields;
}

/**
 * Señuelo estático. Si un bot lo rellena, el envío se marca como spam.
 *
 * @param string $html HTML ya renderizado del formulario.
 * @return string
 */
function theme_skema_cf7_append_honeypot( $html ) {
	if ( ! is_string( $html ) ) {
		return $html;
	}

	if ( strpos( $html, 'name="' . SKEMA_CF7_HP_FIELD . '"' ) !== false ) {
		return $html;
	}

	$honeypot  = '<span class="skema-cf7-hp" aria-hidden="true">';
	$honeypot .= '<label>' . esc_html__( 'Sitio web', 'theme_skema' );
	$honeypot .= '<input type="text" name="' . esc_attr( SKEMA_CF7_HP_FIELD ) . '" value="" tabindex="-1" autocomplete="new-password" />';
	$honeypot .= '</label></span>';

	return $html . $honeypot;
}

/**
 * Escribe la hora de carga de la página en el campo oculto.
 *
 * Usa performance.timeOrigin para que, si WP Rocket retrasa el script hasta
 * el primer clic, la marca siga siendo el momento real de carga.
 */
function theme_skema_cf7_print_timestamp_script() {
	if ( ! wp_script_is( 'contact-form-7', 'enqueued' ) ) {
		return;
	}

	$field_name = esc_js( SKEMA_CF7_TS_FIELD );
	?>
	<script data-nowprocket>
	document.addEventListener('DOMContentLoaded', function () {
		var forms = document.querySelectorAll('form.wpcf7-form');

		forms.forEach(function (form) {
			var field = form.querySelector('input[name="<?php echo $field_name; ?>"]');
			if (!field) {
				return;
			}

			var stamp = function () {
				if (field.value) {
					return;
				}

				var origin = 0;
				if (window.performance && typeof performance.timeOrigin === 'number') {
					origin = performance.timeOrigin;
				}
				if (!origin) {
					origin = Date.now();
				}

				field.value = String(Math.round(origin));
			};

			stamp();
			form.addEventListener('focusin', stamp);
			form.addEventListener('input', stamp);
			form.addEventListener('submit', stamp, true);
		});
	});
	</script>
	<?php
}

/**
 * @param bool              $spam       True si otra capa ya marcó el envío.
 * @param WPCF7_Submission  $submission Envío actual.
 * @return bool
 */
function theme_skema_cf7_filter_spam( $spam, $submission ) {
	if ( $spam ) {
		return $spam;
	}

	$reason = theme_skema_cf7_spam_reason( $submission );
	if ( $reason === '' ) {
		return $spam;
	}

	$submission->add_spam_log(
		array(
			'agent'  => 'theme_skema',
			'reason' => $reason,
		)
	);

	return true;
}

/**
 * Primera razón de spam, o cadena vacía si el envío puede salir.
 *
 * @param WPCF7_Submission $submission Envío actual.
 * @return string
 */
function theme_skema_cf7_spam_reason( $submission ) {
	$honeypot_reason = theme_skema_cf7_honeypot_reason();
	if ( $honeypot_reason !== '' ) {
		return $honeypot_reason;
	}

	$time_reason = theme_skema_cf7_timestamp_reason();
	if ( $time_reason !== '' ) {
		return $time_reason;
	}

	$ip = (string) $submission->get_meta( 'remote_ip' );
	$rate_reason = theme_skema_cf7_rate_limit_reason( $ip );
	if ( $rate_reason !== '' ) {
		return $rate_reason;
	}

	return theme_skema_cf7_content_reason( $submission->get_posted_data() );
}

/**
 * @return string
 */
function theme_skema_cf7_honeypot_reason() {
	if ( ! isset( $_POST[ SKEMA_CF7_HP_FIELD ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return '';
	}

	$value = wp_unslash( $_POST[ SKEMA_CF7_HP_FIELD ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_string( $value ) ) {
		return 'Señuelo rellenado.';
	}

	if ( trim( $value ) === '' ) {
		return '';
	}

	return 'Señuelo rellenado.';
}

/**
 * @return string
 */
function theme_skema_cf7_timestamp_reason() {
	if ( ! isset( $_POST[ SKEMA_CF7_TS_FIELD ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return 'Sin marca de tiempo (JS no ejecutado o POST directo).';
	}

	$raw = wp_unslash( $_POST[ SKEMA_CF7_TS_FIELD ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_string( $raw ) || ! ctype_digit( $raw ) ) {
		return 'Sin marca de tiempo (JS no ejecutado o POST directo).';
	}

	$started = (int) ( (int) $raw / 1000 );
	if ( $started <= 0 ) {
		return 'Sin marca de tiempo (JS no ejecutado o POST directo).';
	}

	$elapsed = time() - $started;
	if ( $elapsed < ( -1 * SKEMA_CF7_CLOCK_SKEW ) ) {
		return 'Marca de tiempo caducada o falsa.';
	}

	if ( $elapsed >= 0 && $elapsed < SKEMA_CF7_MIN_SECONDS ) {
		return sprintf( 'Enviado demasiado rápido (%d s).', $elapsed );
	}

	if ( $elapsed > SKEMA_CF7_MAX_AGE ) {
		return 'Marca de tiempo caducada o falsa.';
	}

	return '';
}

/**
 * Cuenta el intento y rechaza a partir del cuarto en 10 minutos.
 *
 * @param string $ip IP que reporta CF7.
 * @return string
 */
function theme_skema_cf7_rate_limit_reason( $ip ) {
	if ( $ip === '' ) {
		return '';
	}

	$key  = 'skema_cf7_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= SKEMA_CF7_RATE_LIMIT ) {
		return 'Límite de envíos por IP superado.';
	}

	set_transient( $key, $hits + 1, SKEMA_CF7_RATE_WINDOW );
	return '';
}

/**
 * @param mixed $posted_data Datos publicados por CF7.
 * @return string
 */
function theme_skema_cf7_content_reason( $posted_data ) {
	if ( ! is_array( $posted_data ) ) {
		return '';
	}

	$text = theme_skema_cf7_submission_text( $posted_data );
	if ( theme_skema_cf7_link_count( $text ) > SKEMA_CF7_MAX_LINKS ) {
		return 'Demasiados enlaces.';
	}

	if ( theme_skema_cf7_has_unexpected_script( $text ) ) {
		return 'Alfabeto no esperado.';
	}

	if ( theme_skema_cf7_has_blocked_phrase( $text ) ) {
		return 'Contenido no permitido.';
	}

	if ( theme_skema_cf7_name_looks_like_contact( $posted_data ) ) {
		return 'URL o correo en el campo nombre.';
	}

	if ( theme_skema_cf7_email_is_disposable( $posted_data ) ) {
		return 'Correo desechable.';
	}

	return '';
}

/**
 * Junta los campos de texto. Omite el señuelo para no mezclarlo con el mensaje.
 *
 * @param array $posted_data Datos publicados por CF7.
 * @return string
 */
function theme_skema_cf7_submission_text( $posted_data ) {
	$parts = array();

	foreach ( $posted_data as $name => $value ) {
		if ( $name === SKEMA_CF7_HP_FIELD ) {
			continue;
		}

		if ( ! is_scalar( $value ) ) {
			continue;
		}

		$parts[] = (string) $value;
	}

	return implode( ' ', $parts );
}

/**
 * @param string $text Texto del envío.
 * @return int
 */
function theme_skema_cf7_link_count( $text ) {
	$count = preg_match_all( '#https?://|www\.#i', $text );
	if ( ! is_int( $count ) ) {
		return 0;
	}

	return $count;
}

/**
 * El público escribe en español. Cirílico, chino, coreano, árabe o tailandés se trata como spam.
 *
 * @param string $text Texto del envío.
 * @return bool
 */
function theme_skema_cf7_has_unexpected_script( $text ) {
	$matched = preg_match( '/[\p{Cyrillic}\p{Han}\p{Hangul}\p{Arabic}\p{Thai}]/u', $text );
	return $matched === 1;
}

/**
 * @param string $text Texto del envío.
 * @return bool
 */
function theme_skema_cf7_has_blocked_phrase( $text ) {
	$haystack = strtolower( $text );

	foreach ( theme_skema_cf7_blocked_phrases() as $phrase ) {
		if ( strpos( $haystack, $phrase ) !== false ) {
			return true;
		}
	}

	return false;
}

/**
 * Frases vistas en el spam de Flamingo y en la lista del documento.
 * No incluye «crédito», «préstamo» ni «banco»: salen en consultas reales.
 *
 * @return string[]
 */
function theme_skema_cf7_blocked_phrases() {
	return array(
		'seo services',
		'backlinks',
		'crypto',
		'bitcoin',
		'casino',
		'viagra',
		'porn',
		'guest post',
		'ranking on google',
		'bit.ly',
		't.me/',
		'telegram',
		'whatsapp me at',
		'insane leads',
		'unsubscribe your website',
	);
}

/**
 * @param array $posted_data Datos publicados por CF7.
 * @return bool
 */
function theme_skema_cf7_name_looks_like_contact( $posted_data ) {
	if ( empty( $posted_data['nombre'] ) || ! is_string( $posted_data['nombre'] ) ) {
		return false;
	}

	return preg_match( '#https?://|www\.|@|\.com\b#i', $posted_data['nombre'] ) === 1;
}

/**
 * @param array $posted_data Datos publicados por CF7.
 * @return bool
 */
function theme_skema_cf7_email_is_disposable( $posted_data ) {
	if ( empty( $posted_data['email'] ) || ! is_string( $posted_data['email'] ) ) {
		return false;
	}

	$domain = theme_skema_cf7_email_domain( $posted_data['email'] );
	if ( $domain === '' ) {
		return false;
	}

	return in_array( $domain, theme_skema_cf7_disposable_domains(), true );
}

/**
 * @param string $email Correo publicado.
 * @return string
 */
function theme_skema_cf7_email_domain( $email ) {
	$at = strrchr( strtolower( trim( $email ) ), '@' );
	if ( $at === false ) {
		return '';
	}

	return substr( $at, 1 );
}

/**
 * @return string[]
 */
function theme_skema_cf7_disposable_domains() {
	return array(
		'mailinator.com',
		'yopmail.com',
		'guerrillamail.com',
		'sharklasers.com',
		'tempmail.com',
		'temp-mail.org',
		'10minutemail.com',
		'trashmail.com',
	);
}

/**
 * El error se muestra en el campo. No se usa como spam silencioso.
 *
 * @param WPCF7_Validation $result Resultado de CF7.
 * @param WPCF7_FormTag    $tag    Campo que se valida.
 * @return WPCF7_Validation
 */
function theme_skema_cf7_validate_celular( $result, $tag ) {
	if ( ! is_object( $tag ) || $tag->name !== 'celular' ) {
		return $result;
	}

	if ( ! isset( $_POST['celular'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $result;
	}

	$raw = wp_unslash( $_POST['celular'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	if ( ! is_string( $raw ) ) {
		return $result;
	}

	$raw = trim( $raw );
	if ( $raw === '' ) {
		return $result;
	}

	if ( theme_skema_cf7_celular_is_valid( $raw ) ) {
		return $result;
	}

	$result->invalidate( $tag, __( 'Escribe un número de celular válido.', 'theme_skema' ) );
	return $result;
}

/**
 * Acepta un celular de Colombia (7 a 10 dígitos) o con indicativo (hasta 15).
 *
 * @param string $value Valor escrito por la persona.
 * @return bool
 */
function theme_skema_cf7_celular_is_valid( $value ) {
	if ( preg_match( '/[a-z]/i', $value ) ) {
		return false;
	}

	$digits = preg_replace( '/\D+/', '', $value );
	$length = strlen( (string) $digits );

	if ( $length < 7 || $length > 15 ) {
		return false;
	}

	return true;
}
