<?php
/**
 * Landings: render de cabecera slider (misma base que theme_inicio / Slick), hero prelanding y logo de proyecto.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * URLs de imágenes desde un repetidor ACF (subcampo `img`).
 *
 * @param int    $post_id ID de la landing.
 * @param string $repeater_name Nombre del repetidor.
 * @return array<int, string>
 */
function theme_skema_landing_hero_image_urls_from_repeater( $post_id, $repeater_name ) {
	$post_id = absint( $post_id );
	$out     = array();
	if ( ! $post_id || ! function_exists( 'have_rows' ) || ! have_rows( $repeater_name, $post_id ) ) {
		return $out;
	}
	while ( have_rows( $repeater_name, $post_id ) ) {
		the_row();
		$url = theme_skema_acf_value_to_image_url( get_sub_field( 'img' ) );
		if ( $url !== '' ) {
			$out[] = $url;
		}
	}
	return $out;
}

/**
 * IDs de YouTube desde repetidor (subcampo `youtube_url`).
 *
 * @param int    $post_id ID de la landing.
 * @param string $repeater_name Nombre del repetidor.
 * @return array<int, string>
 */
function theme_skema_landing_hero_youtube_ids_from_repeater( $post_id, $repeater_name ) {
	$post_id = absint( $post_id );
	$out     = array();
	if ( ! $post_id || ! function_exists( 'have_rows' ) || ! function_exists( 'theme_skema_youtube_id_from_url' ) ) {
		return $out;
	}
	if ( ! have_rows( $repeater_name, $post_id ) ) {
		return $out;
	}
	while ( have_rows( $repeater_name, $post_id ) ) {
		the_row();
		$url = get_sub_field( 'youtube_url' );
		$url = is_string( $url ) ? trim( $url ) : '';
		if ( $url === '' ) {
			continue;
		}
		$id = theme_skema_youtube_id_from_url( $url );
		if ( $id !== '' ) {
			$out[] = $id;
		}
	}
	return $out;
}

/**
 * Solo permite shortcodes de Contact Form 7 pegados desde el administrador.
 *
 * @param mixed $raw Valor del campo ACF.
 * @return string Cadena vacía si no es válido.
 */
function theme_skema_sanitize_cf7_shortcode( $raw ) {
	if ( ! is_string( $raw ) ) {
		return '';
	}
	$raw = trim( $raw );
	if ( $raw === '' ) {
		return '';
	}
	if ( strlen( $raw ) > 600 ) {
		return '';
	}
	if ( ! preg_match( '/^\[\s*contact-form-7\b/i', $raw ) ) {
		return '';
	}
	return $raw;
}

/**
 * Comprueba si un WYSIWYG ACF (valor en bruto, format_value false) tiene texto visible.
 *
 * @param mixed $raw Valor del campo.
 * @return bool
 */
function theme_skema_landing_acf_wysiwyg_has_content( $raw ) {
	if ( ! is_string( $raw ) ) {
		return false;
	}

	return trim( wp_strip_all_tags( $raw ) ) !== '';
}

/**
 * ID seguro para el contenedor del formulario (ancla).
 *
 * @param mixed $raw Valor del campo ACF.
 * @return string
 */
function theme_skema_sanitize_prelanding_lead_dom_id( $raw ) {
	$fallback = 'solicitar-informacion';
	if ( ! is_string( $raw ) ) {
		return $fallback;
	}
	$clean = strtolower( preg_replace( '/[^a-z0-9\-_]/', '', $raw ) );
	if ( $clean === '' ) {
		return $fallback;
	}
	return $clean;
}

/**
 * Capa de medios del hero prelanding (slider Slick, YouTube o fondo sólido).
 *
 * @param int    $post_id ID de la landing.
 * @param string $tipo Valor de skema_lpre_hero_tipo.
 * @param string $landing_title Título para textos alternativos.
 */
