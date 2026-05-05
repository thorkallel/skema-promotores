<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package skema
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function theme_skema_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'theme_skema_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function theme_skema_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'theme_skema_pingback_header' );

/**
 * Garantiza título accesible en iframes embebidos desde campos WYSIWYG/ACF.
 *
 * @param mixed  $raw_html       HTML crudo del embed.
 * @param string $fallback_title Texto de respaldo para atributo title.
 * @return string
 */
function theme_skema_accessible_embed_html( $raw_html, $fallback_title = '' ) {
	if ( ! is_string( $raw_html ) ) {
		return '';
	}

	$html = trim( $raw_html );
	if ( '' === $html ) {
		return '';
	}

	$fallback_title = trim( wp_strip_all_tags( (string) $fallback_title ) );
	if ( '' === $fallback_title ) {
		$fallback_title = __( 'Contenido embebido', 'theme_skema' );
	}

	$replace_callback = static function ( $matches ) use ( $fallback_title ) {
		$iframe_open = $matches[0];

		if ( preg_match( '/\btitle\s*=\s*("|\').*?\1/i', $iframe_open ) ) {
			return $iframe_open;
		}

		return rtrim( $iframe_open, '>' ) . ' title="' . esc_attr( $fallback_title ) . '">';
	};

	$updated = preg_replace_callback( '/<iframe\b[^>]*>/i', $replace_callback, $html );

	return is_string( $updated ) ? $updated : $html;
}
