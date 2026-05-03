<?php
/**
 * Landings: carrusel «Con el respaldo de» (logos + texto corto encima).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ítems del carrusel de empresas patrocinadoras / respaldo.
 *
 * @param int $post_id ID landings.
 * @return array<int, array{texto: string, src: string, alt: string}>
 */
function theme_skema_landing_get_respaldo_slider_items( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	if ( ! function_exists( 'theme_skema_landing_medios_resolve_image_url' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_respaldo_slider', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$items = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$texto = isset( $row['texto'] ) ? trim( (string) $row['texto'] ) : '';
		if ( $texto === '' ) {
			continue;
		}

		$logo = isset( $row['logo'] ) ? $row['logo'] : null;
		$url  = theme_skema_landing_medios_resolve_image_url( $logo, 'medium' );
		if ( $url === '' ) {
			continue;
		}

		$att_id = 0;
		if ( is_array( $logo ) ) {
			if ( ! empty( $logo['ID'] ) ) {
				$att_id = (int) $logo['ID'];
			} elseif ( ! empty( $logo['id'] ) ) {
				$att_id = (int) $logo['id'];
			}
		} elseif ( is_numeric( $logo ) ) {
			$att_id = (int) $logo;
		}

		$alt_meta = '';
		if ( function_exists( 'theme_skema_landing_medios_get_attachment_alt' ) && $att_id > 0 ) {
			$alt_meta = theme_skema_landing_medios_get_attachment_alt( $att_id );
		}
		$alt = $alt_meta !== '' ? $alt_meta : sprintf(
			/* translators: %s: short label above the logo (e.g. company role). */
			__( 'Logo — %s', 'theme_skema' ),
			$texto
		);

		$items[] = array(
			'texto' => $texto,
			'src'   => $url,
			'alt'   => $alt,
		);
	}

	return $items;
}

/**
 * Datos del botón de descarga del brochure (solo PDF de medios).
 *
 * @param int $post_id ID landings.
 * @return array{url: string, label: string, download: string}|null
 */
function theme_skema_landing_get_brochure_cta_data( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$file = get_field( 'skema_lland_brochure_archivo', $post_id );
	$att_id = 0;
	if ( is_array( $file ) ) {
		if ( ! empty( $file['ID'] ) ) {
			$att_id = (int) $file['ID'];
		} elseif ( ! empty( $file['id'] ) ) {
			$att_id = (int) $file['id'];
		}
	} elseif ( is_numeric( $file ) ) {
		$att_id = (int) $file;
	}

	if ( $att_id <= 0 ) {
		return null;
	}

	$mime      = get_post_mime_type( $att_id );
	$file_path = get_attached_file( $att_id );
	$ext       = is_string( $file_path ) ? strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) : '';
	$is_pdf    = ( $mime === 'application/pdf' ) || ( $ext === 'pdf' );
	if ( ! $is_pdf ) {
		return null;
	}

	$url = wp_get_attachment_url( $att_id );
	if ( ! is_string( $url ) || $url === '' ) {
		return null;
	}

	$label = __( 'Descargar brochure', 'theme_skema' );
	$label_raw = get_field( 'skema_lland_brochure_etiqueta', $post_id );
	if ( is_string( $label_raw ) ) {
		$t = trim( $label_raw );
		if ( $t !== '' ) {
			$label = $t;
		}
	}

	$download = '';
	$fn_raw   = get_field( 'skema_lland_brochure_nombre_descarga', $post_id );
	if ( is_string( $fn_raw ) ) {
		$t = trim( $fn_raw );
		if ( $t !== '' ) {
			$san = sanitize_file_name( $t );
			if ( $san !== '' ) {
				$download = preg_match( '/\.pdf$/i', $san ) ? $san : $san . '.pdf';
			}
		}
	}

	if ( $download === '' && is_string( $file_path ) && $file_path !== '' ) {
		$download = basename( $file_path );
	}

	if ( $download === '' ) {
		$download = 'brochure.pdf';
	}

	return array(
		'url'      => $url,
		'label'    => $label,
		'download' => $download,
	);
}
