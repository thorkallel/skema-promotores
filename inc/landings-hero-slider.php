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
			echo '<div class="home-hero-dots" aria-hidden="true"></div>';
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
			echo '<div class="home-hero-dots" aria-hidden="true"></div>';
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
				'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&autoplay=%s',
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
				'<iframe class="hero-yt-iframe" src="%s" title="%s" width="560" height="315" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="%s"></iframe>',
				esc_url( $embed_src ),
				esc_attr( $slide_title ),
				esc_attr( 0 === $idx ? 'eager' : 'lazy' )
			);
			echo '</div></div>';
		}
		echo '</div>';
		echo '<div class="home-hero-dots" aria-hidden="true"></div>';
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
	$intro     = trim( (string) get_field( 'skema_lpre_hero_intro', $post_id ) );
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
                                alt="<?php echo esc_attr( sprintf( __( 'Logo — %s', 'theme_skema' ), $h1 ) ); ?>"
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
                    <p class="prelanding-copy__intro"><?php echo esc_html( $intro ); ?></p>
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
            <div class="col-xl-4 col-lg-5 ms-xl-auto">
                <div class="project-info-box project-info-box--lead" id="<?php echo esc_attr( $anchor_id ); ?>">
                    <?php if ( $lead_heading !== '' ) : ?>
                    <h5><?php echo esc_html( $lead_heading ); ?></h5>
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
 * Imprime la cabecera slider según la fase editorial de la landing (ACF pestañas Prelanding / Landing completa).
 *
 * @param int|null $post_id ID o null para la consulta actual.
 */
function theme_skema_render_landing_hero( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	if ( null === $post_id ) {
		$post_id = get_queried_object_id();
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

	$tipo_key    = 'skema_lland_hero_tipo';
	$rep_desk    = 'skema_lland_slider_img';
	$rep_movil   = 'skema_lland_slider_img_movil';
	$rep_youtube = 'skema_lland_slider_youtube';

	$tipo = get_field( $tipo_key, $post_id );
	$tipo = is_string( $tipo ) ? $tipo : 'none';

	$landing_title = get_the_title( $post_id );

	if ( 'images' === $tipo ) {
		$desk_urls = theme_skema_landing_hero_image_urls_from_repeater( $post_id, $rep_desk );
		$mob_urls  = theme_skema_landing_hero_image_urls_from_repeater( $post_id, $rep_movil );
		if ( empty( $mob_urls ) ) {
			$mob_urls = $desk_urls;
		}
		if ( empty( $desk_urls ) && empty( $mob_urls ) ) {
			return;
		}
		?>
<div class="landing-hero">
    <?php if ( ! empty( $desk_urls ) ) : ?>
    <div class="slider-banner d-none d-sm-block">
        <div class="slick-slider-banner">
            <?php
			$slide_i = 1;
			foreach ( $desk_urls as $img_url ) :
				$slide_alt = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera %1$d — %2$s', 'theme_skema' ),
					$slide_i,
					$landing_title
				);
				?>
            <div>
                <img src="<?php echo esc_url( $img_url ); ?>" class="img-fluid w-100"
                    alt="<?php echo esc_attr( $slide_alt ); ?>"
                    loading="<?php echo 1 === $slide_i ? 'eager' : 'lazy'; ?>" decoding="async" />
            </div>
            <?php
				++$slide_i;
			endforeach;
			?>
        </div>
        <div class="home-hero-dots" aria-hidden="true"></div>
    </div>
    <?php endif; ?>
    <?php if ( ! empty( $mob_urls ) ) : ?>
    <div class="slider-banner d-sm-none d-block">
        <div class="slick-slider-banner">
            <?php
			$slide_m = 1;
			foreach ( $mob_urls as $img_url ) :
				$slide_alt_m = sprintf(
					/* translators: 1: slide number, 2: landing title */
					__( 'Cabecera móvil %1$d — %2$s', 'theme_skema' ),
					$slide_m,
					$landing_title
				);
				?>
            <div>
                <img src="<?php echo esc_url( $img_url ); ?>" class="img-fluid w-100"
                    alt="<?php echo esc_attr( $slide_alt_m ); ?>"
                    loading="<?php echo 1 === $slide_m ? 'eager' : 'lazy'; ?>" decoding="async" />
            </div>
            <?php
				++$slide_m;
			endforeach;
			?>
        </div>
        <div class="home-hero-dots" aria-hidden="true"></div>
    </div>
    <?php endif; ?>
</div>
<?php
		return;
	}

	if ( 'youtube' === $tipo ) {
		$slides_yt = theme_skema_landing_hero_youtube_ids_from_repeater( $post_id, $rep_youtube );
		if ( empty( $slides_yt ) ) {
			return;
		}
		?>
<div class="landing-hero">
    <div class="slider-banner slider-banner--youtube">
        <div class="slick-slider-banner slick-slider-banner--youtube">
            <?php
			foreach ( $slides_yt as $idx => $slide_yt_id ) {
				$autoplay  = 0 === $idx ? '1' : '0';
				$embed_src = sprintf(
					'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&autoplay=%s',
					rawurlencode( $slide_yt_id ),
					$autoplay
				);
				$slide_title = sprintf(
					/* translators: %d: slide number */
					__( 'Cabecera vídeo %d', 'theme_skema' ),
					$idx + 1
				);
				?>
            <div class="hero-yt-slide" data-slide-index="<?php echo esc_attr( (string) $idx ); ?>"
                data-youtube-id="<?php echo esc_attr( $slide_yt_id ); ?>">
                <div class="hero-yt-embed">
                    <iframe class="hero-yt-iframe" src="<?php echo esc_url( $embed_src ); ?>"
                        title="<?php echo esc_attr( $slide_title ); ?>" width="560" height="315"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                        loading="<?php echo 0 === $idx ? 'eager' : 'lazy'; ?>"></iframe>
                </div>
            </div>
            <?php
			}
			?>
        </div>
        <div class="home-hero-dots" aria-hidden="true"></div>
    </div>
</div>
<?php
	}
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
		$post_id = get_queried_object_id();
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