<?php
/**
 * Landings: rejilla de imágenes + modal (Apto modelo, Renders, etc.).
 *
 * @package skema
 *
 * Variables (require desde landing-content-box.php):
 * @var array<int, array{src: string, alt: string, caption: string}> $slides
 * @var string                                                       $modal_id
 * @var string                                                       $modal_title
 * @var string                                                       $gallery_kind Slug: apto | renders (clases de fila y slot).
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $slides ) || ! is_array( $slides ) || count( $slides ) === 0 ) {
	return;
}

if ( ! isset( $modal_id ) || ! is_string( $modal_id ) || $modal_id === '' ) {
	return;
}

$skema_gallery_kind = isset( $gallery_kind ) && is_string( $gallery_kind ) && $gallery_kind === 'renders' ? 'renders' : 'apto';

$skema_gallery_row_class = 'project-apto-modelo-gallery';
if ( $skema_gallery_kind === 'renders' ) {
	$skema_gallery_row_class = 'project-renders-gallery';
}

$skema_slot_class = 'skema-landing-slot--galeria-' . $skema_gallery_kind;

$skema_medios_modal_title = isset( $modal_title ) && is_string( $modal_title ) && $modal_title !== ''
	? $modal_title
	: __( 'Galería', 'theme_skema' );

$skema_medios_slides_json = wp_json_encode(
	$slides,
	JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
);
if ( ! is_string( $skema_medios_slides_json ) ) {
	$skema_medios_slides_json = '[]';
}

$skema_medios_total = count( $slides );
?>
<div class="row row-cols-2 row-cols-lg-4 g-3 <?php echo esc_attr( $skema_gallery_row_class ); ?> project-gallery-grid skema-landing-slot <?php echo esc_attr( $skema_slot_class ); ?>">
	<?php foreach ( $slides as $skema_medios_idx => $skema_medios_slide ) : ?>
	<div class="col">
		<div class="renvia-image renvia-gallery-item">
			<button
				type="button"
				class="renvia-gallery-thumb-btn"
				data-toggle="modal"
				data-target="#<?php echo esc_attr( $modal_id ); ?>"
				data-gallery-index="<?php echo (int) $skema_medios_idx; ?>"
				aria-label="<?php echo esc_attr( sprintf( /* translators: 1: current index, 2: total images */ __( 'Ampliar imagen %1$d de %2$d', 'theme_skema' ), $skema_medios_idx + 1, $skema_medios_total ) ); ?>"
			>
				<img
					src="<?php echo esc_url( $skema_medios_slide['src'] ); ?>"
					alt="<?php echo esc_attr( $skema_medios_slide['alt'] ); ?>"
					loading="lazy"
					decoding="async"
				>
				<span class="renvia-gallery-hint" aria-hidden="true"><i class="fas fa-search-plus"></i></span>
			</button>
		</div>
	</div>
	<?php endforeach; ?>
</div>

<div
	class="modal fade skema-landing-gallery-modal"
	id="<?php echo esc_attr( $modal_id ); ?>"
	tabindex="-1"
	aria-labelledby="<?php echo esc_attr( $modal_id ); ?>-label"
	aria-hidden="true"
>
	<script type="application/json" class="skema-landing-gallery-json"><?php echo $skema_medios_slides_json; ?></script>
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content border-0 shadow-lg">
			<div class="modal-header border-0 pb-0">
				<h5 class="modal-title" id="<?php echo esc_attr( $modal_id ); ?>-label"><?php echo esc_html( $skema_medios_modal_title ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo esc_attr__( 'Cerrar', 'theme_skema' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pt-2 pb-3 px-3 position-relative">
				<p class="small text-muted mb-2">
					<span id="<?php echo esc_attr( $modal_id ); ?>-counter">1 / <?php echo (int) $skema_medios_total; ?></span>
				</p>
				<div class="modal-gallery-stage position-relative d-flex align-items-center justify-content-center rounded-2">
					<button
						type="button"
						class="gallery-nav-fab gallery-nav-prev skema-landing-gallery-modal__prev"
						id="<?php echo esc_attr( $modal_id ); ?>-prev"
						aria-label="<?php echo esc_attr__( 'Imagen anterior', 'theme_skema' ); ?>"
					>
						<i class="fas fa-chevron-left" aria-hidden="true"></i>
					</button>
					<img
						src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=="
						alt=""
						class="modal-gallery-img skema-landing-gallery-modal__img"
						id="<?php echo esc_attr( $modal_id ); ?>-img"
						decoding="async"
					>
					<button
						type="button"
						class="gallery-nav-fab gallery-nav-next skema-landing-gallery-modal__next"
						id="<?php echo esc_attr( $modal_id ); ?>-next"
						aria-label="<?php echo esc_attr__( 'Imagen siguiente', 'theme_skema' ); ?>"
					>
						<i class="fas fa-chevron-right" aria-hidden="true"></i>
					</button>
				</div>
				<p class="small text-center text-muted mt-3 mb-0 skema-landing-gallery-modal__caption" id="<?php echo esc_attr( $modal_id ); ?>-caption"></p>
			</div>
		</div>
	</div>
</div>
