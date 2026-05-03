<?php
/**
 * Landings: medios del proyecto (galerías por pestañas).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resuelve URL de imagen ACF (array o ID).
 *
 * @param mixed $acf_image Campo imagen ACF.
 * @param string $size Tamaño registrado en WordPress.
 * @return string URL o cadena vacía.
 */
function theme_skema_landing_medios_resolve_image_url( $acf_image, $size = 'large' ) {
	if ( is_array( $acf_image ) ) {
		if ( ! empty( $acf_image['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $acf_image['ID'], $size );
			return is_string( $url ) ? $url : '';
		}
		if ( ! empty( $acf_image['id'] ) ) {
			$url = wp_get_attachment_image_url( (int) $acf_image['id'], $size );
			return is_string( $url ) ? $url : '';
		}
		if ( ! empty( $acf_image['url'] ) && is_string( $acf_image['url'] ) ) {
			return esc_url_raw( $acf_image['url'] );
		}
	}

	if ( is_numeric( $acf_image ) && (int) $acf_image > 0 ) {
		$url = wp_get_attachment_image_url( (int) $acf_image, $size );
		return is_string( $url ) ? $url : '';
	}

	return '';
}

/**
 * Texto alternativo de un adjunto o cadena vacía.
 *
 * @param int $attachment_id ID del adjunto.
 * @return string
 */
function theme_skema_landing_medios_get_attachment_alt( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( $attachment_id <= 0 ) {
		return '';
	}

	$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

	return is_string( $alt ) ? trim( $alt ) : '';
}

/**
 * Diapositivas desde un repetidor ACF (imagen + leyenda opcional).
 *
 * @param int         $post_id ID landings.
 * @param string      $acf_field_name Nombre del campo repetidor (ej. skema_lland_medios_apto).
 * @param string|null $thumb_image_size Tamaño WP para miniatura (ej. medium); null = no clave thumb.
 * @return array<int, array{src: string, alt: string, caption: string, thumb?: string}>
 */
function theme_skema_landing_medios_get_slides_from_repeater( $post_id, $acf_field_name, $thumb_image_size = null ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	if ( ! is_string( $acf_field_name ) || $acf_field_name === '' ) {
		return array();
	}

	$rows = get_field( $acf_field_name, $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$slides = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$img = isset( $row['imagen'] ) ? $row['imagen'] : null;
		$url = theme_skema_landing_medios_resolve_image_url( $img, 'large' );
		if ( $url === '' ) {
			continue;
		}

		$leyenda = isset( $row['leyenda'] ) ? trim( (string) $row['leyenda'] ) : '';
		$att_id  = 0;
		if ( is_array( $img ) ) {
			if ( ! empty( $img['ID'] ) ) {
				$att_id = (int) $img['ID'];
			} elseif ( ! empty( $img['id'] ) ) {
				$att_id = (int) $img['id'];
			}
		} elseif ( is_numeric( $img ) ) {
			$att_id = (int) $img;
		}

		$alt_meta = $att_id > 0 ? theme_skema_landing_medios_get_attachment_alt( $att_id ) : '';
		$alt      = $leyenda !== '' ? $leyenda : $alt_meta;
		if ( $alt === '' ) {
			$alt = __( 'Imagen del proyecto', 'theme_skema' );
		}

		$caption = $leyenda !== '' ? $leyenda : $alt;

		$slide = array(
			'src'     => $url,
			'alt'     => $alt,
			'caption' => $caption,
		);

		if ( is_string( $thumb_image_size ) && $thumb_image_size !== '' ) {
			$thumb_url = theme_skema_landing_medios_resolve_image_url( $img, $thumb_image_size );
			$slide['thumb'] = ( $thumb_url !== '' ) ? $thumb_url : $url;
		}

		$slides[] = $slide;
	}

	return $slides;
}

/**
 * Diapositivas galería «Apto modelo».
 *
 * @param int $post_id ID landings.
 * @return array<int, array{src: string, alt: string, caption: string}>
 */
function theme_skema_landing_medios_get_apto_slides( $post_id ) {
	return theme_skema_landing_medios_get_slides_from_repeater( $post_id, 'skema_lland_medios_apto' );
}

/**
 * Diapositivas galería «Renders».
 *
 * @param int $post_id ID landings.
 * @return array<int, array{src: string, alt: string, caption: string}>
 */
function theme_skema_landing_medios_get_renders_slides( $post_id ) {
	return theme_skema_landing_medios_get_slides_from_repeater( $post_id, 'skema_lland_medios_renders' );
}

/**
 * Diapositivas pestaña «Plantas» (slider principal + miniaturas).
 *
 * @param int $post_id ID landings.
 * @return array<int, array{src: string, alt: string, caption: string, thumb?: string}>
 */
function theme_skema_landing_medios_get_plantas_slides( $post_id ) {
	return theme_skema_landing_medios_get_slides_from_repeater( $post_id, 'skema_lland_medios_plantas', 'medium' );
}

/**
 * URL segura de un MP4 desde campo archivo ACF (ID o array).
 *
 * @param mixed $acf_file Campo file ACF.
 * @return string URL o cadena vacía.
 */
function theme_skema_landing_medios_resolve_video_mp4_url( $acf_file ) {
	if ( is_array( $acf_file ) ) {
		$att_id = 0;
		if ( ! empty( $acf_file['ID'] ) ) {
			$att_id = (int) $acf_file['ID'];
		} elseif ( ! empty( $acf_file['id'] ) ) {
			$att_id = (int) $acf_file['id'];
		}

		if ( $att_id > 0 ) {
			$mime = get_post_mime_type( $att_id );
			$file_path = get_attached_file( $att_id );
			$ext        = is_string( $file_path ) ? strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) : '';
			$is_mp4     = ( $mime === 'video/mp4' ) || ( $ext === 'mp4' );
			if ( ! $is_mp4 ) {
				return '';
			}
			$url = wp_get_attachment_url( $att_id );
			return is_string( $url ) ? esc_url_raw( $url ) : '';
		}

		if ( ! empty( $acf_file['url'] ) && is_string( $acf_file['url'] ) ) {
			$path = wp_parse_url( $acf_file['url'], PHP_URL_PATH );
			$ext  = is_string( $path ) ? strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) : '';
			if ( $ext === 'mp4' ) {
				return esc_url_raw( $acf_file['url'] );
			}
		}

		return '';
	}

	if ( is_numeric( $acf_file ) && (int) $acf_file > 0 ) {
		$att_id = (int) $acf_file;
		$mime   = get_post_mime_type( $att_id );
		$file_path = get_attached_file( $att_id );
		$ext        = is_string( $file_path ) ? strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) ) : '';
		$is_mp4     = ( $mime === 'video/mp4' ) || ( $ext === 'mp4' );
		if ( ! $is_mp4 ) {
			return '';
		}
		$url = wp_get_attachment_url( $att_id );
		return is_string( $url ) ? esc_url_raw( $url ) : '';
	}

	return '';
}

