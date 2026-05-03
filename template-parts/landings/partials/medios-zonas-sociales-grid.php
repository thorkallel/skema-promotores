<?php
/**
 * Landings: pestaña «Zonas sociales» — rejilla estática (sin modal ni overlay).
 *
 * Variables (require desde landing-content-box.php):
 * @var array<int, array{src: string, alt: string, titulo: string}> $skema_zonas_galeria_items
 * @var string                                                     $skema_zonas_galeria_nota Nota opcional (texto plano).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $skema_zonas_galeria_items ) || ! is_array( $skema_zonas_galeria_items ) || count( $skema_zonas_galeria_items ) === 0 ) {
	return;
}

$skema_zonas_nota = isset( $skema_zonas_galeria_nota ) && is_string( $skema_zonas_galeria_nota ) ? trim( $skema_zonas_galeria_nota ) : '';
?>
<div
	class="row row-cols-2 row-cols-lg-6 g-3 zonas-sociales-gallery mb-30 skema-landing-slot skema-landing-slot--galeria-zonas skema-landing-zonas-galeria"
>
	<?php foreach ( $skema_zonas_galeria_items as $skema_zg_item ) : ?>
	<div class="col">
		<figure class="skema-landing-zonas-galeria__item">
			<div class="skema-landing-zonas-galeria__media renvia-image">
				<img
					src="<?php echo esc_url( $skema_zg_item['src'] ); ?>"
					alt="<?php echo esc_attr( $skema_zg_item['alt'] ); ?>"
					loading="lazy"
					decoding="async"
				>
			</div>
			<?php if ( isset( $skema_zg_item['titulo'] ) && is_string( $skema_zg_item['titulo'] ) && $skema_zg_item['titulo'] !== '' ) : ?>
			<figcaption class="skema-landing-zonas-galeria__caption">
				<?php echo esc_html( $skema_zg_item['titulo'] ); ?>
			</figcaption>
			<?php endif; ?>
		</figure>
	</div>
	<?php endforeach; ?>
</div>
<?php if ( $skema_zonas_nota !== '' ) : ?>
<p class="small text-muted mb-0 skema-landing-slot skema-landing-slot--zonas-nota skema-landing-zonas-galeria__nota">
	<?php echo esc_html( $skema_zonas_nota ); ?>
</p>
<?php endif; ?>