function theme_skema_echo_prelanding_hero_media( $post_id, $tipo, $landing_title ) {
	$rep_desk    = 'skema_lpre_slider_img';
	$rep_movil   = 'skema_lpre_slider_img_movil';
	$rep_youtube = 'skema_lpre_slider_youtube';

	if ( 'images' === $tipo ) {
		$desk_urls = theme_skema_landing_hero_image_urls_from_repeater( $post_id, $rep_desk );
		$mob_urls  = theme_skema_landing_hero_image_urls_from_repeater( $post_id, $rep_movil );
		if ( empty( $mob_urls ) ) {
			$mob_urls = $desk_urls;
		}
		if ( empty( $desk_urls ) && empty( $mob_urls ) ) {
			theme_skema_echo_prelanding_hero_media_none();
			return;
		}
		echo '<div class="landing-hero">';
		if ( ! empty( $desk_urls ) ) {
			echo '<div class="slider-banner d-none d-sm-block">';
			echo '<div class="slick-slider-banner">';
			$slide_i = 1;
			foreach ( $desk_urls as $img_url ) {
				$slide_alt = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera %1$d — %2$s', 'theme_skema' ),
					$slide_i,
					$landing_title
				);
				echo '<div class="prelanding-slide">';
				printf(
					'<img src="%s" class="img-fluid w-100" alt="%s" loading="%s" decoding="async" />',
					esc_url( $img_url ),
					esc_attr( $slide_alt ),
					esc_attr( 1 === $slide_i ? 'eager' : 'lazy' )
				);
				echo '</div>';
				++$slide_i;
			}
			echo '</div>';
			echo '<div class="home-hero-dots"></div>';
			echo '</div>';
		}
		if ( ! empty( $mob_urls ) ) {
			echo '<div class="slider-banner d-sm-none d-block">';
			echo '<div class="slick-slider-banner">';
			$slide_m = 1;
			foreach ( $mob_urls as $img_url ) {
				$slide_alt_m = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera móvil %1$d — %2$s', 'theme_skema' ),
					$slide_m,
					$landing_title
				);
				echo '<div class="prelanding-slide">';
				printf(
					'<img src="%s" class="img-fluid w-100" alt="%s" loading="%s" decoding="async" />',
					esc_url( $img_url ),
					esc_attr( $slide_alt_m ),
					esc_attr( 1 === $slide_m ? 'eager' : 'lazy' )
				);
				echo '</div>';
				++$slide_m;
			}
			echo '</div>';
			echo '<div class="home-hero-dots"></div>';
			echo '</div>';
		}
		echo '</div>';
		return;
	}

	if ( 'youtube' === $tipo ) {
		$slides_yt = theme_skema_landing_hero_youtube_ids_from_repeater( $post_id, $rep_youtube );
		if ( empty( $slides_yt ) ) {
			theme_skema_echo_prelanding_hero_media_none();
			return;
		}
		echo '<div class="landing-hero">';
		echo '<div class="slider-banner slider-banner--youtube">';
		echo '<div class="slick-slider-banner slick-slider-banner--youtube">';
		foreach ( $slides_yt as $idx => $slide_yt_id ) {
			$autoplay  = 0 === $idx ? '1' : '0';
			$embed_src = sprintf(
				'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&enablejsapi=1&autoplay=%s',
				rawurlencode( $slide_yt_id ),
				$autoplay
			);
			$slide_title = sprintf(
				/* translators: %d: slide number */
				__( 'Cabecera vídeo %d', 'theme_skema' ),
				$idx + 1
			);
			echo '<div class="hero-yt-slide prelanding-slide" data-slide-index="' . esc_attr( (string) $idx ) . '" data-youtube-id="' . esc_attr( $slide_yt_id ) . '">';
			echo '<div class="hero-yt-embed">';
			printf(
				'<iframe class="hero-yt-iframe" src="%s" title="%s" width="560" height="315" tabindex="-1" aria-hidden="true" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="%s"></iframe>',
				esc_url( $embed_src ),
				esc_attr( $slide_title ),
				esc_attr( 0 === $idx ? 'eager' : 'lazy' )
			);
			echo '</div></div>';
		}
		echo '</div>';
		echo '<div class="home-hero-dots"></div>';
		echo '</div></div>';
		return;
	}

	theme_skema_echo_prelanding_hero_media_none();
}

/**
 * Fondo de respaldo cuando no hay slider configurado.
 */
function theme_skema_echo_prelanding_hero_media_none() {
	echo '<div class="landing-hero landing-hero--prelanding-empty" aria-hidden="true">';
	echo '<div class="prelanding-hero__solid-bg"></div>';
	echo '</div>';
}

/**
 * Columnas de copy + caja lead (Contact Form 7).
 *
 * @param int $post_id ID de la landing.
 */
