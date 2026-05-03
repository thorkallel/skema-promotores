<?php
/**
 * Landings: pestaña «Videos» (YouTube embebido o MP4 de medios).
 *
 * Variables (require desde landing-content-box.php):
 * @var array{type: string, title: string, iframe_src?: string, mp4_url?: string, poster_url?: string}|null $skema_medios_video
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $skema_medios_video ) || ! is_array( $skema_medios_video ) ) {
	return;
}

$skema_v_type = isset( $skema_medios_video['type'] ) ? (string) $skema_medios_video['type'] : '';
if ( $skema_v_type !== 'youtube' && $skema_v_type !== 'mp4' ) {
	return;
}

$skema_v_title = isset( $skema_medios_video['title'] ) && is_string( $skema_medios_video['title'] ) && $skema_medios_video['title'] !== ''
	? $skema_medios_video['title']
	: __( 'Video del proyecto', 'theme_skema' );

$skema_v_iframe = ( $skema_v_type === 'youtube' && isset( $skema_medios_video['iframe_src'] ) && is_string( $skema_medios_video['iframe_src'] ) )
	? trim( $skema_medios_video['iframe_src'] )
	: '';

$skema_v_mp4 = ( $skema_v_type === 'mp4' && isset( $skema_medios_video['mp4_url'] ) && is_string( $skema_medios_video['mp4_url'] ) )
	? trim( $skema_medios_video['mp4_url'] )
	: '';

if ( $skema_v_type === 'youtube' && $skema_v_iframe === '' ) {
	return;
}

if ( $skema_v_type === 'mp4' && $skema_v_mp4 === '' ) {
	return;
}

$skema_v_poster = isset( $skema_medios_video['poster_url'] ) && is_string( $skema_medios_video['poster_url'] )
	? trim( $skema_medios_video['poster_url'] )
	: '';
?>
<div class="row">
	<div class="col-lg-12">
		<div class="renvia-image video-image mb-30 skema-landing-slot skema-landing-slot--videos skema-landing-medios-video">
			<?php if ( $skema_v_type === 'youtube' ) : ?>
			<div class="embed-responsive embed-responsive-16by9 skema-landing-medios-video__ratio">
				<iframe
					class="embed-responsive-item skema-landing-medios-video__iframe"
					src="<?php echo esc_url( $skema_v_iframe ); ?>"
					title="<?php echo esc_attr( $skema_v_title ); ?>"
					loading="lazy"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen
				></iframe>
			</div>
			<?php else : ?>
			<div class="embed-responsive embed-responsive-16by9 skema-landing-medios-video__ratio">
				<video
					class="embed-responsive-item skema-landing-medios-video__html5"
					controls
					playsinline
					preload="metadata"
					<?php if ( $skema_v_poster !== '' ) : ?>
					poster="<?php echo esc_url( $skema_v_poster ); ?>"
					<?php endif; ?>
				>
					<source src="<?php echo esc_url( $skema_v_mp4 ); ?>" type="video/mp4">
					<?php esc_html_e( 'Tu navegador no reproduce video HTML5.', 'theme_skema' ); ?>
				</video>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
