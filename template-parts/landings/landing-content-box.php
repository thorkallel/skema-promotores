<?php
/**
 * Landing fase completa: caja de contenido tipo Renvia (.content-box + grid 8/4).
 *
 * Convención: ningún bloque muestra título (h3/h5) ni envoltorio si no hay datos
 * en sus campos ACF. Los flags `skema_show_*` deben enlazarse a ACF al crear cada bloque.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

$post_id = (int) get_the_ID();
if ( $post_id <= 0 ) {
	return;
}

$skema_lland_tab = 'skema-lland-' . $post_id;

$skema_lland_cf7 = '';
if ( function_exists( 'get_field' ) && function_exists( 'theme_skema_sanitize_cf7_shortcode' ) ) {
	$skema_lland_cf7_raw = get_field( 'skema_lland_lead_cf7', $post_id );
	$skema_lland_cf7     = theme_skema_sanitize_cf7_shortcode( is_string( $skema_lland_cf7_raw ) ? $skema_lland_cf7_raw : '' );
}

$skema_lland_show_lead_sidebar = ( $skema_lland_cf7 !== '' );

// Títulos de bloque: campo texto opcional en ACF o texto por defecto (traducible).
$skema_section_title = function ( $acf_field, $fallback ) use ( $post_id ) {
	if ( ! function_exists( 'theme_skema_landing_get_section_title_or_default' ) ) {
		return $fallback;
	}

	return theme_skema_landing_get_section_title_or_default( $post_id, $acf_field, $fallback );
};

$skema_desc_proyecto_raw = '';
$skema_desc_inmueble_raw = '';
$skema_zonas_intro_raw = '';

if ( function_exists( 'get_field' ) ) {
	$skema_desc_proyecto_raw = get_field( 'skema_lland_desc_proyecto', $post_id, false );
	$skema_desc_proyecto_raw = is_string( $skema_desc_proyecto_raw ) ? $skema_desc_proyecto_raw : '';
	$skema_desc_inmueble_raw = get_field( 'skema_lland_desc_inmueble', $post_id, false );
	$skema_desc_inmueble_raw = is_string( $skema_desc_inmueble_raw ) ? $skema_desc_inmueble_raw : '';
	$skema_zonas_intro_raw   = get_field( 'skema_lland_zonas_intro', $post_id, false );
	$skema_zonas_intro_raw   = is_string( $skema_zonas_intro_raw ) ? $skema_zonas_intro_raw : '';
}

/*
 * Zonas sociales — lista: texto + opcional FA (inc/landings-ficha-apartamento-fa.php).
 */
$skema_zonas_items = function_exists( 'theme_skema_landing_get_zonas_sociales_items_for_display' )
	? theme_skema_landing_get_zonas_sociales_items_for_display( $post_id )
	: array();

$skema_has_wysiwyg = function_exists( 'theme_skema_landing_acf_wysiwyg_has_content' );

$skema_show_desc_proyecto = $skema_has_wysiwyg && theme_skema_landing_acf_wysiwyg_has_content( $skema_desc_proyecto_raw );
$skema_show_desc_inmueble = $skema_has_wysiwyg && theme_skema_landing_acf_wysiwyg_has_content( $skema_desc_inmueble_raw );
$skema_show_zonas         = ( $skema_has_wysiwyg && theme_skema_landing_acf_wysiwyg_has_content( $skema_zonas_intro_raw ) )
	|| count( $skema_zonas_items ) > 0;

/*
 * Ficha del apartamento: ACF repetidor anidado + Font Awesome (ver inc/landings-ficha-apartamento-fa.php).
 * Sin categorías con ítems válidos ⇒ sin título ni bloque.
 */
$skema_ficha_apto_categorias = function_exists( 'theme_skema_landing_get_ficha_apartamento_categorias_for_display' )
	? theme_skema_landing_get_ficha_apartamento_categorias_for_display( $post_id )
	: array();
$skema_show_ficha            = count( $skema_ficha_apto_categorias ) > 0;

$skema_info_proyecto_rows = function_exists( 'theme_skema_landing_get_info_proyecto_rows_for_display' )
	? theme_skema_landing_get_info_proyecto_rows_for_display( $post_id )
	: array();
$skema_show_info          = count( $skema_info_proyecto_rows ) > 0;
$skema_info_allowed_html  = function_exists( 'theme_skema_landing_info_proyecto_allowed_html' )
	? theme_skema_landing_info_proyecto_allowed_html()
	: array();
