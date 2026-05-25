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
 * Normaliza fecha ACF del repetidor «Avance de obra» para mostrar y ordenar.
 *
 * @param mixed $raw Valor del campo fecha (típ. Y-m-d desde ACF).
 * @return array{iso: string, label: string} iso en Y-m-d o cadenas vacías si no aplica.
 */
function theme_skema_landing_medios_parse_avance_fecha_row( $raw ) {
	$out = array(
		'iso'   => '',
		'label' => '',
	);

	if ( ! is_string( $raw ) ) {
		return $out;
	}

	$s = trim( $raw );
	if ( $s === '' || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $s ) ) {
		return $out;
	}

	if ( ! function_exists( 'wp_timezone' ) ) {
		return $out;
	}

	try {
		$d = new \DateTimeImmutable( $s . ' 12:00:00', wp_timezone() );
	} catch ( \Exception $e ) {
		return $out;
	}

	$ts = $d->getTimestamp();
	if ( $ts <= 0 ) {
		return $out;
	}

	if ( function_exists( 'wp_date' ) ) {
		$out['iso']   = wp_date( 'Y-m-d', $ts );
		$out['label'] = wp_date( get_option( 'date_format' ), $ts );
	} else {
		$out['iso']   = date_i18n( 'Y-m-d', $ts );
		$out['label'] = date_i18n( get_option( 'date_format' ), $ts );
	}

	return $out;
}

/**
 * Slides pestaña «Avance de obra» (imagen, vídeo MP4 o YouTube por fila; fecha opcional para histórico).
 *
 * @param int $post_id ID landings.
 * @return array<int, array<string, string>>
 */
function theme_skema_landing_medios_get_avance_obra_slides( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_medios_avance', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$slides = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$tipo = isset( $row['tipo'] ) ? (string) $row['tipo'] : '';
		if ( $tipo !== 'imagen' && $tipo !== 'video' && $tipo !== 'youtube' ) {
			$tipo = 'imagen';
		}

		$leyenda = isset( $row['leyenda'] ) ? trim( (string) $row['leyenda'] ) : '';
		$fecha_raw = isset( $row['fecha'] ) ? $row['fecha'] : '';
		$fecha_raw = is_string( $fecha_raw ) ? $fecha_raw : '';
		$fecha_info = theme_skema_landing_medios_parse_avance_fecha_row( $fecha_raw );

		if ( $tipo === 'imagen' ) {
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
			$alt      = $leyenda !== '' ? $leyenda : $alt_meta;
			if ( $alt === '' ) {
				$alt = __( 'Avance de obra del proyecto', 'theme_skema' );
			}

			$caption = $leyenda !== '' ? $leyenda : $alt;
			$thumb   = theme_skema_landing_medios_resolve_image_url( $img, 'medium' );

			$slides[] = array(
				'media'       => 'image',
				'src'         => $url,
				'iframe_src'  => '',
				'youtube_id'  => '',
				'poster'      => '',
				'alt'         => $alt,
				'caption'     => $caption,
				'leyenda'     => $leyenda,
				'thumb'       => ( $thumb !== '' ) ? $thumb : $url,
				'fecha_iso'   => $fecha_info['iso'],
				'fecha_texto' => $fecha_info['label'],
			);
			continue;
		}

		if ( $tipo === 'youtube' ) {
			if ( ! function_exists( 'theme_skema_youtube_id_from_url' ) ) {
				continue;
			}

			$url_raw = isset( $row['youtube_url'] ) ? $row['youtube_url'] : '';
			$url     = is_string( $url_raw ) ? trim( $url_raw ) : '';
			if ( $url === '' ) {
				continue;
			}

			$yt_id = theme_skema_youtube_id_from_url( $url );
			if ( $yt_id === '' ) {
				continue;
			}

			$iframe_src = sprintf(
				'https://www.youtube-nocookie.com/embed/%s?rel=0&modestbranding=1',
				rawurlencode( $yt_id )
			);

			$thumb_yt = sprintf(
				'https://i.ytimg.com/vi/%s/mqdefault.jpg',
				rawurlencode( $yt_id )
			);

			$alt_yt = $leyenda !== '' ? $leyenda : __( 'Vídeo de avance de obra en YouTube', 'theme_skema' );

			$slides[] = array(
				'media'       => 'youtube',
				'src'         => '',
				'iframe_src'  => $iframe_src,
				'youtube_id'  => $yt_id,
				'poster'      => '',
				'alt'         => $alt_yt,
				'caption'     => $alt_yt,
				'leyenda'     => $leyenda,
				'thumb'       => $thumb_yt,
				'fecha_iso'   => $fecha_info['iso'],
				'fecha_texto' => $fecha_info['label'],
			);
			continue;
		}

		$file = isset( $row['video'] ) ? $row['video'] : null;
		$mp4  = theme_skema_landing_medios_resolve_video_mp4_url( $file );
		if ( $mp4 === '' ) {
			continue;
		}

		$poster_raw = isset( $row['poster'] ) ? $row['poster'] : null;
		$poster_lg  = theme_skema_landing_medios_resolve_image_url( $poster_raw, 'large' );
		$poster_md  = theme_skema_landing_medios_resolve_image_url( $poster_raw, 'medium' );

		$alt_video = $leyenda !== '' ? $leyenda : __( 'Vídeo de avance de obra', 'theme_skema' );

		$slides[] = array(
			'media'       => 'video',
			'src'         => $mp4,
			'iframe_src'  => '',
			'youtube_id'  => '',
			'poster'      => $poster_lg,
			'alt'         => $alt_video,
			'caption'     => $alt_video,
			'leyenda'     => $leyenda,
			'thumb'       => ( $poster_md !== '' ) ? $poster_md : '',
			'fecha_iso'   => $fecha_info['iso'],
			'fecha_texto' => $fecha_info['label'],
		);
	}

	$dated   = array();
	$undated = array();
	foreach ( $slides as $slide ) {
		if ( ! is_array( $slide ) ) {
			continue;
		}
		$iso = isset( $slide['fecha_iso'] ) ? (string) $slide['fecha_iso'] : '';
		if ( $iso !== '' ) {
			$dated[] = $slide;
			continue;
		}
		$undated[] = $slide;
	}

	usort(
		$dated,
		static function ( $a, $b ) {
			$ia = isset( $a['fecha_iso'] ) ? (string) $a['fecha_iso'] : '';
			$ib = isset( $b['fecha_iso'] ) ? (string) $b['fecha_iso'] : '';
			return strcmp( $ia, $ib );
		}
	);

	return array_merge( $dated, $undated );
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
 * Datos para iframe «Maqueta web» (recorrido virtual u otro embed por URL).
 *
 * @param int $post_id ID landings.
 * @return array{iframe_src: string, title: string}|null
 */
