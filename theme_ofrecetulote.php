<?php
/**
 * Template Name: Ofrece tu lote
 **/
get_header(); ?>
<main id="primary" class="site-main">
<section class="banner-nosotros">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 px-0">
                <?php if (has_post_thumbnail()) : ?>                   
                    <?php the_post_thumbnail('full', ['class' => 'img-fluid w-100 d-none d-sm-block']); ?>  
                <?php endif; ?>    
                <?php $banner_movil = get_field('banner_movil');
                if ($banner_movil) : ?>
                    <img src="<?php echo esc_url($banner_movil); ?>" alt="Banner" class="img-fluid d-sm-none d-block w-100">
                <?php endif; ?>             
                <div class="card-img-overlay p-sm-5 d-flex align-items-sm-center align-items-end justify-content-sm-start justify-content-center w-100">
                    <div class="text-content text-white pl-sm-5 pb-5">
                      <h1><?php the_title(); ?></h1>
                    </div>     
                </div>
                
            </div>
        </div>
    </div>
</section>
<section class="contacto p-sm-5 px-4 py-5 mb-sm-5" id="info">
    <div class="container pt-sm-4">
        <div class="row px-sm-3">					    
            <div class="col-sm-12 col-12 pl-sm-5 pr-sm-4">
                <div class="card formu px-sm-3 pt-4 pt-sm-0">
				    <h3 class="pl-sm-4 pt-sm-4 mb-sm-3 mb-4">Contacto</h3>
                    <?php echo do_shortcode('[contact-form-7 id="c091d98" title="Formulario Ofrece tu lote"]'); ?>
                </div>
			</div>
		</div>
        <div class="row justify-content-center mt-5 ico_txt">
            <div class="col-sm-5 px-sm-5 mb-sm-0 mb-4">
                  <?php if( have_rows('icotxt_izq') ): ?>
                        <?php while( have_rows('icotxt_izq') ): the_row(); 
                            $tt = get_sub_field('txt'); 
                            $url_img = get_sub_field('ico'); ?>
                                <div class="card px-sm-4 px-3 pb-sm-4 pb-3 pt-sm-5 pt-4 h-100">
                                    <?php if ($url_img) : ?>
                                        <img src="<?php echo esc_url($url_img); ?>" alt="Icono" class="w-25 mb-4 mx-auto">
                                    <?php endif; ?>
                                    <p class="m-0 text-center"><?php echo $tt; ?></p> 
                                </div>            
                        <?php endwhile; ?>
                    <?php endif; ?>  
            </div>
            <div class="col-sm-5 px-sm-5">
                <?php if( have_rows('icotxt_dere') ): ?>
                        <?php while( have_rows('icotxt_dere') ): the_row(); 
                            $tt = get_sub_field('txt'); 
                            $url_img = get_sub_field('ico'); ?>
                                <div class="card px-sm-4 px-3 pb-sm-4 pb-3 pt-sm-5 pt-4 h-100">
                                    <?php if ($url_img) : ?>
                                        <img src="<?php echo esc_url($url_img); ?>" alt="Icono" class="w-25 mb-4 mx-auto">
                                    <?php endif; ?>
                                    <p class="m-0 text-center"><?php echo $tt; ?></p> 
                                </div>            
                        <?php endwhile; ?>
                    <?php endif; ?>  
            </div>
        </div>
	</div>
</section>
</main>
<?php get_footer(); ?>