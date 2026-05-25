<?php
/**
 * Landings: pestaña «Maqueta web» (iframe con URL de recorrido virtual).
 *
 * Variables (require desde landing-content-box.php):
 * @var array{iframe_src: string, title: string}|null $skema_medios_maqueta
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $skema_medios_maqueta ) || ! is_array( $skema_medios_maqueta ) ) {
	return;
}

$skema_mq_src = isset( $skema_medios_maqueta['iframe_src'] ) && is_string( $skema_medios_maqueta['iframe_src'] )
	? trim( $skema_medios_maqueta['iframe_src'] )
	: '';

if ( $skema_mq_src === '' ) {
	return;
}

$skema_mq_title = isset( $skema_medios_maqueta['title'] ) && is_string( $skema_medios_maqueta['title'] ) && $skema_medios_maqueta['title'] !== ''
	? $skema_medios_maqueta['title']
	: __( 'Recorrido virtual del proyecto', 'theme_skema' );
?>
<div class="row">
	<div class="col-lg-12">
		<div class="renvia-image video-image mb-30 skema-landing-slot skema-landing-slot--medios-maqueta skema-landing-medios-maqueta">
			<div class="embed-responsive embed-responsive-16by9 skema-landing-medios-maqueta__ratio">
				<iframe
					class="embed-responsive-item skema-landing-medios-maqueta__iframe"
					src="<?php echo esc_url( $skema_mq_src ); ?>"
					title="<?php echo esc_attr( $skema_mq_title ); ?>"
					loading="lazy"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; xr-spatial-tracking"
					allowfullscreen
					referrerpolicy="no-referrer-when-downgrade"
				></iframe>
			</div>
		</div>
	</div>
</div>