function theme_skema_echo_prelanding_hero_copy_and_lead( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$tagline   = trim( (string) get_field( 'skema_lpre_hero_tagline', $post_id ) );
	$heading   = trim( (string) get_field( 'skema_lpre_hero_heading', $post_id ) );
	$subtitle  = trim( (string) get_field( 'skema_lpre_hero_subtitle', $post_id ) );
	$location  = trim( (string) get_field( 'skema_lpre_hero_location', $post_id ) );
	$intro     = theme_skema_get_landing_shared_intro_raw( $post_id );
	$lead_t    = trim( (string) get_field( 'skema_lpre_lead_title', $post_id ) );
	$lead_txt  = trim( (string) get_field( 'skema_lpre_lead_text', $post_id ) );
	$cf7_raw   = get_field( 'skema_lpre_lead_cf7', $post_id );
	$cf7       = theme_skema_sanitize_cf7_shortcode( is_string( $cf7_raw ) ? $cf7_raw : '' );
	$anchor_id = theme_skema_sanitize_prelanding_lead_dom_id( get_field( 'skema_lpre_lead_anchor_id', $post_id ) );

	$title_fallback = get_the_title( $post_id );
	$h1             = $heading !== '' ? $heading : $title_fallback;

	$logo_url = '';
	if ( function_exists( 'theme_skema_acf_value_to_image_url' ) ) {
		$logo_url = theme_skema_acf_value_to_image_url( get_field( 'skema_lpre_project_logo', $post_id ) );
	}

	$points = array();
	if ( function_exists( 'have_rows' ) && have_rows( 'skema_lpre_hero_points', $post_id ) ) {
		while ( have_rows( 'skema_lpre_hero_points', $post_id ) ) {
			the_row();
			$pt = get_sub_field( 'point_text' );
			$pt = is_string( $pt ) ? trim( $pt ) : '';
			if ( $pt !== '' ) {
				$points[] = $pt;
			}
		}
	}

	$show_lead_block = ( $cf7 !== '' || $lead_t !== '' || $lead_txt !== '' );
	$lead_heading    = $lead_t;
	if ( $lead_heading === '' && $cf7 !== '' ) {
		$lead_heading = __( 'Solicita información', 'theme_skema' );
	}

	?>
<div class="prelanding-content-wrap">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-xl-7 col-lg-7">
                <div class="prelanding-copy">
                    <?php if ( $tagline !== '' ) : ?>
                    <span class="tag-line"><?php echo esc_html( $tagline ); ?></span>
                    <?php endif; ?>
                    <?php if ( $logo_url !== '' || $subtitle !== '' || $location !== '' ) : ?>
                    <div class="project-hero-branding">
                        <?php if ( $logo_url !== '' ) : ?>
                        <div class="project-hero-logo">
                            <img src="<?php echo esc_url( $logo_url ); ?>"
                                alt=""
                                loading="lazy" decoding="async" />
                        </div>
                        <?php endif; ?>
                        <div class="project-hero-text">
                            <h1><?php echo esc_html( $h1 ); ?></h1>
                            <?php if ( $subtitle !== '' ) : ?>
                            <p><?php echo esc_html( $subtitle ); ?></p>
                            <?php endif; ?>
                            <?php if ( $location !== '' ) : ?>
                            <p class="prelanding-copy__location">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                <?php echo esc_html( $location ); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="project-hero-text project-hero-text--solo">
                        <h1><?php echo esc_html( $h1 ); ?></h1>
                    </div>
                    <?php endif; ?>
                    <?php if ( $intro !== '' ) : ?>
                    <div class="prelanding-copy__intro entry-content"><?php echo apply_filters( 'the_content', $intro ); ?></div>
                    <?php endif; ?>
                    <?php if ( ! empty( $points ) ) : ?>
                    <ul class="prelanding-points">
                        <?php foreach ( $points as $point ) : ?>
                        <li>
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                            <span><?php echo esc_html( $point ); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ( $show_lead_block ) : ?>
            <div class="col-xl-5 col-lg-5 ms-xl-auto">
                <div class="project-info-box project-info-box--lead" id="<?php echo esc_attr( $anchor_id ); ?>">
                    <?php if ( $lead_heading !== '' ) : ?>
                    <h2 class="h5 project-info-box__heading"><?php echo esc_html( $lead_heading ); ?></h2>
                    <?php endif; ?>
                    <?php if ( $lead_txt !== '' ) : ?>
                    <p class="small text-muted mb-3 prelanding-lead-intro"><?php echo esc_html( $lead_txt ); ?></p>
                    <?php endif; ?>
                    <?php
						if ( $cf7 !== '' ) {
							echo '<div class="prelanding-cf7-wrap">';
							echo do_shortcode( $cf7 );
							echo '</div>';
						}
						?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
}

/**
 * Hero prelanding: slider a pantalla completa + overlay + copy + formulario CF7.
 *
 * @param int $post_id ID de la landing.
 */
function theme_skema_render_prelanding_hero_section( $post_id ) {
	$tipo_key = 'skema_lpre_hero_tipo';
	$tipo     = get_field( $tipo_key, $post_id );
	$tipo     = is_string( $tipo ) ? $tipo : 'none';

	$landing_title = get_the_title( $post_id );

	echo '<section class="prelanding-hero">';
	theme_skema_echo_prelanding_hero_media( $post_id, $tipo, $landing_title );
	echo '<div class="shape-one" aria-hidden="true"><span></span></div>';
	/* echo '<div class="prelanding-overlay" aria-hidden="true"></div>'; */
	theme_skema_echo_prelanding_hero_copy_and_lead( $post_id );
	echo '</section>';
}

/**
 * Comprueba si la landing completa tiene slider de cabecera usable (imágenes o YouTube).
 *
 * @param int    $post_id ID de la landing.
 * @param string $tipo Valor de skema_lland_hero_tipo.
 */
function theme_skema_landing_full_has_configured_hero_media( $post_id, $tipo ) {
	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return false;
	}

	if ( 'images' === $tipo ) {
		$desk = theme_skema_landing_hero_image_urls_from_repeater( $post_id, 'skema_lland_slider_img' );
		$mob  = theme_skema_landing_hero_image_urls_from_repeater( $post_id, 'skema_lland_slider_img_movil' );
		return ! empty( $desk ) || ! empty( $mob );
	}

	if ( 'youtube' === $tipo ) {
		$ids = theme_skema_landing_hero_youtube_ids_from_repeater( $post_id, 'skema_lland_slider_youtube' );
		return ! empty( $ids );
	}

	return false;
}

/**
 * Logo bajo cabecera solo cuando no hay hero slider en landing completa.
 *
 * @param int|null $post_id ID o null para la consulta actual.
 */
