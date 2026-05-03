<?php
/**
 * Carrusel Slick «Con el respaldo de» (Renvia: clients-slider + renvia-client-item).
 *
 * Variables:
 * @var array<int, array{texto: string, src: string, alt: string}> $skema_respaldo_items
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $skema_respaldo_items ) || ! is_array( $skema_respaldo_items ) || count( $skema_respaldo_items ) === 0 ) {
	return;
}
?>
<div class="clients-slider skema-landing-aliados-slider skema-landing-slot skema-landing-slot--aliados-slider">
	<?php foreach ( $skema_respaldo_items as $skema_respaldo_row ) : ?>
	<div class="renvia-client-item">
		<p class="client-slider-item-title"><?php echo esc_html( $skema_respaldo_row['texto'] ); ?></p>
		<div class="client-img renvia-image">
			<img
				src="<?php echo esc_url( $skema_respaldo_row['src'] ); ?>"
				alt="<?php echo esc_attr( $skema_respaldo_row['alt'] ); ?>"
				loading="lazy"
				decoding="async"
			>
		</div>
	</div>
	<?php endforeach; ?>
</div>
