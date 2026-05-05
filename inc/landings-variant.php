<?php
/**
 * Landings: variante de plantilla / piel CSS (ACF + assets + partial).
 *
 * Arquitectura CSS:
 * - Core del sitio (style.css, Bootstrap, icons, slick, responsive.css) sigue cargándose
 *   en todas las URLs; define tipografía, rejilla, header/footer.
 * - @layer skema-theme-base, skema-theme-landings (style.css + responsive): formularios globales
 *   en «base»; overrides de landings/prelanding en «landings» (ver phase-prelanding.css).
 * - Inicio (theme_inicio.php): css/home/skema-home-hero.css + skema-home-proyectos.css (ver theme_skema_scripts).
 * - landings-shared.css: layout común (entry, logo proyecto); sin cabecera hero.
 * - skema-landing-hero.css: slider cabecera landings (solo .site-main--landings .landing-hero).
 * - skins/{slug}/skin.css: overrides por variante (ej. promo).
 * - phase-{fase}.css: prelanding vs landing completa (hero full viewport en phase-landing.css); encola después del skin.
 * - Partials: entry-{variante}-{fase}.php → entry-{variante}.php → entry-default-{fase}.php → entry-default.php.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/** Meta key / nombre de campo ACF (mismo valor guardado en post_meta). */
const THEME_SKEMA_LANDING_VARIANT_FIELD = 'skema_landing_variant';

/** Fase editorial: prelanding (por defecto) o landing completa. */
const THEME_SKEMA_LANDING_PHASE_FIELD = 'skema_landing_phase';

/**
 * Comprueba que una ruta relativa a css/landings sea segura (sin .. ni segmentos raros).
 *
 * @param string $relative Ruta tipo "phase-prelanding.css" o "skins/promo/skin.css".
 */