function theme_skema_should_render_landing_standalone_project_logo( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'theme_skema_get_landing_phase' ) ) {
		return false;
	}

	if ( null === $post_id ) {
		$post_id = function_exists( 'theme_skema_viewing_landing_id' )
			? theme_skema_viewing_landing_id()
			: get_queried_object_id();
	}
	$post_id = absint( $post_id );
	if ( ! $post_id || 'landings' !== get_post_type( $post_id ) ) {
		return false;
	}

	if ( 'landing' !== theme_skema_get_landing_phase( $post_id ) ) {
		return false;
	}

	$tipo = get_field( 'skema_lland_hero_tipo', $post_id );
	$tipo = is_string( $tipo ) ? $tipo : 'none';

	return ! theme_skema_landing_full_has_configured_hero_media( $post_id, $tipo );
}

/**
 * Datos de copy del hero landing completa (ACF pestaña Landing completa).
 *
 * @param int $post_id ID de la landing.
 * @return array{tagline: string, h1: string, location: string, logo_url: string}
 */
function theme_skema_get_landing_full_hero_copy_bundle( $post_id ) {
	$post_id = absint( $post_id );
	$bundle  = array(
		'tagline'   => '',
		'h1'        => '',
		'location'  => '',
		'logo_url'  => '',
	);

	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $bundle;
	}

	$bundle['tagline']  = trim( (string) get_field( 'skema_lland_hero_tagline', $post_id ) );
	$heading            = trim( (string) get_field( 'skema_lland_hero_heading', $post_id ) );
	$bundle['location'] = trim( (string) get_field( 'skema_lland_hero_location', $post_id ) );
	$title_fallback     = get_the_title( $post_id );
	$bundle['h1']       = $heading !== '' ? $heading : $title_fallback;

	if ( function_exists( 'theme_skema_acf_value_to_image_url' ) ) {
		$bundle['logo_url'] = theme_skema_acf_value_to_image_url( get_field( 'skema_lland_project_logo', $post_id ) );
	}

	return $bundle;
}

/**
 * Diapositivas del hero landing completa con imagen + textos opcionales por fila del repetidor.
 *
 * @param int    $post_id ID de la landing.
 * @param string $repeater_name Nombre del repetidor ACF (p. ej. skema_lland_slider_img).
 * @return array<int, array{url: string, slide_tagline: string, slide_heading: string, slide_location: string}>
 */
function theme_skema_landing_full_hero_slides_from_image_repeater( $post_id, $repeater_name ) {
	$post_id = absint( $post_id );
	$out     = array();
	if ( ! $post_id || ! function_exists( 'have_rows' ) || ! have_rows( $repeater_name, $post_id ) ) {
		return $out;
	}
	while ( have_rows( $repeater_name, $post_id ) ) {
		the_row();
		$url = '';
		if ( function_exists( 'theme_skema_acf_value_to_image_url' ) ) {
			$url = theme_skema_acf_value_to_image_url( get_sub_field( 'img' ) );
		}
		if ( $url === '' ) {
			continue;
		}
		$out[] = array(
			'url'            => $url,
			'slide_tagline'  => trim( (string) get_sub_field( 'slide_tagline' ) ),
			'slide_heading'  => trim( (string) get_sub_field( 'slide_heading' ) ),
			'slide_location' => trim( (string) get_sub_field( 'slide_location' ) ),
		);
	}
	return $out;
}

/**
 * Diapositivas YouTube del hero landing completa con textos opcionales por fila.
 *
 * @param int    $post_id ID de la landing.
 * @param string $repeater_name Nombre del repetidor ACF.
 * @return array<int, array{youtube_id: string, slide_tagline: string, slide_heading: string, slide_location: string}>
 */
function theme_skema_landing_full_hero_slides_from_youtube_repeater( $post_id, $repeater_name ) {
	$post_id = absint( $post_id );
	$out     = array();
	if ( ! $post_id || ! function_exists( 'have_rows' ) || ! function_exists( 'theme_skema_youtube_id_from_url' ) ) {
		return $out;
	}
	if ( ! have_rows( $repeater_name, $post_id ) ) {
		return $out;
	}
	while ( have_rows( $repeater_name, $post_id ) ) {
		the_row();
		$url = get_sub_field( 'youtube_url' );
		$url = is_string( $url ) ? trim( $url ) : '';
		if ( $url === '' ) {
			continue;
		}
		$id = theme_skema_youtube_id_from_url( $url );
		if ( $id === '' ) {
			continue;
		}
		$out[] = array(
			'youtube_id'     => $id,
			'slide_tagline'  => trim( (string) get_sub_field( 'slide_tagline' ) ),
			'slide_heading'  => trim( (string) get_sub_field( 'slide_heading' ) ),
			'slide_location' => trim( (string) get_sub_field( 'slide_location' ) ),
		);
	}
	return $out;
}

/**
 * Aplica textos por slide sobre el bundle global del hero (solo sustituye si el subcampo no está vacío).
 *
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $global_bundle De theme_skema_get_landing_full_hero_copy_bundle.
 * @param array{slide_tagline?: string, slide_heading?: string, slide_location?: string} $slide_row Fila del repetidor.
 * @return array{tagline: string, h1: string, location: string, logo_url: string}
 */