$skema_medios_apto_slides = function_exists( 'theme_skema_landing_medios_get_apto_slides' )
	? theme_skema_landing_medios_get_apto_slides( $post_id )
	: array();
$skema_medios_renders_slides = function_exists( 'theme_skema_landing_medios_get_renders_slides' )
	? theme_skema_landing_medios_get_renders_slides( $post_id )
	: array();
$skema_medios_plantas_slides = function_exists( 'theme_skema_landing_medios_get_plantas_slides' )
	? theme_skema_landing_medios_get_plantas_slides( $post_id )
	: array();
$skema_medios_avance_slides = function_exists( 'theme_skema_landing_medios_get_avance_obra_slides' )
	? theme_skema_landing_medios_get_avance_obra_slides( $post_id )
	: array();
$skema_medios_video = function_exists( 'theme_skema_landing_medios_get_video_for_display' )
	? theme_skema_landing_medios_get_video_for_display( $post_id )
	: null;
$skema_zonas_galeria_items = function_exists( 'theme_skema_landing_medios_get_zonas_galeria_items' )
	? theme_skema_landing_medios_get_zonas_galeria_items( $post_id )
	: array();
$skema_zonas_galeria_nota  = function_exists( 'theme_skema_landing_medios_get_zonas_galeria_nota' )
	? theme_skema_landing_medios_get_zonas_galeria_nota( $post_id )
	: '';
$skema_medios_maqueta = function_exists( 'theme_skema_landing_medios_get_maqueta_web_for_display' )
	? theme_skema_landing_medios_get_maqueta_web_for_display( $post_id )
	: null;
$skema_show_medios       = function_exists( 'theme_skema_landing_medios_should_show_block' )
	&& theme_skema_landing_medios_should_show_block( $post_id );
$skema_respaldo_items = function_exists( 'theme_skema_landing_get_respaldo_slider_items' )
	? theme_skema_landing_get_respaldo_slider_items( $post_id )
	: array();
$skema_show_aliados = count( $skema_respaldo_items ) > 0;
$skema_brochure_cta = function_exists( 'theme_skema_landing_get_brochure_cta_data' )
	? theme_skema_landing_get_brochure_cta_data( $post_id )
	: null;
$skema_show_respaldo_section = $skema_show_aliados || null !== $skema_brochure_cta;

$skema_mapa_embed_html = function_exists( 'theme_skema_landing_get_google_maps_embed_html' )
	? theme_skema_landing_get_google_maps_embed_html( $post_id )
	: '';
$skema_show_mapa = $skema_mapa_embed_html !== '';

$skema_mapa_titulo = '';
if ( $skema_show_mapa ) {
	$skema_mapa_titulo = $skema_section_title( 'skema_lland_mapa_titulo', __( 'Ubicación', 'theme_skema' ) );
}

$skema_main_has_any = $skema_show_desc_proyecto
	|| $skema_show_desc_inmueble
	|| $skema_show_zonas
	|| $skema_show_ficha
	|| $skema_show_info
	|| $skema_show_medios
	|| $skema_show_respaldo_section
	|| $skema_show_mapa;

if ( ! $skema_main_has_any && ! $skema_lland_show_lead_sidebar ) {
	return;
}

$skema_main_col_class = '';
if ( $skema_main_has_any ) {
	$skema_main_col_class = $skema_lland_show_lead_sidebar ? 'col-xl-8' : 'col-12';
}

$skema_sidebar_col_class = '';
if ( $skema_lland_show_lead_sidebar ) {
	$skema_sidebar_col_class = $skema_main_has_any ? 'col-xl-4' : 'col-12';
}

$skema_content_section_heading_class = static function ( $has_prior_block ) {
	return $has_prior_block ? 'mt-40' : '';
};

$skema_prior_content_block = false;

