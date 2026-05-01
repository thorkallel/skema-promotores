<?php
/**
 * Utilidades para la plantilla de inicio (YouTube, tarjetas mixtas).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Indica si la vista actual usa la plantilla PHP theme_inicio.php.
 *
 * Algunos contextos (inicio estático, rutas de plantilla con subcarpeta) pueden hacer que
 * is_page_template() no coincida; el slug almacenado en la página es la fuente de verdad.
 *
 * @return bool
 */
function theme_skema_is_theme_inicio_template_active() {
	if ( is_page_template( 'theme_inicio.php' ) ) {
		return true;
	}

	$page_id = get_queried_object_id();
	if ( ! $page_id ) {
		return false;
	}

	if ( 'page' !== get_post_type( $page_id ) ) {
		return false;
	}

	$slug = get_page_template_slug( $page_id );
	if ( 'theme_inicio.php' === $slug ) {
		return true;
	}

	if ( $slug !== '' && preg_match( '/(^|\/)theme_inicio\.php$/', $slug ) ) {
		return true;
	}

	return false;
}

/**
 * Obtiene el ID de vídeo de YouTube a partir de una URL.
 *
 * @param string $url URL del vídeo o embed.
 * @return string ID de 11 caracteres o cadena vacía.
 */
function theme_skema_youtube_id_from_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return '';
	}
	if ( preg_match( '/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return $m[1];
	}
	if ( preg_match( '/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return $m[1];
	}
	if ( preg_match( '/\/embed\/([a-zA-Z0-9_-]{11})/', $url, $m ) ) {
		return $m[1];
	}
	return '';
}

/**
 * URL de poster para un ID de YouTube (o imagen ACF).
 *
 * @param string $video_id ID de vídeo.
 * @param string $acf_poster URL de imagen opcional desde ACF.
 * @return string URL de imagen.
 */
function theme_skema_youtube_poster_url( $video_id, $acf_poster = '' ) {
	if ( $acf_poster ) {
		return $acf_poster;
	}
	return $video_id ? 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/maxresdefault.jpg' : '';
}

/**
 * Imprime una tarjeta de proyecto/landing del bloque inicio (misma estructura que el tema).
 *
 * @param int    $post_id ID de entrada (proyectos o landings).
 * @param int    $cont    Índice visual (1-based).
 * @param int    $cuantos Total de tarjetas en el slider.
 */
function theme_skema_render_inicio_proyecto_card( $post_id, $cont, $cuantos ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! get_post( $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$title      = get_the_title( $post_id );
	$permalink  = get_permalink( $post_id );
	$excerpt    = get_the_excerpt( $post_id );

	if ( 'proyectos' === $post_type ) {
		$estado_terms = wp_get_post_terms( $post_id, 'estado_proyecto' );
		$tipo_terms   = wp_get_post_terms( $post_id, 'tipo_proyecto' );
		$ciudad_terms = wp_get_post_terms( $post_id, 'ciudad_proyecto' );
		$estado       = ( ! is_wp_error( $estado_terms ) && ! empty( $estado_terms ) ) ? $estado_terms[0]->name : __( 'No especificado', 'theme_skema' );
		$tipo_label   = ( ! is_wp_error( $tipo_terms ) && ! empty( $tipo_terms ) ) ? $tipo_terms[0]->name : __( 'No especificado', 'theme_skema' );
		$ciudad       = ( ! is_wp_error( $ciudad_terms ) && ! empty( $ciudad_terms ) ) ? $ciudad_terms[0]->name : __( 'No especificado', 'theme_skema' );
		$btn_text     = __( 'Ver proyecto', 'theme_skema' );
	} else {
		$estado     = '';
		$tipo_obj   = get_post_type_object( $post_type );
		$tipo_label = $tipo_obj ? $tipo_obj->labels->singular_name : __( 'Contenido', 'theme_skema' );
		$ciudad     = '';
		$btn_text   = __( 'Ver más', 'theme_skema' );
	}

	$thumb = get_the_post_thumbnail(
		$post_id,
		'full',
		array(
			'class' => 'img-desc-proyecto img-fluid w-100',
			'alt'   => esc_attr( $title ),
		)
	);

	$solo = ( 1 === (int) $cuantos ) ? 'solo-uno' : '';
	?>
<div
    class="col-sm-6 col-12 div-cont-pro cont-pro-<?php echo esc_attr( (string) $cont ); ?> <?php echo esc_attr( $solo ); ?> project-item">
    <?php if ( $estado ) : ?>
    <span class="txt-estado"><?php echo esc_html( $estado ); ?></span>
    <?php endif; ?>
    <?php
		if ( $thumb ) {
			echo $thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			$placeholder = get_template_directory_uri() . '/img/logo-skema.png';
			echo '<img src="' . esc_url( $placeholder ) . '" class="img-desc-proyecto img-fluid w-100" alt="' . esc_attr( $title ) . '" width="800" height="450" loading="lazy" />';
		}
		?>
    <span class="icono-mas">
        <?php echo file_get_contents( get_template_directory() . '/img/ico-mas.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents ?>
    </span>
    <div class="hover-infoproyecto px-sm-5 px-4 pt-sm-4 pb-sm-5">
        <div class="row">
            <div class="col-sm-7 col-12 py-3">
                <h2><?php echo esc_html( $tipo_label ); ?></h2>
                <h3><?php echo esc_html( $title ); ?></h3>
                <?php if ( $ciudad ) : ?>
                <h4><?php echo esc_html( $ciudad ); ?></h4>
                <?php endif; ?>
                <p><?php echo esc_html( wp_strip_all_tags( $excerpt ) ); ?></p>
            </div>
            <div class="col-sm-5 col-12 align-self-center py-sm-5 py-0">
                <a class="btn-verproyecto mt-sm-3 mb-sm-5 mb-3"
                    href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $btn_text ); ?></a>
                <img class="img-fluid w-75 mt-sm-4 mt-2 d-none d-sm-block rounded-0"
                    src="<?php echo esc_url( get_template_directory_uri() . '/img/logo-skema.png' ); ?>" alt=""
                    loading="lazy" width="200" height="60" />
            </div>
        </div>
    </div>
</div>
<?php
}

/**
 * Normaliza un valor ACF de imagen a URL.
 *
 * @param mixed $val Valor devuelto por ACF (ID, array o URL).
 * @return string URL o cadena vacía.
 */
function theme_skema_acf_value_to_image_url( $val ) {
	if ( empty( $val ) ) {
		return '';
	}
	if ( is_numeric( $val ) ) {
		$url = wp_get_attachment_image_url( (int) $val, 'medium' );
		return $url ? $url : '';
	}
	if ( is_array( $val ) ) {
		if ( ! empty( $val['sizes']['large'] ) ) {
			return (string) $val['sizes']['large'];
		}
		if ( ! empty( $val['url'] ) ) {
			return (string) $val['url'];
		}
		if ( ! empty( $val['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $val['ID'], 'large' );
			if ( ! $url ) {
				$url = wp_get_attachment_image_url( (int) $val['ID'], 'medium' );
			}
			return $url ? $url : '';
		}
	}
	if ( is_string( $val ) ) {
		return $val;
	}
	return '';
}

/**
 * Texto de precio "desde" para la ficha inicio (ACF `precio_desde`).
 *
 * @param int $post_id ID de entrada.
 * @return string Cadena recortada o vacío si no aplica.
 */
function theme_skema_inicio_renvia_precio_text( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return '';
	}
	foreach ( array( 'precio_desde', 'precio' ) as $pk ) {
		$pv = get_field( $pk, $post_id );
		if ( is_string( $pv ) && trim( $pv ) !== '' ) {
			return trim( $pv );
		}
		if ( is_numeric( $pv ) ) {
			return trim( (string) $pv );
		}
	}
	return '';
}

/**
 * Texto de tipología para la ficha (prioriza ACF `tipologia`).
 *
 * @param int $post_id ID de entrada.
 * @return string Cadena recortada o vacío.
 */
function theme_skema_inicio_renvia_tipologia_text( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return '';
	}
	foreach ( array( 'tipologia', 'ficha_tipologia', 'tipologia_listado', 'descripcion_ficha' ) as $key ) {
		$v = get_field( $key, $post_id );
		if ( is_string( $v ) && trim( $v ) !== '' ) {
			return trim( $v );
		}
	}
	return '';
}

/**
 * Si la entrada cumple requisitos del carrusel fichas (precio + tipología).
 *
 * @param int $post_id ID de entrada.
 * @return bool
 */
function theme_skema_inicio_renvia_incluir_en_slider( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}
	// Por defecto no se excluye: si todo queda vacío el bloque no se ve. Activar: add_filter( '...', '__return_true' ).
	$exigir = apply_filters( 'theme_skema_inicio_renvia_requiere_precio_tipologia', false, $post_id );
	if ( ! $exigir ) {
		return true;
	}
	return theme_skema_inicio_renvia_precio_text( $post_id ) !== ''
		&& theme_skema_inicio_renvia_tipologia_text( $post_id ) !== '';
}

/**
 * Tarjeta estilo Renvia (ficha) para el carrusel independiente del inicio.
 *
 * @param int $post_id ID de proyecto o landing.
 */
function theme_skema_render_inicio_renvia_ficha_card( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! get_post( $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$title     = get_the_title( $post_id );
	$permalink = get_permalink( $post_id );

	$img_url = theme_skema_acf_value_to_image_url( get_field( 'imagen_carrusel', $post_id ) );
	if ( ! $img_url ) {
		$img_url = get_the_post_thumbnail_url( $post_id, 'large' );
	}
	if ( ! $img_url ) {
		$img_url = get_template_directory_uri() . '/img/logo-skema.png';
	}

	$tipo_label = '';
	$ciudad     = '';

	if ( 'proyectos' === $post_type ) {
		$tipo_txt = get_field( 'tipo_proyecto_text', $post_id );
		if ( is_string( $tipo_txt ) && trim( $tipo_txt ) !== '' ) {
			$tipo_label = trim( $tipo_txt );
		} else {
			$tipo_terms = wp_get_post_terms( $post_id, 'tipo_proyecto' );
			$tipo_label = ( ! is_wp_error( $tipo_terms ) && ! empty( $tipo_terms ) ) ? $tipo_terms[0]->name : '';
		}
		$ciu_terms = wp_get_post_terms( $post_id, 'ciudad_proyecto' );
		$ciudad    = ( ! is_wp_error( $ciu_terms ) && ! empty( $ciu_terms ) ) ? $ciu_terms[0]->name : '';
		if ( $ciudad === '' ) {
			$ciudad_cf = get_field( 'ciudad_texto', $post_id );
			if ( is_string( $ciudad_cf ) && trim( $ciudad_cf ) !== '' ) {
				$ciudad = trim( $ciudad_cf );
			}
		}
	} else {
		$tipo_txt = get_field( 'tipo_proyecto_text', $post_id );
		if ( is_string( $tipo_txt ) && trim( $tipo_txt ) !== '' ) {
			$tipo_label = trim( $tipo_txt );
		} elseif (
			'landings' === $post_type
			&& function_exists( 'theme_skema_get_landing_phase' )
			&& 'prelanding' === theme_skema_get_landing_phase( $post_id )
		) {
			$tipo_label = __( 'Prelanding', 'theme_skema' );
		} else {
			$pto        = get_post_type_object( $post_type );
			$tipo_label = $pto ? $pto->labels->singular_name : '';
		}
		$ciudad_cf = get_field( 'ciudad_texto', $post_id );
		if ( is_string( $ciudad_cf ) && trim( $ciudad_cf ) !== '' ) {
			$ciudad = trim( $ciudad_cf );
		}
	}

	$logo_url = '';
	foreach ( array( 'logo_proyecto', 'logo_listado', 'logo_slider', 'logotipo' ) as $logo_key ) {
		$logo_url = theme_skema_acf_value_to_image_url( get_field( $logo_key, $post_id ) );
		if ( $logo_url ) {
			break;
		}
	}

	$area_priv  = get_field( 'area_priv', $post_id );
	$area_const = get_field( 'area_const', $post_id );
	$sup_strong = '';
	if ( $area_priv ) {
		$sup_strong = is_numeric( $area_priv ) ? $area_priv . ' m²' : (string) $area_priv;
	} elseif ( $area_const ) {
		$sup_strong = is_numeric( $area_const ) ? $area_const . ' m²' : (string) $area_const;
	}

	$tipologia_line = theme_skema_inicio_renvia_tipologia_text( $post_id );
	$precio         = theme_skema_inicio_renvia_precio_text( $post_id );

	$btn = ( 'proyectos' === $post_type )
		? __( 'Ver detalles', 'theme_skema' )
		: __( 'Ver más', 'theme_skema' );
	?>
<div class="renvia-service-card style-two service-ficha">
    <div class="content">
        <div class="service-ficha__figure">
            <img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy"
                width="640" height="360" />
            <div class="service-ficha__caption">
                <h4 class="service-ficha__nombre"><a
                        href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h4>
                <?php if ( $tipo_label ) : ?>
                <p class="service-ficha__tipo">
                    <strong><?php esc_html_e( 'Tipo de proyecto:', 'theme_skema' ); ?></strong>
                    <?php echo esc_html( $tipo_label ); ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ( $ciudad ) : ?>
        <p class="service-ficha__ciudad">
            <i class="bi bi-geo-alt" aria-hidden="true"></i><?php echo esc_html( $ciudad ); ?>
        </p>
        <?php endif; ?>
        <div class="row align-items-center service-ficha__specs">
            <div
                class="col-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start mb-3 mb-md-0">
                <div class="service-ficha__logo">
                    <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="" loading="lazy" />
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-center mb-3 mb-md-0">
                <div class="service-ficha__dims w-100">
                    <i class="bi bi-rulers service-ficha__spec-icon" aria-hidden="true"></i><br>
                    <div class="service-ficha__spec-copy">
                        <span class="service-ficha__label"><?php esc_html_e( 'Superficie', 'theme_skema' ); ?></span>
                        <span
                            class="service-ficha__meta"><?php echo esc_html( $tipologia_line !== '' ? $tipologia_line : '—' ); ?></span>
                    </div>
                    <?php if ( $sup_strong ) : ?>
                    <strong class="service-ficha__sup-m2"
                        title="<?php esc_attr_e( 'Detalle en m²', 'theme_skema' ); ?>"><?php echo esc_html( $sup_strong ); ?></strong>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-center">
                <div class="service-ficha__precio w-100">
                    <i class="bi bi-currency-dollar service-ficha__spec-icon" aria-hidden="true"></i>
                    <div class="service-ficha__spec-copy">
                        <span class="service-ficha__label"><?php esc_html_e( 'Desde', 'theme_skema' ); ?></span>
                    </div>
                    <span class="service-ficha__amount"><?php echo esc_html( $precio !== '' ? $precio : '—' ); ?></span>
                </div>
            </div>
        </div>
        <a href="<?php echo esc_url( $permalink ); ?>" class="read-more style-two">
            <span class="icon" aria-hidden="true"><i class="bi bi-arrow-right"></i></span>
            <?php echo esc_html( $btn ); ?>
        </a>
    </div>
</div>
<?php
}