function theme_skema_landing_medios_get_maqueta_web_for_display( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return null;
	}

	$url_raw = get_field( 'skema_lland_medios_maqueta_url', $post_id );
	$url     = is_string( $url_raw ) ? trim( $url_raw ) : '';
	if ( $url === '' ) {
		return null;
	}

	if ( ! function_exists( 'wp_http_validate_url' ) ) {
		return null;
	}

	$validated = wp_http_validate_url( $url );
	if ( ! is_string( $validated ) || $validated === '' ) {
		return null;
	}

	$parsed = wp_parse_url( $validated );
	$scheme = isset( $parsed['scheme'] ) ? strtolower( (string) $parsed['scheme'] ) : '';
	if ( $scheme !== 'http' && $scheme !== 'https' ) {
		return null;
	}

	$titulo_raw = get_field( 'skema_lland_medios_maqueta_titulo', $post_id );
	$titulo_opt = is_string( $titulo_raw ) ? trim( $titulo_raw ) : '';
	$title      = $titulo_opt !== '' ? $titulo_opt : __( 'Recorrido virtual del proyecto', 'theme_skema' );

	return array(
		'iframe_src' => $validated,
		'title'      => $title,
	);
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

	if ( count( theme_skema_landing_medios_get_avance_obra_slides( $post_id ) ) > 0 ) {
		return true;
	}

	if ( null !== theme_skema_landing_medios_get_video_for_display( $post_id ) ) {
		return true;
	}

	if ( null !== theme_skema_landing_medios_get_maqueta_web_for_display( $post_id ) ) {
		return true;
	}

	return count( theme_skema_landing_medios_get_zonas_galeria_items( $post_id ) ) > 0;
}
