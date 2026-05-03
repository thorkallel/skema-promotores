<?php
/**
 * Botón centrado «Descargar brochure» (Renvia: theme-btn style-one + PDF).
 *
 * Variables:
 * @var array{url: string, label: string, download: string}|null $skema_brochure_cta
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $skema_brochure_cta ) || ! is_array( $skema_brochure_cta ) ) {
	return;
}

$skema_bc_url = isset( $skema_brochure_cta['url'] ) && is_string( $skema_brochure_cta['url'] ) ? trim( $skema_brochure_cta['url'] ) : '';
if ( $skema_bc_url === '' ) {
	return;
}

$skema_bc_label = isset( $skema_brochure_cta['label'] ) && is_string( $skema_brochure_cta['label'] ) && $skema_brochure_cta['label'] !== ''
	? $skema_brochure_cta['label']
	: __( 'Descargar brochure', 'theme_skema' );

$skema_bc_download = isset( $skema_brochure_cta['download'] ) && is_string( $skema_brochure_cta['download'] ) && $skema_brochure_cta['download'] !== ''
	? $skema_brochure_cta['download']
	: 'brochure.pdf';
?>
<div class="text-center mb-3 mb-md-4 skema-landing-slot skema-landing-slot--brochure-cta">
	<a
		href="<?php echo esc_url( $skema_bc_url ); ?>"
		class="theme-btn style-one skema-landing-brochure-btn"
		download="<?php echo esc_attr( $skema_bc_download ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<?php echo esc_html( $skema_bc_label ); ?>
		<i class="fa-regular fa-file-pdf" aria-hidden="true"></i>
	</a>
</div>