function theme_skema_landing_full_hero_merge_slide_copy( array $global_bundle, array $slide_row ) {
	$merged = $global_bundle;

	$t = isset( $slide_row['slide_tagline'] ) ? trim( (string) $slide_row['slide_tagline'] ) : '';
	if ( $t !== '' ) {
		$merged['tagline'] = $t;
	}

	$h = isset( $slide_row['slide_heading'] ) ? trim( (string) $slide_row['slide_heading'] ) : '';
	if ( $h !== '' ) {
		$merged['h1'] = $h;
	}

	$l = isset( $slide_row['slide_location'] ) ? trim( (string) $slide_row['slide_location'] ) : '';
	if ( $l !== '' ) {
		$merged['location'] = $l;
	}

	return $merged;
}

/**
 * Título accesible (H1 oculto): primera diapositiva visible con textos ya fusionados.
 *
 * @param array<int, array<string, string>> $slides Diapositivas con claves slide_*.
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $global_bundle Bundle global.
 * @param string $landing_title Título de la entrada como último recurso.
 * @return string
 */
function theme_skema_landing_full_hero_sr_heading_from_first_slide( array $slides, array $global_bundle, $landing_title ) {
	if ( empty( $slides ) ) {
		$h = isset( $global_bundle['h1'] ) ? trim( (string) $global_bundle['h1'] ) : '';
		return $h !== '' ? $h : trim( (string) $landing_title );
	}

	$merged = theme_skema_landing_full_hero_merge_slide_copy( $global_bundle, $slides[0] );
	$h      = isset( $merged['h1'] ) ? trim( (string) $merged['h1'] ) : '';

	return $h !== '' ? $h : trim( (string) $landing_title );
}

/**
 * Textos que rotan con el slide. La línea se omite si repite el H1 de la página.
 *
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $global_bundle Bundle global.
 * @param array{slide_tagline?: string, slide_heading?: string, slide_location?: string} $slide_row Fila del repetidor.
 * @return array{line: string, tagline: string, location: string}
 */
function theme_skema_landing_hero_variable_from_slide( array $global_bundle, array $slide_row ) {
	$merged = theme_skema_landing_full_hero_merge_slide_copy( $global_bundle, $slide_row );
	$page   = isset( $global_bundle['h1'] ) ? trim( (string) $global_bundle['h1'] ) : '';
	$line   = isset( $merged['h1'] ) ? trim( (string) $merged['h1'] ) : '';
	if ( $line === $page ) {
		$line = '';
	}

	return array(
		'line'     => $line,
		'tagline'  => isset( $merged['tagline'] ) ? trim( (string) $merged['tagline'] ) : '',
		'location' => isset( $merged['location'] ) ? trim( (string) $merged['location'] ) : '',
	);
}

/**
 * Primer slide con medio usable, para pintar el copy antes de que arranque Slick.
 *
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $global_bundle Bundle global.
 * @param array<int, array<string, string>> $slides Filas del repetidor.
 * @param string $media_key Clave que indica que el slide tiene medio (`url` o `youtube_id`).
 * @return array{line: string, tagline: string, location: string}
 */
function theme_skema_landing_hero_first_variable( array $global_bundle, array $slides, $media_key = 'url' ) {
	foreach ( $slides as $slide_row ) {
		if ( ! is_array( $slide_row ) ) {
			continue;
		}
		$media = isset( $slide_row[ $media_key ] ) ? trim( (string) $slide_row[ $media_key ] ) : '';
		if ( $media === '' ) {
			continue;
		}
		return theme_skema_landing_hero_variable_from_slide( $global_bundle, $slide_row );
	}

	return theme_skema_landing_hero_variable_from_slide( $global_bundle, array() );
}

/**
 * Atributos de copy en el slide, para que el JS actualice el párrafo fuera del carrusel.
 *
 * @param array{line: string, tagline: string, location: string} $variable Textos del slide.
 */
function theme_skema_echo_landing_hero_slide_open( array $variable, $extra_class = '' ) {
	$class = 'landing-full-slide';
	if ( $extra_class !== '' ) {
		$class .= ' ' . $extra_class;
	}
	printf(
		'<div class="%1$s" data-slide-line="%2$s" data-slide-tagline="%3$s" data-slide-location="%4$s">',
		esc_attr( $class ),
		esc_attr( $variable['line'] ),
		esc_attr( $variable['tagline'] ),
		esc_attr( $variable['location'] )
	);
}

/**
 * Párrafo, bajada y ubicación de un grupo de slides (escritorio, móvil o único).
 *
 * @param array{line: string, tagline: string, location: string} $variable Textos iniciales.
 * @param string $slot Identificador que el JS empareja con el slider (`desk`, `mob` o `shared`).
 * @param string $visibility_class Clases de Bootstrap para mostrar el grupo en un solo punto de corte.
 */
