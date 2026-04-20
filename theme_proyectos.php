<?php
/**
 * Template Name: Theme Proyectos
 **/
get_header(); ?>
<main id="primary" class="site-main">
<section class="banner-proyectos">
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
                <div class="card-img-overlay p-sm-5 d-flex align-items-sm-center align-items-end justify-content-center w-100">
                    <div class="text-content text-white mb-5 mb-sm-0 pl-sm-5">
                        <h1><?php the_title(); ?></h1>
                    </div>     
                </div>               
            </div>
        </div>
    </div>
</section>
<section class="descubre mt-sm-4">
    <div class="container-fluid text-center px-sm-5">
        <div class="text-center py-4"><?php the_content(); ?> </div>
        <div class="row mb-sm-5">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12">
                <ul class="nav text-center justify-content-center" id="myTab" role="tablist">
                    <?php $terms = get_terms(array('taxonomy' => 'tipo_proyecto', 'hide_empty' => false));
                    foreach ($terms as $index => $term) { ?>
                        <li class="col-sm-3 px-4 col-6 mb-4 mb-sm-0">
                            <a href="#<?= $term->slug; ?>" class="nav-link btn-catego <?php echo ($index== 0)?'active':''; ?>" data-toggle="tab" role="tab" aria-controls="<?= $term->slug ?>" aria-selected="<?= $index == 0 ? 'true' : 'false' ?>">
                                <?= $term->name; ?>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="col"></div>
        </div>
        <div class="row mb-sm-4">
            <div class="col-12 col-xl-12 col-md-12 col-sm-12 px-sm-5 px-4">
                <div class="tab-content" id="myTabContent">
                    <?php foreach ($terms as $index => $term) : ?>
                        <div class="tab-pane fade <?= $index == 0 ? 'show active' : '' ?>" id="<?= $term->slug ?>" role="tabpanel" aria-labelledby="<?= $term->slug ?>-tab">
                            <div class="row justify-content-center">
                                <?php $args = array(
                                    'post_type' => 'proyectos',
                                    'posts_per_page' => -1,
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'tipo_proyecto',
                                            'field' => 'slug',
                                            'terms' => $term->slug
                                        )
                                    )
                                );
                                $query = new WP_Query($args);
                                if ($query->have_posts()) :  $cont = 1; 
                                    $cuantos = $query->found_posts;
                                    while ($query->have_posts()) : $query->the_post();
                                        $estado = wp_get_post_terms(get_the_ID(), 'estado_proyecto')[0]->name ?? 'No especificado';
                                        $tipo_proyecto = wp_get_post_terms(get_the_ID(), 'tipo_proyecto')[0]->name ?? 'No especificado';
                                        $ciudad = wp_get_post_terms(get_the_ID(), 'ciudad_proyecto')[0]->name ?? 'No especificado'; ?>
                                        <div class="col-sm-3 px-sm-2 px-3 col-12 project-item mb-5">
                                            <div class="card h-100 pb-sm-4 mx-2">
                                                <div class="img-proyect">
                                                    <span class="txt-estado"><?= $estado; ?></span>
                                                    <?= get_the_post_thumbnail(get_the_ID(), 'full', array('class' => 'img-desc-proyecto img-fluid w-100')); ?>
                                                    <a class="icono-mas" href="<?php the_permalink(); ?>">
                                                        <?php echo file_get_contents(get_template_directory() . '/img/ico-mas.svg'); ?>                                                    
                                                    </a>                  
                                                </div>                              
                                                <div class="card-body text-left">                                                       
                                                    <p class="tipo-proyecto-meta"><?= esc_html( $tipo_proyecto ); ?></p>
                                                    <h3><?= esc_html( get_the_title() ); ?></h3>
                                                    <p class="ciudad-proyecto-meta"><?= esc_html( $ciudad ); ?></p>                                              
                                                </div>   
                                            </div>                                             
                                        </div>
                                        <?php $cont++;
                                    endwhile; ?>
                                <?php endif;  wp_reset_postdata(); ?>                                    
                            </div>                                
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-7 col-12">
                <a href="" class="btn-intere">Estoy interesado en un proyecto</a>
            </div>
        </div>
    </div>
</section>
<?php get_template_part( 'template-parts/content', 'fcontacto' ); ?>

</main>
<?php get_footer(); ?>