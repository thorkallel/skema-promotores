<?php
/**
 * SEO: meta descripción, Open Graph y datos estructurados (solo si no hay plugin SEO).
 *
 * @package theme_skema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Comprueba si hay un plugin SEO que ya gestione meta y schema.
 *
 * @return bool
 */
function theme_skema_seo_plugin_active() {
	return (
		defined( 'WPSEO_VERSION' ) ||
		defined( 'AIOSEO_VERSION' ) ||
		defined( 'SEOPRESS_VERSION' ) ||
		defined( 'RANK_MATH_VERSION' ) ||
		class_exists( 'RankMath', false )
	);
}

/**
 * Descripción corta del sitio si el lema (Ajustes → Lectura) está vacío.
 *
 * @return string
 */
function theme_skema_get_fallback_site_description() {
	return sprintf(
		/* translators: %s: site title */
		__( 'Proyectos y desarrollos inmobiliarios de %s.', 'theme_skema' ),
		get_bloginfo( 'name' )
	);
}

/**
 * Texto para meta descripción (máx. ~160 caracteres).
 *
 * @return string
 */
function theme_skema_get_meta_description_text() {
	$desc = '';
	if ( is_singular() ) {
		$desc = get_the_excerpt();
		if ( '' === trim( wp_strip_all_tags( (string) $desc ) ) && get_post() ) {
			$desc = wp_trim_words( wp_strip_all_tags( get_post()->post_content ), 40, '…' );
		}
	} elseif ( is_home() || is_front_page() ) {
		$desc = get_bloginfo( 'description', 'display' );
		if ( '' === trim( wp_strip_all_tags( (string) $desc ) ) ) {
			$desc = theme_skema_get_fallback_site_description();
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$raw = term_description();
		if ( $raw ) {
			$desc = wp_strip_all_tags( $raw );
		}
	}

	$desc = trim( wp_strip_all_tags( (string) $desc ) );

	if ( '' === $desc ) {
		if ( is_singular() && get_post() ) {
			$desc = sprintf(
				/* translators: 1: post title, 2: site name */
				__( '%1$s — %2$s', 'theme_skema' ),
				get_the_title(),
				get_bloginfo( 'name' )
			);
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->name ) ) {
				$desc = sprintf(
					/* translators: 1: term name, 2: site name */
					__( 'Contenidos en «%1$s» — %2$s', 'theme_skema' ),
					$term->name,
					get_bloginfo( 'name' )
				);
			}
		}
	}

	if ( '' === $desc ) {
		return '';
	}
	if ( strlen( $desc ) > 160 ) {
		$desc = substr( $desc, 0, 157 ) . '…';
	}
	return $desc;
}

/**
 * Meta description en el head.
 */
function theme_skema_meta_description() {
	if ( theme_skema_seo_plugin_active() ) {
		return;
	}
	$desc = theme_skema_get_meta_description_text();
	if ( '' === $desc ) {
		return;
	}
	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
}
add_action( 'wp_head', 'theme_skema_meta_description', 1 );

/**
 * Open Graph básico (compartidos en redes).
 */
function theme_skema_og_tags() {
	if ( theme_skema_seo_plugin_active() ) {
		return;
	}
	$desc = theme_skema_get_meta_description_text();
	$url  = '';
	$type = 'website';
	$title = wp_get_document_title();

	if ( is_singular() ) {
		$url  = get_permalink();
		$type = 'article';
	} elseif ( is_front_page() || is_home() ) {
		$url = home_url( '/' );
	} elseif ( function_exists( 'wp_get_canonical_url' ) ) {
		$canon = wp_get_canonical_url();
		$url   = $canon ? $canon : '';
	}

	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $url ) {
		echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	}
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}

	$og_image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$og_image = get_the_post_thumbnail_url( null, 'large' );
	} elseif ( function_exists( 'get_field' ) ) {
		$logo = get_field( 'logo_marca', 'option' );
		if ( $logo ) {
			$og_image = esc_url( $logo );
		}
	}
	if ( $og_image ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
	}

	// Twitter Cards (alineadas con Open Graph).
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $og_image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'theme_skema_og_tags', 2 );

/**
 * JSON-LD Organization / RealEstateAgent.
 */
function theme_skema_json_ld_organization() {
	if ( theme_skema_seo_plugin_active() ) {
		return;
	}
	$logo = '';
	if ( function_exists( 'get_field' ) ) {
		$acf_logo = get_field( 'logo_marca', 'option' );
		if ( $acf_logo ) {
			$logo = esc_url( $acf_logo );
		}
	}

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'RealEstateAgent',
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
	);

	if ( $logo ) {
		$data['logo'] = $logo;
	}

	$tagline = trim( wp_strip_all_tags( (string) get_bloginfo( 'description', 'display' ) ) );
	if ( $tagline ) {
		$data['description'] = $tagline;
	} else {
		$data['description'] = theme_skema_get_fallback_site_description();
		if ( strlen( $data['description'] ) > 300 ) {
			$data['description'] = substr( $data['description'], 0, 297 ) . '…';
		}
	}

	$search_url = home_url( '/?s={search_term_string}' );
	$website    = array(
		'@type'           => 'WebSite',
		'name'            => get_bloginfo( 'name' ),
		'url'             => home_url( '/' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => $search_url,
			'query-input' => 'required name=search_term_string',
		),
	);

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array( $data, $website ),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'theme_skema_json_ld_organization', 99 );

/**
 * Asegura la URL del sitemap XML en robots.txt (WordPress 5.5+).
 *
 * @param string $output Contenido de robots.txt.
 * @param bool   $public Sitio público para buscadores.
 * @return string
 */
function theme_skema_robots_txt_sitemap( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$sitemap = home_url( '/wp-sitemap.xml' );
	// WordPress core ya suele añadir esta línea desde 5.5.
	if ( strpos( $output, 'wp-sitemap.xml' ) !== false ) {
		return $output;
	}
	return trim( (string) $output ) . "\n\nSitemap: " . esc_url( $sitemap ) . "\n";
}
add_filter( 'robots_txt', 'theme_skema_robots_txt_sitemap', 20, 2 );

/**
 * Enlace de descubrimiento del sitemap en el head (complemento a robots.txt).
 */
function theme_skema_head_sitemap_link() {
	if ( theme_skema_seo_plugin_active() ) {
		return;
	}
	if ( (string) get_option( 'blog_public' ) === '0' ) {
		return;
	}
	printf(
		'<link rel="sitemap" type="application/xml" title="%s" href="%s" />' . "\n",
		esc_attr( __( 'Índice del mapa del sitio', 'theme_skema' ) ),
		esc_url( home_url( '/wp-sitemap.xml' ) )
	);
}
add_action( 'wp_head', 'theme_skema_head_sitemap_link', 3 );