function theme_skema_echo_landing_hero_variable_group( array $variable, $slot, $visibility_class = '' ) {
	$line     = isset( $variable['line'] ) ? $variable['line'] : '';
	$tagline  = isset( $variable['tagline'] ) ? $variable['tagline'] : '';
	$location = isset( $variable['location'] ) ? $variable['location'] : '';
	$class    = 'landing-full-hero__variable';
	if ( $visibility_class !== '' ) {
		$class .= ' ' . $visibility_class;
	}
	?>
<div class="<?php echo esc_attr( $class ); ?>" data-hero-slot="<?php echo esc_attr( $slot ); ?>">
    <p class="project-hero-title landing-full-hero__slide-line<?php echo $line === '' ? ' is-absent' : ''; ?>" data-hero-field="line"><?php echo esc_html( $line ); ?></p>
    <span class="tag-line landing-full-hero__tagline<?php echo $tagline === '' ? ' is-absent' : ''; ?>" data-hero-field="tagline"><?php echo esc_html( $tagline ); ?></span>
    <p class="landing-full-hero__location<?php echo $location === '' ? ' is-absent' : ''; ?>" data-hero-field="location">
        <i class="bi bi-geo-alt" aria-hidden="true"></i>
        <span class="landing-full-hero__location-text"><?php echo esc_html( $location ); ?></span>
    </p>
</div>
	<?php
}

/**
 * H1 único, fuera del carrusel, y los textos que cambian por slide en párrafos.
 *
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $page_bundle Bundle global. Su `h1` es el título de la página.
 * @param array<int, array{slot: string, class: string, variable: array{line: string, tagline: string, location: string}}> $groups Grupos de copy.
 */
function theme_skema_echo_landing_full_hero_stable_copy( array $page_bundle, array $groups ) {
	$heading  = isset( $page_bundle['h1'] ) ? trim( (string) $page_bundle['h1'] ) : '';
	$logo_url = isset( $page_bundle['logo_url'] ) ? $page_bundle['logo_url'] : '';
	if ( $heading === '' && $logo_url === '' && empty( $groups ) ) {
		return;
	}
	?>
<div class="landing-full-hero__copy landing-full-slide__inner">
    <div class="container">
        <div class="row justify-content-start">
            <div class="col-lg-10">
                <div class="hero-content text-start">
                    <?php if ( $logo_url !== '' ) : ?>
                    <div class="project-hero-branding">
                        <div class="project-hero-logo">
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="" loading="lazy" decoding="async" />
                        </div>
                        <div class="project-hero-text">
                            <?php if ( $heading !== '' ) : ?>
                            <h1 class="project-hero-title"><?php echo esc_html( $heading ); ?></h1>
                            <?php endif; ?>
                            <?php foreach ( $groups as $group ) : ?>
                            <?php theme_skema_echo_landing_hero_variable_group( $group['variable'], $group['slot'], $group['class'] ); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="project-hero-text project-hero-text--solo">
                        <?php if ( $heading !== '' ) : ?>
                        <h1 class="project-hero-title"><?php echo esc_html( $heading ); ?></h1>
                        <?php endif; ?>
                        <?php foreach ( $groups as $group ) : ?>
                        <?php theme_skema_echo_landing_hero_variable_group( $group['variable'], $group['slot'], $group['class'] ); ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
	<?php
}

/**
 * Bloque interior hero-content. El título del slide es un párrafo: el H1 vive fuera del carrusel.
 *
 * La firma se mantiene. `$heading_tag` ya no elige la etiqueta.
 *
 * @param array{tagline: string, h1: string, location: string, logo_url: string} $bundle Datos de copy.
 * @param string $heading_tag Se ignora. Conservado para no romper llamadas existentes.
 */
function theme_skema_echo_landing_full_hero_inner_markup( array $bundle, $heading_tag = 'h2' ) {
	unset( $heading_tag );
	$tagline  = isset( $bundle['tagline'] ) ? $bundle['tagline'] : '';
	$heading  = isset( $bundle['h1'] ) ? $bundle['h1'] : '';
	$location = isset( $bundle['location'] ) ? $bundle['location'] : '';
	$logo_url = isset( $bundle['logo_url'] ) ? $bundle['logo_url'] : '';

	$show_branding_row = ( $logo_url !== '' );
	?>
<div class="container">
    <div class="row justify-content-start">
        <div class="col-lg-10">
            <div class="hero-content text-start">
                <?php if ( $show_branding_row ) : ?>
                <div class="project-hero-branding">
                    <div class="project-hero-logo">
                        <img src="<?php echo esc_url( $logo_url ); ?>"
                            alt=""
                            loading="lazy" decoding="async" />
                    </div>
                    <div class="project-hero-text">
                        <?php if ( $heading !== '' ) : ?>
                        <p class="project-hero-title"><?php echo esc_html( $heading ); ?></p>
                        <?php endif; ?>
                        <?php if ( $tagline !== '' ) : ?>
                        <span class="tag-line"><?php echo esc_html( $tagline ); ?></span>
                        <?php endif; ?>
                        <?php if ( $location !== '' ) : ?>
                        <p class="landing-full-hero__location">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <?php echo esc_html( $location ); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else : ?>
                <div class="project-hero-text project-hero-text--solo">
                    <?php if ( $heading !== '' ) : ?>
                    <p class="project-hero-title"><?php echo esc_html( $heading ); ?></p>
                    <?php endif; ?>
                    <?php if ( $tagline !== '' ) : ?>
                    <span class="tag-line"><?php echo esc_html( $tagline ); ?></span>
                    <?php endif; ?>
                    <?php if ( $location !== '' ) : ?>
                    <p class="landing-full-hero__location">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <?php echo esc_html( $location ); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
}

