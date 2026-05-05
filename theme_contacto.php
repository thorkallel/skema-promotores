<?php
/**
 * Template Name: Contacto
 **/
get_header(); ?>
<main id="primary" class="site-main">
<section class="banner-nosotros pt-sm-4 mt-5 pt-4">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-0">
                <?php if (has_post_thumbnail()) : ?>                   
                    <?php the_post_thumbnail('full', ['class' => 'img-fluid w-100 d-none d-sm-block']); ?>
                <?php endif; ?>
                <?php $banner_movil = get_field('banner_movil');
                if ($banner_movil) : ?>
                    <img src="<?php echo esc_url($banner_movil); ?>"
                        alt="<?php echo esc_attr( sprintf( __( 'Banner de %s', 'theme_skema' ), get_the_title() ) ); ?>"
                        class="img-fluid d-sm-none d-block w-100">
                <?php endif; ?> 
                <div class="card-img-overlay p-sm-5 px-0 d-sm-flex align-items-center justify-content-end w-100">
                    <div class="col-sm-6 col-12"></div>
                    <div class="col-sm-6 col-12 mt-sm-5 mt-4">
                        <div class="text-content text-center pt-sm-5 pr-sm-5 mr-sm-5">
                        <h1><?php the_title(); ?></h1>
                        <?php the_content(); ?>   
                    </div>  
                </div>
                
            </div>
        </div>
    </div>
</section>
<section class="contacto p-sm-5 mb-sm-5 px-4" id="info">
    <div class="container pt-sm-4">
        <div class="row px-sm-3">			
		    <div class="col-sm-6 col-12 pt-sm-5 pr-sm-5 pl-sm-4 d-sm-flex flex-column justify-content-between ">
				<div class="info mb-sm-0 mb-4">
                    <h2 class="mb-sm-4 d-none d-sm-block"><?php the_field('tt_cone') ?></h2>
                    <p><?php the_field('desc_cono') ?></p>
                </div>
                <div class="card info-redes p-4 mb-sm-0 mb-4">
                    <div class="logo-info-skema py-3">
                        <?php the_custom_logo(); ?>
                    </div>
                    <div class="redes-info py-3">
                         <a href="<?php the_field('face', 'option') ?>" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr__( 'Facebook', 'theme_skema' ); ?>"><i class="bi bi-facebook"></i></a>
				        <a href="<?php the_field('inst', 'option') ?>" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr__( 'Instagram', 'theme_skema' ); ?>"><i class="bi bi-instagram"></i></a>
				        <a href="https://wa.me/<?php the_field('wp', 'option') ?>" target="_blank" rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr__( 'Contactar por WhatsApp', 'theme_skema' ); ?>"><i class="bi bi-whatsapp"></i></a>
                        <a href="tel:<?php the_field('tele', 'option') ?>"
                            aria-label="<?php echo esc_attr__( 'Llamar por teléfono', 'theme_skema' ); ?>"><i class="bi bi-phone"></i></a>
                    </div>
                </div>
			</div>
            <div class="col-sm-6 col-12 pl-sm-5 pr-sm-4 mb-sm-0 mb-5">
                <div class="card formu px-sm-3 pt-4 pt-sm-0">
				    <h3 class="pl-sm-4 pt-sm-4 mb-sm-3 mb-4">Contacto</h3>
                    <?php echo do_shortcode('[contact-form-7 id="d3f6ebb" title="Formulario Contacto"]'); ?>
                </div>
			</div>
		</div>
	</div>
</section>
</main>
<?php get_footer(); ?>