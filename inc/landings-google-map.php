<?php
/**
 * Landings: mapa embebido (Google Maps).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Atributos permitidos en el iframe del embed de Google Maps.
 *
 * @return array<string, array<string, bool>>
 */
function theme_skema_landing_google_maps_iframe_allowed_html() {
	return array(
		'iframe' => array(
			'src'               => true,
			'width'             => true,
			'height'            => true,
			'style'             => true,
			'allow'             => true,
			'allowfullscreen'   => true,
			'loading'           => true,
			'referrerpolicy'    => true,
			'title'             => true,
			'class'             => true,
			'frameborder'       => true,
			'aria-label'        => true,
		),
	);
}

/**
 * Comprueba si el host es un dominio de Google aceptado para mapas embebidos.
 *
 * @param string $host Host en minúsculas.
 */
function theme_skema_landing_is_trusted_google_maps_host( $host ) {
	$host = strtolower( trim( (string) $host ) );
	if ( $host === '' ) {
		return false;
	}

	if ( in_array( $host, array( 'google.com', 'www.google.com', 'maps.google.com' ), true ) ) {
		return true;
	}

	return (bool) preg_match( '/(\.google\.com|\.google\.[a-z]{2,3}(\.[a-z]{2})?)$/', $host );
}

/**
 * Valida que la URL sea un embed de Google Maps (no enlaces cortos ni búsqueda genérica).
 *
 * @param string $url URL absoluta.
 */
function theme_skema_landing_is_allowed_google_maps_embed_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return false;
	}

	$parsed = wp_parse_url( $url );
	if ( ! is_array( $parsed ) || empty( $parsed['scheme'] ) ) {
		return false;
	}

	if ( ! in_array( strtolower( (string) $parsed['scheme'] ), array( 'http', 'https' ), true ) ) {
		return false;
	}

	$host = isset( $parsed['host'] ) ? (string) $parsed['host'] : '';
	if ( $host === '' || ! theme_skema_landing_is_trusted_google_maps_host( $host ) ) {
		return false;
	}

	$path = isset( $parsed['path'] ) ? (string) $parsed['path'] : '';
	if ( strpos( $path, '/maps/embed' ) !== false ) {
		return true;
	}

	return false;
}

/**
 * Normaliza la entrada del editor: código iframe o solo URL de embed.
 *
 * @param string $raw Texto del campo ACF.
 */
function theme_skema_landing_normalize_google_maps_embed_input( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	if ( preg_match( '/<iframe/i', $raw ) ) {
		return $raw;
	}

	if ( theme_skema_landing_is_allowed_google_maps_embed_url( $raw ) ) {
		return sprintf(
			'<iframe src="%s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="%s"></iframe>',
			esc_url( $raw ),
			esc_attr__( 'Ubicación del proyecto en Google Maps', 'theme_skema' )
		);
	}

	return '';
}

/**
 * Extrae y valida la URL src del primer iframe en un fragmento HTML.
 *
 * @param string $html HTML saneado.
 */
function theme_skema_landing_google_maps_iframe_src_is_valid( $html ) {
	if ( ! preg_match( '/<iframe[^>]+src\s*=\s*["\']([^"\']+)["\']/i', $html, $matches ) ) {
		return false;
	}

	$src = html_entity_decode( $matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	return theme_skema_landing_is_allowed_google_maps_embed_url( $src );
}

/**
 * Devuelve HTML seguro del iframe o cadena vacía si no es válido.
 *
 * @param int $post_id ID del CPT landings.
 */
function theme_skema_landing_get_google_maps_embed_html( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$raw = get_field( 'skema_lland_mapa_embed', $post_id );
	$raw = is_string( $raw ) ? $raw : '';

	$normalized = theme_skema_landing_normalize_google_maps_embed_input( $raw );
	if ( $normalized === '' ) {
		return '';
	}

	$allowed = theme_skema_landing_google_maps_iframe_allowed_html();
	$clean   = wp_kses( $normalized, $allowed );

	if ( $clean === '' || ! theme_skema_landing_google_maps_iframe_src_is_valid( $clean ) ) {
		return '';
	}

	return $clean;
}

/**
 * Valor de un campo ACF de texto, recortado (vacío si no hay datos).
 *
 * @param int    $post_id  ID del CPT landings.
 * @param string $acf_name Nombre del campo.
 */
function theme_skema_landing_get_optional_acf_text_trimmed( $post_id, $acf_name ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$raw = get_field( $acf_name, $post_id );
	$raw = is_string( $raw ) ? trim( $raw ) : '';

	return $raw;
}

/**
 * Título de sección: texto del campo ACF o valor por defecto (traducido en el llamador).
 *
 * @param int    $post_id  ID del CPT landings.
 * @param string $acf_name Nombre del campo de texto en ACF.
 * @param string $fallback Texto si el campo está vacío.
 */
function theme_skema_landing_get_section_title_or_default( $post_id, $acf_name, $fallback ) {
	$custom = theme_skema_landing_get_optional_acf_text_trimmed( $post_id, $acf_name );

	return $custom !== '' ? $custom : $fallback;
}

/**
 * Título de la sección del mapa (opcional en ACF).
 *
 * @param int $post_id ID del CPT landings.
 */
function theme_skema_landing_get_google_maps_section_title( $post_id ) {
	return theme_skema_landing_get_optional_acf_text_trimmed( $post_id, 'skema_lland_mapa_titulo' );
}