/**
 * Slider hero landing completa: pantalla completa + copy por slide (patrón prelanding / referencia Renvia).
 *
 * @param int $post_id ID de la landing.
 */
function theme_skema_render_landing_full_hero_section( $post_id ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$post_id = absint( $post_id );
	if ( ! $post_id ) {
		return;
	}

	$rep_desk    = 'skema_lland_slider_img';
	$rep_movil   = 'skema_lland_slider_img_movil';
	$rep_youtube = 'skema_lland_slider_youtube';

	$tipo = get_field( 'skema_lland_hero_tipo', $post_id );
	$tipo = is_string( $tipo ) ? $tipo : 'none';

	if ( ! theme_skema_landing_full_has_configured_hero_media( $post_id, $tipo ) ) {
		return;
	}

	$landing_title = get_the_title( $post_id );
	$copy_bundle   = theme_skema_get_landing_full_hero_copy_bundle( $post_id );

	$desk_slides = array();
	$mob_slides  = array();
	$slides_yt   = array();

	if ( 'images' === $tipo ) {
		$desk_slides = theme_skema_landing_full_hero_slides_from_image_repeater( $post_id, $rep_desk );
		$mob_slides  = theme_skema_landing_full_hero_slides_from_image_repeater( $post_id, $rep_movil );
		if ( empty( $mob_slides ) ) {
			$mob_slides = $desk_slides;
		}
	} elseif ( 'youtube' === $tipo ) {
		$slides_yt = theme_skema_landing_full_hero_slides_from_youtube_repeater( $post_id, $rep_youtube );
	}

	echo '<section class="landing-full-hero project-page-hero renvia-hero_one">';
	echo '<div class="shape-one" aria-hidden="true"><span></span></div>';

	if ( 'images' === $tipo ) {
		if ( empty( $desk_slides ) && empty( $mob_slides ) ) {
			echo '</section>';
			return;
		}

		$copy_groups = array();
		if ( ! empty( $desk_slides ) ) {
			$copy_groups[] = array(
				'slot'     => 'desk',
				'class'    => 'd-none d-sm-block',
				'variable' => theme_skema_landing_hero_first_variable( $copy_bundle, $desk_slides, 'url' ),
			);
		}
		if ( ! empty( $mob_slides ) ) {
			$copy_groups[] = array(
				'slot'     => 'mob',
				'class'    => 'd-sm-none d-block',
				'variable' => theme_skema_landing_hero_first_variable( $copy_bundle, $mob_slides, 'url' ),
			);
		}

		echo '<div class="landing-hero landing-hero--landing-full">';
		if ( ! empty( $desk_slides ) ) {
			echo '<div class="slider-banner d-none d-sm-block">';
			echo '<div class="slick-slider-banner slick-slider-banner--landing-full">';
			$slide_i = 1;
			foreach ( $desk_slides as $slide_row ) {
				$img_url = isset( $slide_row['url'] ) ? $slide_row['url'] : '';
				if ( $img_url === '' ) {
					continue;
				}
				$slide_alt = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera %1$d — %2$s', 'theme_skema' ),
					$slide_i,
					$landing_title
				);
				theme_skema_echo_landing_hero_slide_open(
					theme_skema_landing_hero_variable_from_slide( $copy_bundle, $slide_row )
				);
				echo '<div class="landing-full-slide__media" aria-hidden="true">';
				printf(
					'<img src="%s" class="landing-full-slide__img" alt="%s" loading="%s" decoding="async" />',
					esc_url( $img_url ),
					esc_attr( $slide_alt ),
					esc_attr( 1 === $slide_i ? 'eager' : 'lazy' )
				);
				echo '</div>';
				echo '<div class="landing-full-slide__overlay" aria-hidden="true"></div>';
				echo '</div>';
				++$slide_i;
			}
			echo '</div>';
			echo '<div class="home-hero-dots"></div>';
			echo '</div>';
		}
		if ( ! empty( $mob_slides ) ) {
			echo '<div class="slider-banner d-sm-none d-block">';
			echo '<div class="slick-slider-banner slick-slider-banner--landing-full">';
			$slide_m = 1;
			foreach ( $mob_slides as $slide_row ) {
				$img_url = isset( $slide_row['url'] ) ? $slide_row['url'] : '';
				if ( $img_url === '' ) {
					continue;
				}
				$slide_alt_m = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera móvil %1$d — %2$s', 'theme_skema' ),
					$slide_m,
					$landing_title
				);
				theme_skema_echo_landing_hero_slide_open(
					theme_skema_landing_hero_variable_from_slide( $copy_bundle, $slide_row )
				);
				echo '<div class="landing-full-slide__media" aria-hidden="true">';
				printf(
					'<img src="%s" class="landing-full-slide__img" alt="%s" loading="%s" decoding="async" />',
					esc_url( $img_url ),
					esc_attr( $slide_alt_m ),
					esc_attr( 1 === $slide_m ? 'eager' : 'lazy' )
				);
				echo '</div>';
				echo '<div class="landing-full-slide__overlay" aria-hidden="true"></div>';
				echo '</div>';
				++$slide_m;
			}
			echo '</div>';
			echo '<div class="home-hero-dots"></div>';
			echo '</div>';
		}
		theme_skema_echo_landing_full_hero_stable_copy( $copy_bundle, $copy_groups );
		echo '</div></section>';
		return;
	}

	if ( 'youtube' !== $tipo ) {
		echo '</section>';
		return;
	}

	if ( empty( $slides_yt ) ) {
		echo '</section>';
		return;
	}

	echo '<div class="landing-hero landing-hero--landing-full">';
	echo '<div class="slider-banner slider-banner--youtube">';
	echo '<div class="slick-slider-banner slick-slider-banner--youtube slick-slider-banner--landing-full">';
	foreach ( $slides_yt as $idx => $slide_row ) {
		$slide_yt_id = isset( $slide_row['youtube_id'] ) ? $slide_row['youtube_id'] : '';
		if ( $slide_yt_id === '' ) {
			continue;
		}
		$autoplay  = 0 === $idx ? '1' : '0';
		$embed_src = sprintf(
			'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&enablejsapi=1&autoplay=%s',
			rawurlencode( $slide_yt_id ),
			$autoplay
		);
		$slide_title = sprintf(
			/* translators: %d: slide number */
			__( 'Cabecera vídeo %d', 'theme_skema' ),
			$idx + 1
		);
		$variable = theme_skema_landing_hero_variable_from_slide( $copy_bundle, $slide_row );
		printf(
			'<div class="landing-full-slide landing-full-slide--youtube hero-yt-slide" data-slide-index="%1$s" data-youtube-id="%2$s" data-slide-line="%3$s" data-slide-tagline="%4$s" data-slide-location="%5$s">',
			esc_attr( (string) $idx ),
			esc_attr( $slide_yt_id ),
			esc_attr( $variable['line'] ),
			esc_attr( $variable['tagline'] ),
			esc_attr( $variable['location'] )
		);
		echo '<div class="hero-yt-embed">';
		printf(
			'<iframe class="hero-yt-iframe" src="%s" title="%s" width="560" height="315" tabindex="-1" aria-hidden="true" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="%s"></iframe>',
			esc_url( $embed_src ),
			esc_attr( $slide_title ),
			esc_attr( 0 === $idx ? 'eager' : 'lazy' )
		);
		echo '</div>';
		echo '<div class="landing-full-slide__overlay landing-full-slide__overlay--youtube" aria-hidden="true"></div>';
		echo '</div>';
	}
	echo '</div>';
	echo '<div class="home-hero-dots"></div>';
	echo '</div>';
	theme_skema_echo_landing_full_hero_stable_copy(
		$copy_bundle,
		array(
			array(
				'slot'     => 'shared',
				'class'    => '',
				'variable' => theme_skema_landing_hero_first_variable( $copy_bundle, $slides_yt, 'youtube_id' ),
			),
		)
	);
	echo '</div></section>';
}