/**
 * Datos del vídeo de la pestaña «Videos» (YouTube o MP4 de medios).
 *
 * @param int $post_id ID landings.
 * @return array{type: string, title: string, iframe_src?: string, youtube_id?: string, mp4_url?: string, poster_url?: string}|null
 */
function theme_skema_landing_medios_get_video_for_display( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$fuente = get_field( 'skema_lland_medios_video_fuente', $post_id );
	$fuente = is_string( $fuente ) ? $fuente : '';

	$titulo_raw = get_field( 'skema_lland_medios_video_titulo', $post_id );
	$titulo_opt = is_string( $titulo_raw ) ? trim( $titulo_raw ) : '';

	if ( $fuente === 'youtube' ) {
		if ( ! function_exists( 'theme_skema_youtube_id_from_url' ) ) {
			return null;
		}

		$url_raw = get_field( 'skema_lland_medios_video_youtube_url', $post_id );
		$url     = is_string( $url_raw ) ? trim( $url_raw ) : '';
		if ( $url === '' ) {
			return null;
		}

		$yt_id = theme_skema_youtube_id_from_url( $url );
		if ( $yt_id === '' ) {
			return null;
		}

		$title = $titulo_opt !== '' ? $titulo_opt : __( 'Video del proyecto en YouTube', 'theme_skema' );
		$iframe_src = sprintf(
			'https://www.youtube-nocookie.com/embed/%s?rel=0&modestbranding=1',
			rawurlencode( $yt_id )
		);

		return array(
			'type'       => 'youtube',
			'youtube_id' => $yt_id,
			'iframe_src' => $iframe_src,
			'title'      => $title,
		);
	}

	if ( $fuente === 'mp4' ) {
		$file = get_field( 'skema_lland_medios_video_mp4', $post_id );
		$mp4  = theme_skema_landing_medios_resolve_video_mp4_url( $file );
		if ( $mp4 === '' ) {
			return null;
		}

		$poster_raw = get_field( 'skema_lland_medios_video_poster', $post_id );
		$poster_url = '';
		if ( is_array( $poster_raw ) || is_numeric( $poster_raw ) ) {
			$poster_url = theme_skema_landing_medios_resolve_image_url( $poster_raw, 'large' );
		}

		$title = $titulo_opt !== '' ? $titulo_opt : __( 'Video del proyecto', 'theme_skema' );

		return array(
			'type'       => 'mp4',
			'mp4_url'    => $mp4,
			'poster_url' => $poster_url,
			'title'      => $title,
		);
	}

	return null;
}