?>
<div class="skema-landing-content-box-wrap project-details-wrapper">
    <div class="content-box">
        <div class="row">
            <?php if ( $skema_main_has_any ) : ?>
            <div class="<?php echo esc_attr( $skema_main_col_class ); ?> skema-landing-content-box__main">
                <?php if ( $skema_show_desc_proyecto ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_desc_proyecto', __( 'Descripción del proyecto', 'theme_skema' ) ) ); ?>
                </h3>
                <div class="skema-landing-slot skema-landing-slot--descripcion-proyecto entry-content">
                    <?php echo apply_filters( 'the_content', $skema_desc_proyecto_raw ); ?>
                </div>
                <?php
					$skema_prior_content_block = true;
				endif;
				?>

                <?php if ( $skema_show_desc_inmueble ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_desc_inmueble', __( 'Descripción del inmueble (tipología)', 'theme_skema' ) ) ); ?>
                </h3>
                <div class="skema-landing-slot skema-landing-slot--descripcion-inmueble entry-content">
                    <?php echo apply_filters( 'the_content', $skema_desc_inmueble_raw ); ?>
                </div>
                <?php
					$skema_prior_content_block = true;
				endif;
				?>

                <?php if ( $skema_show_zonas ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_zonas_sociales', __( 'Zonas sociales', 'theme_skema' ) ) ); ?>
                </h3>
                <?php if ( $skema_has_wysiwyg && theme_skema_landing_acf_wysiwyg_has_content( $skema_zonas_intro_raw ) ) : ?>
                <div class="skema-landing-slot skema-landing-slot--zonas-sociales-intro entry-content">
                    <?php echo apply_filters( 'the_content', $skema_zonas_intro_raw ); ?>
                </div>
                <?php endif; ?>
                <?php if ( count( $skema_zonas_items ) > 0 ) : ?>
                <div
                    class="features-amenities-list project-ficha-grid project-ficha-grid--fa-cats mb-5 mb-xl-0 skema-landing-slot skema-landing-slot--zonas-sociales-list"
                    role="list">
                    <?php foreach ( $skema_zonas_items as $skema_zona_item ) : ?>
                    <div class="skema-ficha-apto-category" role="listitem">
                        <div class="skema-ficha-apto-category__items">
                            <div class="skema-ficha-apto-item">
                                <?php if ( $skema_zona_item['fa_classes'] !== '' ) : ?>
                                <span class="skema-ficha-apto-item__icon" aria-hidden="true">
                                    <i class="<?php echo esc_attr( $skema_zona_item['fa_classes'] ); ?>"></i>
                                </span>
                                <?php endif; ?>
                                <span class="skema-ficha-apto-item__text"><?php echo esc_html( $skema_zona_item['texto'] ); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php
					$skema_prior_content_block = true;
				endif;
				?>

                <?php if ( $skema_show_ficha ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_ficha_apartamento', __( 'Ficha del apartamento', 'theme_skema' ) ) ); ?>
                </h3>
                <div
                    class="features-amenities-list project-ficha-grid project-ficha-grid--fa-cats mb-5 mb-xl-0 skema-landing-slot skema-landing-slot--ficha-apartamento">
                    <?php foreach ( $skema_ficha_apto_categorias as $skema_ficha_cat ) : ?>
                    <div class="skema-ficha-apto-category">
                        <h4 class="skema-ficha-apto-category__title">
                            <?php echo esc_html( $skema_ficha_cat['titulo'] ); ?></h4>
                        <div class="skema-ficha-apto-category__items">
                            <?php foreach ( $skema_ficha_cat['items'] as $skema_ficha_item ) : ?>
                            <div class="skema-ficha-apto-item">
                                <?php if ( $skema_ficha_item['fa_classes'] !== '' ) : ?>
                                <span class="skema-ficha-apto-item__icon" aria-hidden="true">
                                    <i class="<?php echo esc_attr( $skema_ficha_item['fa_classes'] ); ?>"></i>
                                </span>
                                <?php endif; ?>
                                <span
                                    class="skema-ficha-apto-item__text"><?php echo esc_html( $skema_ficha_item['texto'] ); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php $skema_prior_content_block = true; ?>
                <?php endif; ?>

                <?php if ( $skema_show_info ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_info_proyecto', __( 'Información del proyecto', 'theme_skema' ) ) ); ?>
                </h3>
                <div class="project-info-box mb-5 mb-xl-0 skema-landing-slot skema-landing-slot--info-proyecto">
                    <ul class="skema-lland-info-proyecto-list">
                        <?php foreach ( $skema_info_proyecto_rows as $skema_info_row ) : ?>
                        <li>
                            <?php echo esc_html( $skema_info_row['etiqueta'] ); ?>:<span
                                class="skema-lland-info-proyecto-list__valor"><?php echo wp_kses( $skema_info_row['valor'], $skema_info_allowed_html ); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php $skema_prior_content_block = true; ?>
                <?php endif; ?>

                <?php if ( $skema_show_medios ) : ?>
                <h3
                    class="<?php echo esc_attr( $skema_content_section_heading_class( $skema_prior_content_block ) ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_medios', __( 'Medios del proyecto', 'theme_skema' ) ) ); ?>
                </h3>
                <div class="property-media-box mt-40 skema-landing-slot skema-landing-slot--medios">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="property-tabs mb-40">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-apto"
                                            role="tab" aria-selected="true">
                                            <?php esc_html_e( 'Apto modelo', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-renders"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Renders', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-plantas"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Plantas', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-avance-obra"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Avance de obra', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-videos"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Video', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-zonas"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Zonas sociales', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <?php if ( null !== $skema_medios_maqueta ) : ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" type="button" data-toggle="tab"
                                            data-target="#<?php echo esc_attr( $skema_lland_tab ); ?>-tab-maqueta"
                                            role="tab" aria-selected="false" tabindex="-1">
                                            <?php esc_html_e( 'Maqueta web', 'theme_skema' ); ?>
                                        </button>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-apto" role="tabpanel">
                            <?php
							$slides        = $skema_medios_apto_slides;
							$modal_id      = $skema_lland_tab . '-gallery-apto';
							$modal_title   = __( 'Galería — Apto modelo', 'theme_skema' );
							$gallery_kind  = 'apto';
							$skema_medios_lb_tpl = locate_template( 'template-parts/landings/partials/medios-gallery-lightbox.php' );
							if ( is_string( $skema_medios_lb_tpl ) && $skema_medios_lb_tpl !== '' ) {
								require $skema_medios_lb_tpl;
							}
							unset( $slides, $modal_id, $modal_title, $gallery_kind, $skema_medios_lb_tpl );
							?>
                        </div>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-renders" role="tabpanel">
                            <?php
							$slides        = $skema_medios_renders_slides;
							$modal_id      = $skema_lland_tab . '-gallery-renders';
							$modal_title   = __( 'Galería — Renders', 'theme_skema' );
							$gallery_kind  = 'renders';
							$skema_medios_lb_tpl = locate_template( 'template-parts/landings/partials/medios-gallery-lightbox.php' );
							if ( is_string( $skema_medios_lb_tpl ) && $skema_medios_lb_tpl !== '' ) {
								require $skema_medios_lb_tpl;
							}
							unset( $slides, $modal_id, $modal_title, $gallery_kind, $skema_medios_lb_tpl );
							?>
                        </div>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-plantas" role="tabpanel">
                            <?php
							$plantas_slides      = $skema_medios_plantas_slides;
							$plantas_id_prefix   = $skema_lland_tab;
							$skema_plantas_tpl   = locate_template( 'template-parts/landings/partials/medios-plantas-slider.php' );
							if ( is_string( $skema_plantas_tpl ) && $skema_plantas_tpl !== '' ) {
								require $skema_plantas_tpl;
							}
							unset( $plantas_slides, $plantas_id_prefix, $skema_plantas_tpl );
							?>
                        </div>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-avance-obra" role="tabpanel">
                            <?php
							$avance_obra_slides = $skema_medios_avance_slides;
							$avance_id_prefix   = $skema_lland_tab;
							$skema_avance_tpl   = locate_template( 'template-parts/landings/partials/medios-avance-obra-slider.php' );
							if ( is_string( $skema_avance_tpl ) && $skema_avance_tpl !== '' ) {
								require $skema_avance_tpl;
							}
							unset( $avance_obra_slides, $avance_id_prefix, $skema_avance_tpl );
							?>
                        </div>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-videos" role="tabpanel">
                            <?php
							$skema_medios_video_tpl = locate_template( 'template-parts/landings/partials/medios-tab-video.php' );
							if ( is_string( $skema_medios_video_tpl ) && $skema_medios_video_tpl !== '' ) {
								require $skema_medios_video_tpl;
							}
							unset( $skema_medios_video_tpl, $skema_medios_video );
							?>
                        </div>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-zonas" role="tabpanel">
                            <?php
							$skema_zonas_galeria_tpl = locate_template( 'template-parts/landings/partials/medios-zonas-sociales-grid.php' );
							if ( is_string( $skema_zonas_galeria_tpl ) && $skema_zonas_galeria_tpl !== '' ) {
								require $skema_zonas_galeria_tpl;
							}
							unset( $skema_zonas_galeria_tpl, $skema_zonas_galeria_items, $skema_zonas_galeria_nota );
							?>
                        </div>
                        <?php if ( null !== $skema_medios_maqueta ) : ?>
                        <div class="tab-pane fade skema-landing-tab-pane"
                            id="<?php echo esc_attr( $skema_lland_tab ); ?>-tab-maqueta" role="tabpanel">
                            <?php
							$skema_maqueta_tpl = locate_template( 'template-parts/landings/partials/medios-tab-maqueta-web.php' );
							if ( is_string( $skema_maqueta_tpl ) && $skema_maqueta_tpl !== '' ) {
								require $skema_maqueta_tpl;
							}
							unset( $skema_maqueta_tpl );
							?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <p class="skema-landing-medios-legal-disclaimer" role="note">
                        <?php
						echo esc_html(
							__( '*Los apartamentos se entregan en obra gris. Precios sujetos a cambio sin previo aviso. Las imágenes, diseños, áreas y precios son únicamente de referencia, están sujetos a modificaciones en el proceso de coordinación técnica y el desarrollo arquitectónico y constructivo. Inventario sujeto a disponibilidad.', 'theme_skema' )
						);
						?>
                    </p>
                </div>
                <?php $skema_prior_content_block = true; ?>
                <?php endif; ?>

                <?php if ( $skema_show_respaldo_section ) : ?>
                <?php if ( $skema_show_aliados ) : ?>
                <?php
					$skema_aliados_h3_class = $skema_content_section_heading_class( $skema_prior_content_block );
					$skema_aliados_h3_class = $skema_aliados_h3_class === '' ? 'mb-70' : $skema_aliados_h3_class . ' mb-70';
					?>
                <h3 class="<?php echo esc_attr( $skema_aliados_h3_class ); ?>">
                    <?php echo esc_html( $skema_section_title( 'skema_lland_titulo_respaldo', __( 'Con el respaldo de:', 'theme_skema' ) ) ); ?>
                </h3>
                <?php endif; ?>
                <div class="property-media-box mt-40 mb-60 skema-landing-slot skema-landing-slot--aliados">
                    <section class="renvia-choose-sec">
                        <div class="container-fluid px-0">
                            <div class="clients-wrapper pt-20">
                                <?php if ( $skema_show_aliados ) : ?>
                                <?php
								$skema_respaldo_tpl = locate_template( 'template-parts/landings/partials/landing-respaldo-slider.php' );
								if ( is_string( $skema_respaldo_tpl ) && $skema_respaldo_tpl !== '' ) {
									require $skema_respaldo_tpl;
								}
								unset( $skema_respaldo_tpl, $skema_respaldo_items );
								?>
                                <?php endif; ?>
                                <?php
								if ( null !== $skema_brochure_cta ) {
									$skema_brochure_tpl = locate_template( 'template-parts/landings/partials/landing-brochure-cta.php' );
									if ( is_string( $skema_brochure_tpl ) && $skema_brochure_tpl !== '' ) {
										require $skema_brochure_tpl;
									}
									unset( $skema_brochure_tpl, $skema_brochure_cta );
								}
								?>
                            </div>
                        </div>
                    </section>
                </div>
                <?php $skema_prior_content_block = true; ?>
                <?php endif; ?>

                <?php if ( $skema_show_mapa ) : ?>
                <?php
					$skema_mapa_h3_class = $skema_content_section_heading_class( $skema_prior_content_block );
					$skema_mapa_h3_class = $skema_mapa_h3_class === '' ? 'mb-40' : $skema_mapa_h3_class . ' mb-40';
					?>
                <h3 class="<?php echo esc_attr( $skema_mapa_h3_class ); ?>">
                    <?php echo esc_html( $skema_mapa_titulo ); ?>
                </h3>
                <div
                    class="skema-landing-slot skema-landing-slot--mapa property-media-box mt-0 mb-60 skema-landing-mapa-wrap">
                    <div class="skema-landing-mapa-ratio">
                        <?php
						if ( function_exists( 'theme_skema_landing_google_maps_iframe_allowed_html' ) ) {
							echo wp_kses( $skema_mapa_embed_html, theme_skema_landing_google_maps_iframe_allowed_html() );
						}
						?>
                    </div>
                </div>
                <?php $skema_prior_content_block = true; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ( $skema_lland_show_lead_sidebar ) : ?>
            <div class="<?php echo esc_attr( $skema_sidebar_col_class ); ?> project-lead-sidebar">
                <div class="project-info-box project-info-box--lead mb-5 mb-xl-0 skema-landing-slot skema-landing-slot--lead"
                    id="solicitar-informacion">
                    <h5><?php esc_html_e( 'Solicitar información', 'theme_skema' ); ?></h5>
                    <div class="skema-landing-cf7-wrap project-lead-form">
                        <?php echo do_shortcode( $skema_lland_cf7 ); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>