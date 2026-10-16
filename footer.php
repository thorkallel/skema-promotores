<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package skema
 */

?>
<footer id="colophon" class="site-footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 col-12 px-0">
                <div class="div-mapa">
                    <?php if (have_rows('into_ofi', 'option')): $c = 0;
                			while (have_rows('into_ofi', 'option')): the_row(); ?>
                    <div class="ciu-<?php echo $c; ?> divContact" style="display:none;">
                        <?php $location = get_sub_field('ubicacion');
    								if( $location ):
    									$city_name = get_sub_field( 'ciudad' );
    									$map_title = is_string( $city_name ) && '' !== trim( $city_name )
    										? sprintf(
											/* translators: %s: city name */
											__( 'Mapa de oficina en %s', 'theme_skema' ),
											$city_name
										)
    										: __( 'Mapa de oficina', 'theme_skema' );
    									$location_html = function_exists( 'theme_skema_accessible_embed_html' )
    										? theme_skema_accessible_embed_html( $location, $map_title )
    										: ( is_string( $location ) ? $location : '' );
    									echo $location_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    								endif; ?>
                    </div>
                    <?php $c++; 
							endwhile;
            			endif; ?>

                </div>
            </div>
            <div class="col-sm-6 col-12 pl-5 p-4">
                <div class="row py-3 pb-4">
                    <div class="col-12">
                        <div class="logo-footer">
                            <?php the_custom_logo(); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-12 pr-5">
                        <p class="h6 mb-1">Contacto en:</p>
                        <hr>
                        <div class="div-selectContact mb-2">
                            <label for="selectContact" class="screen-reader-text">
                                <?php esc_html_e( 'Selecciona la ciudad de contacto', 'theme_skema' ); ?>
                            </label>
                            <select id="selectContact" aria-controls="contacto-telefonos contacto-direcciones">
                                <?php if (have_rows('into_ofi', 'option')): $c = 0;
                					while (have_rows('into_ofi', 'option')): the_row(); ?>
                                <option value="ciu-<?php echo $c; ?>"><?php the_sub_field('ciudad'); ?></option>
                                <?php $c++; 
									endwhile;
            					endif; ?>
                            </select>
                        </div>
                        <p id="contacto-anuncio" class="screen-reader-text" aria-live="polite"></p>

                        <p class="mb-0">Teléfono:</p>
                        <div id="contacto-telefonos" aria-live="polite">
                        <?php if (have_rows('into_ofi', 'option')): $c = 0;
                					while (have_rows('into_ofi', 'option')): the_row();
									$skema_tel_raw  = (string) get_sub_field( 'tele' );
									$skema_tel_href = preg_replace( '/[^0-9+]/', '', $skema_tel_raw );
									?>
                        <div class="ciu-<?php echo $c; ?> divContact" style="display:none;">
                            <p class="mb-0"><u><?php if ( '' !== $skema_tel_href ) : ?><a href="tel:<?php echo esc_attr( $skema_tel_href ); ?>"><?php echo esc_html( $skema_tel_raw ); ?></a><?php else : ?><?php echo esc_html( $skema_tel_raw ); ?><?php endif; ?></u></p>
                        </div>
                        <?php $c++; 
									endwhile;
            					endif; ?>
                        </div>
                        <p class="mb-0">Correo:</p>
                        <p class="mb-3 mb-sm-0"><u><a href="mailto:servicioalcliente@skema.co">servicioalcliente@skema.co</a></u></p>
                    </div>
                    <div class="col-sm-6 col-12 pl-sm-4 pr-sm-5 pb-5 pb-sm-0">
                        <p class="h6 mb-sm-1 mb-0">Dirección:</p>
                        <hr>
                        <div id="contacto-direcciones" aria-live="polite">
                        <?php if (have_rows('into_ofi', 'option')): $c = 0;
                				while (have_rows('into_ofi', 'option')): the_row(); ?>
                        <div class="ciu-<?php echo $c; ?> divContact mb-sm-4" style="display:none;">
                            <p class="mb-5 mb-sm-0"><u><?php the_sub_field('dire'); ?></u></p>
                        </div>
                        <?php $c++; 
								endwhile;
            				endif; ?>
                        </div>
                        <?php
                        $skema_footer_enlace_url  = get_field( 'url_enlace', 'option' );
                        $skema_footer_enlace_text = trim( wp_strip_all_tags( (string) get_field( 'texto_enlace', 'option' ) ) );
                        if ( $skema_footer_enlace_url && '' !== $skema_footer_enlace_text ) :
                        ?>
                        <a class="text-white pdt" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $skema_footer_enlace_url ); ?>">
                            <?php echo esc_html( $skema_footer_enlace_text ); ?>
                            <span class="screen-reader-text"><?php esc_html_e( '(abre en una nueva pestaña)', 'theme_skema' ); ?></span>
                        </a>
                        <?php endif; ?>

                    </div>
                    <div class="copyrights">
                        <!-- 								<a href="https://himalayadigital.co/" target="_blank" class="text-copyrights">
									Diseñado y desarrollado por:
								</a>
								<a href="https://himalayadigital.co/" target="_blank" class="img-logo-himalaya">
									<img class="logo-himalaya" src="<?php echo get_template_directory_uri() ?>/img/logo-himalaya.svg" alt="logo-himalaya-sem" loading="lazy">
								</a> -->
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>