<?php
/**
 * Inicio: cabecera hero según ACF (`tipo_banner`: vídeo, slider de imágenes, slider YouTube).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imprime la cabecera del inicio (vídeo / imágenes / YouTube).
 *
 * Campos ACF esperados en la página de inicio: `tipo_banner`, `video-banner`,
 * `video-banner-movil`, repeaters `slider_img`, `slider_img_movil`, `slider_youtube`.
 *
 * @return void
 */
function theme_skema_render_inicio_hero_banner() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$tipo_banner = get_field( 'tipo_banner' );

	if ( 'Video' === $tipo_banner ) {
		theme_skema_render_inicio_hero_video();
		return;
	}

	if ( 'Slider' === $tipo_banner ) {
		theme_skema_render_inicio_hero_image_sliders();
		return;
	}

	if ( 'slider_youtube' !== $tipo_banner ) {
		return;
	}

	theme_skema_render_inicio_hero_youtube_slider();
}

/**
 * Banner en modo vídeo (escritorio + móvil).
 *
 * @return void
 */
function theme_skema_render_inicio_hero_video() {
	?>
	<div class="video-content">
		<div class="video-background">
			<div class="video-foreground reveal">
				<video id="video-banner" class="d-none d-sm-block" src="<?php the_field( 'video-banner' ); ?>" autoplay
					playsinline loop muted>
					Your browser does not support HTML5 video.
				</video>
				<video id="video-banner-movil" class="d-sm-none d-block" src="<?php the_field( 'video-banner-movil' ); ?>"
					autoplay playsinline loop muted>
					Your browser does not support HTML5 video.
				</video>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Sliders Slick de imágenes (escritorio y móvil).
 *
 * @return void
 */
function theme_skema_render_inicio_hero_image_sliders() {
	?>
	<div class="slider-banner d-none d-sm-block">
		<div class="slick-slider-banner">
			<?php theme_skema_render_inicio_hero_image_slides( 'slider_img', 'cabecera' ); ?>
		</div>
		<div class="home-hero-dots" aria-hidden="true"></div>
	</div>
	<div class="slider-banner d-sm-none d-block">
		<div class="slick-slider-banner">
			<?php theme_skema_render_inicio_hero_image_slides( 'slider_img_movil', 'cabecera_movil' ); ?>
		</div>
	</div>
	<?php
}

/**
 * Slide de imágenes desde un repeater ACF.
 *
 * @param string $repeater_name Nombre del campo repeater.
 * @param string $context       'cabecera' | 'cabecera_movil' (textos de accesibilidad).
 * @return void
 */
function theme_skema_render_inicio_hero_image_slides( $repeater_name, $context ) {
	if ( ! function_exists( 'have_rows' ) ) {
		return;
	}

	$slide_i = 1;

	if ( ! have_rows( $repeater_name ) ) {
		return;
	}

	while ( have_rows( $repeater_name ) ) {
		the_row();
		$img = get_sub_field( 'img' );
		if ( ! $img ) {
			continue;
		}

		if ( 'cabecera_movil' === $context ) {
			$slide_alt = sprintf(
				/* translators: 1: slide number, 2: site name */
				__( 'Cabecera móvil %1$d — %2$s', 'theme_skema' ),
				$slide_i,
				get_bloginfo( 'name' )
			);
		} else {
			$slide_alt = sprintf(
				/* translators: 1: slide number, 2: site name */
				__( 'Cabecera %1$d — %2$s', 'theme_skema' ),
				$slide_i,
				get_bloginfo( 'name' )
			);
		}
		?>
			<div>
				<img src="<?php echo esc_url( $img ); ?>" class="img-fluid w-100" alt="<?php echo esc_attr( $slide_alt ); ?>">
			</div>
		<?php
		$slide_i++;
	}
}

/**
 * Recoge IDs de YouTube válidos desde el repeater `slider_youtube`.
 *
 * @return array<int, array{id: string}>
 */
function theme_skema_inicio_hero_youtube_slides_data() {
	if ( ! function_exists( 'have_rows' ) || ! function_exists( 'theme_skema_youtube_id_from_url' ) ) {
		return array();
	}

	$slides_yt = array();

	if ( ! have_rows( 'slider_youtube' ) ) {
		return $slides_yt;
	}

	while ( have_rows( 'slider_youtube' ) ) {
		the_row();
		$yt_url = get_sub_field( 'youtube_url' );
		$yt_id  = theme_skema_youtube_id_from_url( $yt_url );
		if ( ! $yt_id ) {
			continue;
		}
		$slides_yt[] = array( 'id' => $yt_id );
	}

	return $slides_yt;
}

/**
 * Slider de iframes YouTube (cabecera).
 *
 * @return void
 */
function theme_skema_render_inicio_hero_youtube_slider() {
	$slides_yt = theme_skema_inicio_hero_youtube_slides_data();

	if ( array() === $slides_yt ) {
		return;
	}

	?>
	<div class="slider-banner slider-banner--youtube">
		<div class="slick-slider-banner slick-slider-banner--youtube">
			<?php
			foreach ( $slides_yt as $idx => $slide_yt ) {
				$autoplay = 0 === $idx ? '1' : '0';
				$embed_src = sprintf(
					'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&enablejsapi=1&autoplay=%s',
					rawurlencode( $slide_yt['id'] ),
					$autoplay
				);
				$slide_title = sprintf(
					/* translators: %d: slide number */
					__( 'Cabecera vídeo %d', 'theme_skema' ),
					$idx + 1
				);
				?>
			<div class="hero-yt-slide" data-slide-index="<?php echo esc_attr( (string) $idx ); ?>"
				data-youtube-id="<?php echo esc_attr( $slide_yt['id'] ); ?>">
				<div class="hero-yt-embed">
					<iframe class="hero-yt-iframe" src="<?php echo esc_url( $embed_src ); ?>"
						title="<?php echo esc_attr( $slide_title ); ?>" width="560" height="315"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
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
	<?php
}
