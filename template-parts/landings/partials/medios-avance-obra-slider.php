<?php
/**
 * Landings: slider «Avance de obra» (Plantas + Slick; imagen, MP4 o YouTube; fecha y leyenda opcionales).
 *
 * Variables (require desde landing-content-box.php):
 * @var array<int, array<string, string>> $avance_obra_slides
 * @var string                            $avance_id_prefix Prefijo único por post (ej. skema-lland-123).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $avance_obra_slides ) || ! is_array( $avance_obra_slides ) || count( $avance_obra_slides ) === 0 ) {
	return;
}

if ( ! isset( $avance_id_prefix ) || ! is_string( $avance_id_prefix ) || $avance_id_prefix === '' ) {
	return;
}

$skema_avance_main_id = $avance_id_prefix . '-avance-main';
$skema_avance_nav_id  = $avance_id_prefix . '-avance-nav';
?>
<div
	class="plantas-slider-wrap mb-30 skema-landing-plantas-wrap skema-landing-plantas-wrap--avance-obra skema-landing-slot skema-landing-slot--avance-obra"
	data-skema-plantas="1"
>
	<div id="<?php echo esc_attr( $skema_avance_main_id ); ?>" class="plantas-slider-main renvia-image">
		<?php foreach ( $avance_obra_slides as $skema_av_slide ) : ?>
			<?php
			$skema_av_media   = isset( $skema_av_slide['media'] ) ? (string) $skema_av_slide['media'] : 'image';
			$skema_av_src     = isset( $skema_av_slide['src'] ) ? trim( (string) $skema_av_slide['src'] ) : '';
			$skema_av_iframe  = isset( $skema_av_slide['iframe_src'] ) ? trim( (string) $skema_av_slide['iframe_src'] ) : '';
			$skema_av_poster  = isset( $skema_av_slide['poster'] ) ? trim( (string) $skema_av_slide['poster'] ) : '';
			$skema_av_alt     = isset( $skema_av_slide['alt'] ) ? (string) $skema_av_slide['alt'] : '';
			$skema_av_leyenda = isset( $skema_av_slide['leyenda'] ) ? trim( (string) $skema_av_slide['leyenda'] ) : '';
			$skema_av_fecha_t = isset( $skema_av_slide['fecha_texto'] ) ? trim( (string) $skema_av_slide['fecha_texto'] ) : '';
			$skema_av_fecha_i = isset( $skema_av_slide['fecha_iso'] ) ? trim( (string) $skema_av_slide['fecha_iso'] ) : '';

			if ( $skema_av_media === 'youtube' ) {
				if ( $skema_av_iframe === '' ) {
					continue;
				}
			} elseif ( $skema_av_src === '' ) {
				continue;
			}

			$skema_av_show_historico = ( $skema_av_fecha_t !== '' || $skema_av_leyenda !== '' );
			?>
		<div class="plantas-slide">
			<div class="plantas-slide-inner plantas-slide-inner--avance">
				<?php if ( $skema_av_media === 'youtube' ) : ?>
				<div class="embed-responsive embed-responsive-16by9 skema-landing-avance-obra__yt-wrap">
					<iframe
						class="embed-responsive-item skema-landing-avance-obra__yt-iframe"
						src="<?php echo esc_url( $skema_av_iframe ); ?>"
						title="<?php echo esc_attr( $skema_av_alt ); ?>"
						loading="lazy"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
				<?php elseif ( $skema_av_media === 'video' ) : ?>
				<video
					class="skema-landing-avance-obra__video"
					controls
					playsinline
					preload="metadata"
					aria-label="<?php echo esc_attr( $skema_av_alt ); ?>"
					<?php if ( $skema_av_poster !== '' ) : ?>
					poster="<?php echo esc_url( $skema_av_poster ); ?>"
					<?php endif; ?>
				>
					<source src="<?php echo esc_url( $skema_av_src ); ?>" type="video/mp4">
					<?php esc_html_e( 'Tu navegador no reproduce video HTML5.', 'theme_skema' ); ?>
				</video>
				<?php else : ?>
				<img
					src="<?php echo esc_url( $skema_av_src ); ?>"
					alt="<?php echo esc_attr( $skema_av_alt ); ?>"
					loading="lazy"
					decoding="async"
				>
				<?php endif; ?>
				<?php if ( $skema_av_show_historico ) : ?>
				<p class="skema-landing-avance-obra__historico">
					<?php if ( $skema_av_leyenda !== '' ) : ?>
					<span class="skema-landing-avance-obra__leyenda"><?php echo esc_html( $skema_av_leyenda ); ?></span>
					<?php endif; ?>
					<?php if ( $skema_av_leyenda !== '' && $skema_av_fecha_t !== '' ) : ?>
					<span class="skema-landing-avance-obra__sep" aria-hidden="true"> · </span>
					<?php endif; ?>
					<?php if ( $skema_av_fecha_t !== '' && $skema_av_fecha_i !== '' ) : ?>
					<time class="skema-landing-avance-obra__fecha" datetime="<?php echo esc_attr( $skema_av_fecha_i ); ?>"><?php echo esc_html( $skema_av_fecha_t ); ?></time>
					<?php elseif ( $skema_av_fecha_t !== '' ) : ?>
					<span class="skema-landing-avance-obra__fecha"><?php echo esc_html( $skema_av_fecha_t ); ?></span>
					<?php endif; ?>
				</p>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<div id="<?php echo esc_attr( $skema_avance_nav_id ); ?>" class="plantas-slider-nav">
		<?php foreach ( $avance_obra_slides as $skema_av_slide ) : ?>
			<?php
			$skema_av_media  = isset( $skema_av_slide['media'] ) ? (string) $skema_av_slide['media'] : 'image';
			$skema_av_thumb   = isset( $skema_av_slide['thumb'] ) ? trim( (string) $skema_av_slide['thumb'] ) : '';
			$skema_av_src     = isset( $skema_av_slide['src'] ) ? trim( (string) $skema_av_slide['src'] ) : '';
			$skema_av_iframe  = isset( $skema_av_slide['iframe_src'] ) ? trim( (string) $skema_av_slide['iframe_src'] ) : '';

			if ( $skema_av_media === 'youtube' ) {
				if ( $skema_av_iframe === '' ) {
					continue;
				}
			} elseif ( $skema_av_src === '' ) {
				continue;
			}

			$skema_av_nav_use_fallback = ( $skema_av_media === 'video' && $skema_av_thumb === '' );
			$skema_av_nav_src          = $skema_av_thumb !== '' ? $skema_av_thumb : $skema_av_src;
			?>
		<div class="plantas-nav-slide">
			<div class="plantas-nav-inner<?php echo $skema_av_nav_use_fallback ? ' plantas-nav-inner--video-fallback' : ''; ?>">
				<?php if ( $skema_av_nav_use_fallback ) : ?>
				<span class="skema-landing-avance-obra__nav-icon" aria-hidden="true">
					<i class="fas fa-play"></i>
				</span>
				<span class="screen-reader-text"><?php esc_html_e( 'Vídeo', 'theme_skema' ); ?></span>
				<?php else : ?>
				<img
					src="<?php echo esc_url( $skema_av_nav_src ); ?>"
					alt=""
					role="presentation"
					decoding="async"
					loading="lazy"
				>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