function theme_skema_landing_is_safe_landings_css_relative( $relative ) {
	if ( ! is_string( $relative ) || $relative === '' ) {
		return false;
	}

	if ( strpos( $relative, '..' ) !== false ) {
		return false;
	}

	$normalized = str_replace( '\\', '/', $relative );
	if ( $normalized !== '' && $normalized[0] === '/' ) {
		return false;
	}

	$parts = explode( '/', $normalized );
	$last_index = count( $parts ) - 1;

	foreach ( $parts as $index => $part ) {
		if ( $part === '' ) {
			return false;
		}

		$is_filename = ( $index === $last_index );
		if ( $is_filename ) {
			if ( ! preg_match( '/^[a-z0-9][a-z0-9_-]*\.css$/', $part ) ) {
				return false;
			}
			continue;
		}

		if ( ! preg_match( '/^[a-z0-9][a-z0-9_-]*$/', $part ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Variantes permitidas (lista blanca). Añade filas y el CSS bajo css/landings/skins/{slug}/skin.css
 * (o otra ruta relativa segura; ver theme_skema_landing_is_safe_landings_css_relative).
 *
 * @return array<string, array{label: string, skin_file: string}>
 */
function theme_skema_landing_variant_definitions() {
	return array(
		'default' => array(
			'label'     => __( 'Estándar (tema)', 'theme_skema' ),
			'skin_file' => 'skins/default/skin.css',
		),
		'promo'   => array(
			'label'     => __( 'Promoción / CTA destacado', 'theme_skema' ),
			'skin_file' => 'skins/promo/skin.css',
		),
	);
}

/**
 * Slug de variante para el post actual o el indicado.
 *
 * @param int|null $post_id ID de post o null para el loop / consulta actual.
 * @return string Slug seguro (keys del array de definiciones).
 */
function theme_skema_get_landing_variant( $post_id = null ) {
	$defs = theme_skema_landing_variant_definitions();
	$allowed = array_keys( $defs );
	$default = 'default';

	if ( null === $post_id ) {
		$post_id = get_queried_object_id();
	}
	if ( ! $post_id || 'landings' !== get_post_type( $post_id ) ) {
		return $default;
	}

	$value = '';
	if ( function_exists( 'get_field' ) ) {
		$raw = get_field( THEME_SKEMA_LANDING_VARIANT_FIELD, $post_id );
		if ( is_string( $raw ) && $raw !== '' ) {
			$value = $raw;
		}
	}
	if ( $value === '' ) {
		$value = (string) get_post_meta( $post_id, THEME_SKEMA_LANDING_VARIANT_FIELD, true );
	}

	$value = sanitize_key( $value );
	if ( ! in_array( $value, $allowed, true ) ) {
		return $default;
	}

	return $value;
}

/**
 * Choices para campo ACF select (value => label).
 *
 * @return array<string, string>
 */
function theme_skema_landing_variant_acf_choices() {
	$out = array();
	foreach ( theme_skema_landing_variant_definitions() as $slug => $row ) {
		$out[ $slug ] = $row['label'];
	}
	return $out;
}

/**
 * Fases permitidas (lista blanca): prelanding por defecto en CMS y en código.
 *
 * @return array<string, array{label: string, phase_file: string}>
 */
function theme_skema_landing_phase_definitions() {
	return array(
		'prelanding' => array(
			'label'      => __( 'Prelanding (teaser / captación)', 'theme_skema' ),
			'phase_file' => 'phase-prelanding.css',
		),
		'landing'    => array(
			'label'      => __( 'Landing completa', 'theme_skema' ),
			'phase_file' => 'phase-landing.css',
		),
	);
}

/**
 * Slug de fase (prelanding | landing) para el post actual o el indicado.
 *
 * @param int|null $post_id ID de post o null para la consulta actual.
 * @return string
 */
function theme_skema_get_landing_phase( $post_id = null ) {
	$defs    = theme_skema_landing_phase_definitions();
	$allowed = array_keys( $defs );
	$default = 'prelanding';

	if ( null === $post_id ) {
		$post_id = get_queried_object_id();
	}
	if ( ! $post_id || 'landings' !== get_post_type( $post_id ) ) {
		return $default;
	}

	$value = '';
	if ( function_exists( 'get_field' ) ) {
		$raw = get_field( THEME_SKEMA_LANDING_PHASE_FIELD, $post_id );
		if ( is_string( $raw ) && $raw !== '' ) {
			$value = $raw;
		}
	}
	if ( $value === '' ) {
		$value = (string) get_post_meta( $post_id, THEME_SKEMA_LANDING_PHASE_FIELD, true );
	}

	$value = sanitize_key( $value );
	if ( ! in_array( $value, $allowed, true ) ) {
		return $default;
	}

	return $value;
}

/**
 * Choices ACF para fase.
 *
 * @return array<string, string>
 */
function theme_skema_landing_phase_acf_choices() {
	$out = array();
	foreach ( theme_skema_landing_phase_definitions() as $slug => $row ) {
		$out[ $slug ] = $row['label'];
	}
	return $out;
}

/**
 * Registra grupo ACF en CPT landings (orden antes de «Ficha y carrusel»).
 */
function theme_skema_acf_register_landing_variant() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_landing_variant',
			'title'                 => __( 'Plantilla / estilo de landing', 'theme_skema' ),
			'fields'                => array(
				array(
					'key'           => 'field_skema_landing_variant',
					'label'         => __( 'Variante de plantilla', 'theme_skema' ),
					'name'          => THEME_SKEMA_LANDING_VARIANT_FIELD,
					'type'          => 'select',
					'instructions'  => __( 'Elige la piel visual y el bloque PHP asociado. El CSS del tema global siempre se carga; la variante añade estilos propios de landing.', 'theme_skema' ),
					'required'      => 0,
					'choices'       => theme_skema_landing_variant_acf_choices(),
					'default_value' => 'default',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'return_format' => 'value',
				),
				array(
					'key'           => 'field_skema_landing_phase',
					'label'         => __( 'Fase de la landing', 'theme_skema' ),
					'name'          => THEME_SKEMA_LANDING_PHASE_FIELD,
					'type'          => 'select',
					'instructions'  => __( 'Prelanding: teaser o captación por defecto. «Landing completa» cuando publiques la versión larga o de conversión en la misma entrada (o duplica la entrada si preferís URLs distintas).', 'theme_skema' ),
					'required'      => 0,
					'choices'       => theme_skema_landing_phase_acf_choices(),
					'default_value' => 'prelanding',
					'allow_null'    => 0,
					'multiple'      => 0,
					'ui'            => 1,
					'return_format' => 'value',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'landings',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'theme_skema_acf_register_landing_variant' );

/**
 * Encola CSS compartido de landings + skin de la variante (solo en single landings).
 */
function theme_skema_enqueue_landing_variant_styles() {
	if ( ! is_singular( 'landings' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	if ( function_exists( 'theme_skema_get_landing_phase' ) && 'landing' === theme_skema_get_landing_phase( $post_id ) && function_exists( 'theme_skema_enqueue_fontawesome_on_landings' ) ) {
		theme_skema_enqueue_fontawesome_on_landings();
	}

	$base_uri = get_template_directory_uri() . '/css/landings';
	$base_dir = get_template_directory() . '/css/landings';

	// Después del responsive del tema para poder ajustar layout sin tocar el core.
	$parent = array( 'theme_skema-style', 'theme_skema-responsive-css' );

	$shared_path = $base_dir . '/landings-shared.css';
	if ( is_readable( $shared_path ) ) {
		wp_enqueue_style(
			'theme_skema-landings-shared',
			$base_uri . '/landings-shared.css',
			$parent,
			_S_VERSION
		);
		$parent = array( 'theme_skema-landings-shared' );
	}

	$header_renvia_path = $base_dir . '/skema-landings-header-renvia-desktop.css';
	if ( is_readable( $header_renvia_path ) ) {
		wp_enqueue_style(
			'theme_skema-landings-header-renvia',
			$base_uri . '/skema-landings-header-renvia-desktop.css',
			$parent,
			_S_VERSION
		);
		$parent = array( 'theme_skema-landings-header-renvia' );
	}

	$hero_css = $base_dir . '/skema-landing-hero.css';
	if ( is_readable( $hero_css ) ) {
		wp_enqueue_style(
			'theme_skema-landings-hero',
			$base_uri . '/skema-landing-hero.css',
			$parent,
			_S_VERSION
		);
		$parent = array( 'theme_skema-landings-hero' );
	}

	$footer_css = $base_dir . '/footer-landings.css';
	if ( is_readable( $footer_css ) ) {
		wp_enqueue_style(
			'theme_skema-landings-footer',
			$base_uri . '/footer-landings.css',
			$parent,
			_S_VERSION
		);
		$parent = array( 'theme_skema-landings-footer' );
	}

	$variant = theme_skema_get_landing_variant( $post_id );
	$defs    = theme_skema_landing_variant_definitions();
	$row     = isset( $defs[ $variant ] ) ? $defs[ $variant ] : $defs['default'];
	$file    = isset( $row['skin_file'] ) ? $row['skin_file'] : '';

	if ( $file === '' || ! theme_skema_landing_is_safe_landings_css_relative( $file ) ) {
		return;
	}

	$skin_fs = $base_dir . '/' . $file;
	if ( ! is_readable( $skin_fs ) && isset( $defs['default']['skin_file'] ) ) {
		$file    = $defs['default']['skin_file'];
		$skin_fs = $base_dir . '/' . $file;
		$variant = 'default';
	}
	if ( ! is_readable( $skin_fs ) ) {
		return;
	}

	$skin_slug = $variant;
	wp_enqueue_style(
		'theme_skema-landing-skin-' . $skin_slug,
		$base_uri . '/' . $file,
		$parent,
		_S_VERSION
	);

	$skin_handle = 'theme_skema-landing-skin-' . $skin_slug;

	$phase       = theme_skema_get_landing_phase( $post_id );
	$phase_defs  = theme_skema_landing_phase_definitions();
	$phase_row   = isset( $phase_defs[ $phase ] ) ? $phase_defs[ $phase ] : $phase_defs['prelanding'];
	$phase_file  = isset( $phase_row['phase_file'] ) ? $phase_row['phase_file'] : '';

	if ( $phase_file !== '' && theme_skema_landing_is_safe_landings_css_relative( $phase_file ) ) {
		$phase_fs = $base_dir . '/' . $phase_file;
		if ( ! is_readable( $phase_fs ) && isset( $phase_defs['prelanding']['phase_file'] ) ) {
			$phase_file = $phase_defs['prelanding']['phase_file'];
			$phase_fs   = $base_dir . '/' . $phase_file;
			$phase      = 'prelanding';
		}
		if ( is_readable( $phase_fs ) ) {
			wp_enqueue_style(
				'theme_skema-landing-phase-' . $phase,
				$base_uri . '/' . $phase_file,
				array( $skin_handle ),
				_S_VERSION
			);

			$typography_path = $base_dir . '/landings-typography.css';
			if ( is_readable( $typography_path ) ) {
				wp_enqueue_style(
					'theme_skema-landings-typography',
					$base_uri . '/landings-typography.css',
					array( 'theme_skema-landing-phase-' . $phase ),
					_S_VERSION
				);
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'theme_skema_enqueue_landing_variant_styles', 25 );

/**
 * Encola JS de galería modal (Apto modelo) en single landings fase «landing».
 *
 * @return void
 */
function theme_skema_enqueue_landing_medios_gallery_script() {
	if ( ! is_singular( 'landings' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return;
	}

	if ( ! function_exists( 'theme_skema_get_landing_phase' ) || 'landing' !== theme_skema_get_landing_phase( $post_id ) ) {
		return;
	}

	$path = get_template_directory() . '/js/skema-landing-medios-gallery.js';
	if ( ! is_readable( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'theme-skema-landing-medios-gallery',
		get_template_directory_uri() . '/js/skema-landing-medios-gallery.js',
		array( 'theme_skema-bootstrap-bundle-js' ),
		_S_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'theme_skema_enqueue_landing_medios_gallery_script', 26 );

/**
 * Encola JS del slider «Plantas» (Slick + pestaña Bootstrap 4) en single landings fase «landing».
 *
 * @return void
 */
function theme_skema_enqueue_landing_plantas_slider_script() {
	if ( ! is_singular( 'landings' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return;
	}

	if ( ! function_exists( 'theme_skema_get_landing_phase' ) || 'landing' !== theme_skema_get_landing_phase( $post_id ) ) {
		return;
	}

	$path = get_template_directory() . '/js/skema-landing-plantas-slider.js';
	if ( ! is_readable( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'theme-skema-landing-plantas-slider',
		get_template_directory_uri() . '/js/skema-landing-plantas-slider.js',
		array( 'jquery', 'theme_skema-slickslider-js', 'theme_skema-bootstrap-bundle-js' ),
		_S_VERSION,
		true
	);

	wp_localize_script(
		'theme-skema-landing-plantas-slider',
		'skemaLandingPlantasI18n',
		array(
			'prevMain' => __( 'Planta anterior', 'theme_skema' ),
			'nextMain' => __( 'Planta siguiente', 'theme_skema' ),
			'prevNav'  => __( 'Miniaturas anteriores', 'theme_skema' ),
			'nextNav'  => __( 'Miniaturas siguientes', 'theme_skema' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'theme_skema_enqueue_landing_plantas_slider_script', 26 );

/**
 * Encola JS del carrusel «Con el respaldo de» (Slick) en single landings fase «landing».
 *
 * @return void
 */
function theme_skema_enqueue_landing_respaldo_slider_script() {
	if ( ! is_singular( 'landings' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return;
	}

	if ( ! function_exists( 'theme_skema_get_landing_phase' ) || 'landing' !== theme_skema_get_landing_phase( $post_id ) ) {
		return;
	}

	$path = get_template_directory() . '/js/skema-landing-respaldo-slider.js';
	if ( ! is_readable( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'theme-skema-landing-respaldo-slider',
		get_template_directory_uri() . '/js/skema-landing-respaldo-slider.js',
		array( 'jquery', 'theme_skema-slickslider-js' ),
		_S_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'theme_skema_enqueue_landing_respaldo_slider_script', 26 );

/**
 * Clase en body para targeting global opcional.
 *
 * @param array<int, string> $classes Clases existentes.
 * @return array<int, string>
 */
function theme_skema_body_class_landing_variant( $classes ) {
	if ( is_singular( 'landings' ) ) {
		$classes[] = 'skema-landing-shell';
		$classes[] = 'landing-variant--' . theme_skema_get_landing_variant();
		$classes[] = 'landing-phase--' . theme_skema_get_landing_phase();
	}
	return $classes;
}
add_filter( 'body_class', 'theme_skema_body_class_landing_variant' );

/**
 * Localiza el partial de entrada (variante + fase con fallbacks).
 *
 * @return string Ruta absoluta o cadena vacía.
 */
function theme_skema_landing_locate_entry_template() {
	$variant = theme_skema_get_landing_variant();
	$phase   = theme_skema_get_landing_phase();
	$v       = sanitize_file_name( $variant );
	$p       = sanitize_file_name( $phase );

	$candidates = array(
		'template-parts/landings/entry-' . $v . '-' . $p . '.php',
		'template-parts/landings/entry-' . $v . '.php',
		'template-parts/landings/entry-default-' . $p . '.php',
		'template-parts/landings/entry-default.php',
	);

	foreach ( $candidates as $relative ) {
		$path = locate_template( $relative );
		if ( $path ) {
			return $path;
		}
	}

	return '';
}

/**
 * Carga el partial entry según variante y fase (ver theme_skema_landing_locate_entry_template).
 */
function theme_skema_landing_entry_template() {
	$located = theme_skema_landing_locate_entry_template();
	if ( $located ) {
		load_template( $located, false );
		return;
	}
	the_content();
}
