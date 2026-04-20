<?php
/**
 * Template Name: Theme Sobre nosotros
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
                <div class="card-img-overlay p-sm-5 d-flex align-items-sm-center align-items-end justify-content-start w-100">
                    <div class="text-content text-white pl-sm-5">
                      <?php the_content(); ?>   
                    </div>     
                </div>               
            </div>
        </div>
    </div>
</section>
<section class="valores my-sm-5 my-4 px-4">
    <div class="container-fluid px-sm-5">
        <div class="row">
            <h2 class="text-center col-sm-12 mb-5"><?php the_field('tt_valores'); ?></h2>
            <div class="col-sm-12 px-sm-5 px-4">
                <div class="row justify-content-center">
                    <?php if( have_rows('valores') ): ?>
                        <?php while( have_rows('valores') ): the_row(); 
                            $tt = get_sub_field('valor'); 
                            $desc = get_sub_field('desc'); 
                            $url_img = get_sub_field('icono'); ?>
                            <div class="col-sm-3 col-6 px-sm-3 px-2 pb-sm-0 pb-4">
                                <div class="card px-sm-4 px-2 py-3 pb-sm-4 pt-sm-5 h-100">
                                    <?php if ($url_img) : ?>
                                        <img src="<?php echo esc_url($url_img); ?>" alt="Icono" class="w-25 mb-sm-3 mb-2 mx-auto mx-sm-0">
                                    <?php endif; ?>
                                    <h3 class="mb-sm-3 md-2"><?php echo $tt; ?></h3>
                                    <p class="m-0"><?php echo $desc; ?></p> 
                                </div>                                                              
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="trayecto" id="trayecto">
    <div class="container-fluid text-center px-sm-5 pb-sm-5">
        <h2 class="text-center py-4"><?php the_field('tt_tray'); ?></h2>
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12">
                <ul class="nav text-center justify-content-center" id="myTab" role="tablist">
                    <?php $terms = get_terms(array('taxonomy' => 'tipo_proyecto', 'hide_empty' => false));
                    foreach ($terms as $index => $term) { ?>
                        <li class="col-sm-3 col-6 mb-4 mb-sm-0">
                            <a href="#<?= $term->slug; ?>" class="nav-link btn-catego <?php echo ($index== 0)?'active':''; ?>" data-toggle="tab" role="tab" aria-controls="<?= $term->slug ?>" aria-selected="<?= $index == 0 ? 'true' : 'false' ?>">
                                <?= $term->name; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <div class="container-fluid text-center py-sm-5 px-3 px-sm-3" style="background: #f8f8f8;">
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12 py-sm-0 py-5 px-sm-0">
                <div class="tab-content" id="myTabContent">
                    <?php foreach ($terms as $index => $term) : ?>
                        <div class="tab-pane fade <?= $index == 0 ? 'show active' : '' ?>" id="<?= $term->slug ?>" role="tabpanel">
                            <div class="row">
                                <!-- Timeline a la izquierda -->
                               <div class="col-6 px-2 d-flex justify-content-start pr-sm-5">
                                    <div class="timeline-container position-relative w-100 align-self-center">
                                        <!-- Flechas -->
                                        <div class="timeline-arrows d-none d-sm-flex flex-column align-items-center position-absolute">
                                            <div class="arrow-up cursor-pointer mb-2"><?php echo file_get_contents(get_template_directory() . '/img/f-arriba.svg'); ?></div>
                                            <div class="arrow-down cursor-pointer"><?php echo file_get_contents(get_template_directory() . '/img/f-abajo.svg'); ?></div>
                                        </div>
                                        <!-- Timeline Scroll -->
                                        <div class="timeline-scroll overflow-auto ml-sm-5 pl-sm-4" style="max-height: 500px; scroll-behavior: smooth;">
                                            <div class="timeline-years">
                                                <?php if (have_rows('anos', 'term_' . $term->term_id)) :
                                                    while (have_rows('anos', 'term_' . $term->term_id)) : the_row(); ?>
                                                        <div class="timeline-year-block mb-4">
                                                            <div class="d-flex">
                                                                <div class="timeline-line d-flex flex-column align-items-center position-relative">
                                                                    <span class="timeline-dot big"></span>
                                                                    <div class="timeline-line-inner position-absolute mr-4" style="top: 12px; bottom:-25px;left:-3px;width: 2px; background-color: #002f87;"></div>
                                                                </div>
                                                                <div class="div_info_anos">
                                                                    <h4 class="font-weight-bold d-block ml-4 mb-0 pl-sm-2"><?php the_sub_field('ano'); ?></h4>
                                                                    <?php if (have_rows('proyectos')) :  $c = 0;
                                                                        while (have_rows('proyectos')) : the_row(); ?>
                                                                            <div class="d-flex mb-4">
                                                                                <?php if ($c != 0): ?>
                                                                                <div class="timeline-dot small mr-sm-3 mt-sm-2"></div>
                                                                               <?php else: ?>
                                                                                <div class="mr-sm-4 mt-sm-2"></div>
                                                                               <?php endif; ?>
                                                                                <div class="pl-sm-2 <?= $c != 0 ? 'pl-2' : 'pl-4' ?>">
                                                                                    <p class="m-0"><b><?php the_sub_field('proy'); ?></b></p>
                                                                                    <p class="m-0"><?php the_sub_field('ciu-m'); ?></p>
                                                                                </div>
                                                                            </div>
                                                                        <?php $c++; 
                                                                        endwhile;
                                                                    endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endwhile;
                                                endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Galería a la derecha -->
                                <div class="col-6">
                                    <div class="d-flex flex-wrap rounded overflow-hidden">
                                        <?php
                                        $imagenes = get_field('imgs_pro', 'term_' . $term->term_id);
                                        if ($imagenes) :
                                            foreach ($imagenes as $index => $img) :
                                                // clases para bordes redondeados según posición
                                                $clase_borde = '';
                                                if ($index == 0) $clase_borde = 'rounded-top-left';
                                                if ($index == 1) $clase_borde = 'rounded-top-right';
                                                if ($index == 2) $clase_borde = 'rounded-bottom-left';
                                                if ($index == 3) $clase_borde = 'rounded-bottom-right';
                                        ?>
                                            <div class="div_imgs" style="">
                                                <img src="<?= esc_url($img); ?>" class="img-fluid w-100 <?= $clase_borde ?>" alt="Proyecto">
                                            </div>
                                        <?php endforeach;
                                        endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-sm"></div>
        </div>
    </div>
</section>
<section class="sainc py-5 px-sm-5">
    <div class="container-fluid py-sm-5 px-sm-5">
        <div class="row justify-content-center">
            <div class="col-sm-5 col-12 pl-sm-5 d-flex">
                <?php $url_img = get_field('logo_sainc'); 
                if ($url_img) : ?>
                    <img src="<?php echo esc_url($url_img); ?>" alt="Icono" class="w-75 img-fluid mx-auto mb-3 mb-sm-0">
                <?php endif; ?>
            </div>
            <div class="col-sm-7 col-12 px-5 align-self-center">
                <?php if( have_rows('textos_noso') ): ?>
                    <?php while( have_rows('textos_noso') ): the_row(); 
                        $tt = get_sub_field('titulo'); 
                        $descr = get_sub_field('descr'); ?>              
                        <h3 class="mb-4"><?php echo $tt; ?></h3>
                        <p class="m-0 pr-sm-5"><?php echo $descr; ?></p> 
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<section class="ofrecemos py-sm-5 py-4 px-sm-5 px-4">
    <div class="container-fluid px-sm-5">
        <h2 class="pb-sm-5 pb-2 text-center"><?php the_field('tt_que'); ?></h2>        
        <?php if (have_rows('que_ofrecemos')): $i = 0;?>
            <?php while (have_rows('que_ofrecemos')): the_row(); ?>
                <div class="row justify-content-center px-sm-5 mb-sm-5">
                    <?php if ($i % 2 == 0):  ?>
                       <div class="col-sm-6 col-12 pl-sm-5 pr-sm-2">
                            <img class="w-100 mb-3 mb-sm-0" src="<?php the_sub_field('img') ?>" alt="<?php echo esc_attr( wp_strip_all_tags( get_sub_field( 'tt' ) ) ); ?>">
                        </div>
                        <div class="col-sm-6 col-12 px-sm-5 mb-4 mb-sm-0 align-self-center">
                            <div class="text_ofre pl-sm-4">
                                <h3 class="mb-sm-4 pr-sm-4"><?php the_sub_field('tt') ?></h3>
                                <p><?php the_sub_field('desc') ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-sm-6 col-12 px-sm-5 mb-4 mb-sm-0 order-sm-0 order-1 align-self-center ">
                            <div class="text_ofre">
                                <h3 class="mb-sm-4 pr-sm-5"><?php the_sub_field('tt') ?></h3>
                                <p><?php the_sub_field('desc') ?></p>
                           </div>
                        </div>
                        <div class="col-sm-6 col-12 order-sm-1 order-0 pr-sm-5 pl-sm-2">
                            <img class="w-100 mb-3 mb-sm-0" src="<?php the_sub_field('img') ?>" alt="<?php echo esc_attr( wp_strip_all_tags( get_sub_field( 'tt' ) ) ); ?>">
                        </div>                        
                    <?php endif; ?>
                </div>
            <?php $i++; ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>
</main>
<?php get_footer(); ?>