/**
 * Imprime la cabecera slider según la fase editorial de la landing (ACF pestañas Prelanding / Landing completa).
 *
 * @param int|null $post_id ID o null para la consulta actual.
 */
function theme_skema_render_landing_hero( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	if ( null === $post_id ) {
		$post_id = function_exists( 'theme_skema_viewing_landing_id' )
			? theme_skema_viewing_landing_id()
			: get_queried_object_id();
	}
	$post_id = absint( $post_id );
	if ( ! $post_id || 'landings' !== get_post_type( $post_id ) ) {
		return;
	}

	$phase = function_exists( 'theme_skema_get_landing_phase' )
		? theme_skema_get_landing_phase( $post_id )
		: 'prelanding';

	if ( 'prelanding' === $phase ) {
		theme_skema_render_prelanding_hero_section( $post_id );
		return;
	}

	theme_skema_render_landing_full_hero_section( $post_id );
}

/**
 * Logo de proyecto (ACF por fase: prelanding vs landing completa).
 *
 * @param int|null $post_id ID o null para la consulta actual.
 */
function theme_skema_render_landing_project_logo( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) || ! function_exists( 'theme_skema_acf_value_to_image_url' ) ) {
		return;
	}

	if ( null === $post_id ) {
		$post_id = function_exists( 'theme_skema_viewing_landing_id' )
			? theme_skema_viewing_landing_id()
			: get_queried_object_id();
	}
	$post_id = absint( $post_id );
	if ( ! $post_id || 'landings' !== get_post_type( $post_id ) ) {
		return;
	}

	$phase = function_exists( 'theme_skema_get_landing_phase' )
		? theme_skema_get_landing_phase( $post_id )
		: 'prelanding';

	if ( 'prelanding' === $phase ) {
		return;
	}

	$field = 'skema_lland_project_logo';
	$raw   = get_field( $field, $post_id );
	$url   = theme_skema_acf_value_to_image_url( $raw );
	if ( $url === '' ) {
		return;
	}

	$title = get_the_title( $post_id );
	$alt   = sprintf(
		/* translators: %s: landing title */
		__( 'Logo — %s', 'theme_skema' ),
		$title
	);
	?>
<div class="landing-project-logo-wrap">
    <img src="<?php echo esc_url( $url ); ?>" class="landing-project-logo img-fluid"
        alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
</div>
<?php
}