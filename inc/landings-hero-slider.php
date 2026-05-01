<?php
/**
 * Landings: render de cabecera slider (misma base que theme_inicio / Slick) y logo de proyecto.
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

	if ( 'landing' === $phase ) {
		$tipo_key   = 'skema_lland_hero_tipo';
		$rep_desk   = 'skema_lland_slider_img';
		$rep_movil  = 'skema_lland_slider_img_movil';
		$rep_youtube = 'skema_lland_slider_youtube';
	} else {
		$tipo_key   = 'skema_lpre_hero_tipo';
		$rep_desk   = 'skema_lpre_slider_img';
		$rep_movil  = 'skema_lpre_slider_img_movil';
		$rep_youtube = 'skema_lpre_slider_youtube';
	}

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
				<img src="<?php echo esc_url( $img_url ); ?>" class="img-fluid w-100" alt="<?php echo esc_attr( $slide_alt ); ?>" loading="<?php echo 1 === $slide_i ? 'eager' : 'lazy'; ?>" decoding="async" />
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
				<img src="<?php echo esc_url( $img_url ); ?>" class="img-fluid w-100" alt="<?php echo esc_attr( $slide_alt_m ); ?>" loading="<?php echo 1 === $slide_m ? 'eager' : 'lazy'; ?>" decoding="async" />
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

	$field = ( 'landing' === $phase ) ? 'skema_lland_project_logo' : 'skema_lpre_project_logo';
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
	<img src="<?php echo esc_url( $url ); ?>" class="landing-project-logo img-fluid" alt="<?php echo esc_attr( $alt ); ?>" loading="lazy" decoding="async" />
</div>
	<?php
}
