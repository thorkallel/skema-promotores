<?php
/**
 * Landings: slider Slick «Plantas» (vista principal + carril de miniaturas).
 *
 * Referencia: renvia/landing-static.html (plantas-slider-wrap + asNavFor).
 *
 * Variables (require desde landing-content-box.php):
 * @var array<int, array{src: string, alt: string, caption: string, thumb?: string}> $plantas_slides
 * @var string                                                                       $plantas_id_prefix Prefijo único por post (ej. skema-lland-123).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $plantas_slides ) || ! is_array( $plantas_slides ) || count( $plantas_slides ) === 0 ) {
	return;
}

if ( ! isset( $plantas_id_prefix ) || ! is_string( $plantas_id_prefix ) || $plantas_id_prefix === '' ) {
	return;
}

$skema_plantas_main_id = $plantas_id_prefix . '-plantas-main';
$skema_plantas_nav_id  = $plantas_id_prefix . '-plantas-nav';
?>
<div
	class="plantas-slider-wrap mb-30 skema-landing-plantas-wrap skema-landing-slot skema-landing-slot--plantas"
	data-skema-plantas="1"
>
	<div id="<?php echo esc_attr( $skema_plantas_main_id ); ?>" class="plantas-slider-main renvia-image">
		<?php foreach ( $plantas_slides as $skema_pl_slide ) : ?>
		<div class="plantas-slide">
			<div class="plantas-slide-inner">
				<img
					src="<?php echo esc_url( $skema_pl_slide['src'] ); ?>"
					alt="<?php echo esc_attr( $skema_pl_slide['alt'] ); ?>"
					loading="lazy"
					decoding="async"
				>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<div id="<?php echo esc_attr( $skema_plantas_nav_id ); ?>" class="plantas-slider-nav">
		<?php foreach ( $plantas_slides as $skema_pl_slide ) : ?>
			<?php
			$skema_pl_thumb = isset( $skema_pl_slide['thumb'] ) && is_string( $skema_pl_slide['thumb'] ) && $skema_pl_slide['thumb'] !== ''
				? $skema_pl_slide['thumb']
				: $skema_pl_slide['src'];
			?>
		<div class="plantas-nav-slide">
			<div class="plantas-nav-inner">
				<img
					src="<?php echo esc_url( $skema_pl_thumb ); ?>"
					alt=""
					role="presentation"
					decoding="async"
					loading="lazy"
				>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