/**
 * Ítems de la pestaña «Zonas sociales» (rejilla de imágenes estáticas + título corto).
 *
 * @param int $post_id ID landings.
 * @return array<int, array{src: string, alt: string, titulo: string}>
 */
function theme_skema_landing_medios_get_zonas_galeria_items( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_medios_zonas', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$items = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$img = isset( $row['imagen'] ) ? $row['imagen'] : null;
		$url = theme_skema_landing_medios_resolve_image_url( $img, 'large' );
		if ( $url === '' ) {
			continue;
		}

		$att_id = 0;
		if ( is_array( $img ) ) {
			if ( ! empty( $img['ID'] ) ) {
				$att_id = (int) $img['ID'];
			} elseif ( ! empty( $img['id'] ) ) {
				$att_id = (int) $img['id'];
			}
		} elseif ( is_numeric( $img ) ) {
			$att_id = (int) $img;
		}

		$alt_meta = $att_id > 0 ? theme_skema_landing_medios_get_attachment_alt( $att_id ) : '';
		$titulo   = isset( $row['titulo'] ) ? trim( (string) $row['titulo'] ) : '';
		$alt      = $titulo !== '' ? $titulo : $alt_meta;
		if ( $alt === '' ) {
			$alt = __( 'Zona social del proyecto', 'theme_skema' );
		}

		$items[] = array(
			'src'    => $url,
			'alt'    => $alt,
			'titulo' => $titulo,
		);
	}

	return $items;
}

/**
 * Nota opcional bajo la galería «Zonas sociales» (pestaña Medios).
 *
 * @param int $post_id ID landings.
 * @return string Texto plano seguro para esc_html o cadena vacía.
 */
function theme_skema_landing_medios_get_zonas_galeria_nota( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$raw = get_field( 'skema_lland_medios_zonas_nota', $post_id );
	if ( ! is_string( $raw ) ) {
		return '';
	}

	return trim( $raw );
}

/**
 * Si debe mostrarse el bloque «Medios del proyecto» (al menos una galería con datos).
 *
 * @param int $post_id ID landings.
 * @return bool
 */
function theme_skema_landing_medios_should_show_block( $post_id ) {
	if ( count( theme_skema_landing_medios_get_apto_slides( $post_id ) ) > 0 ) {
		return true;
	}

	if ( count( theme_skema_landing_medios_get_renders_slides( $post_id ) ) > 0 ) {
		return true;
	}

	if ( count( theme_skema_landing_medios_get_plantas_slides( $post_id ) ) > 0 ) {
		return true;
	}

	if ( null !== theme_skema_landing_medios_get_video_for_display( $post_id ) ) {
		return true;
	}

	return count( theme_skema_landing_medios_get_zonas_galeria_items( $post_id ) ) > 0;
}
