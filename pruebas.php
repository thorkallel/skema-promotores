<section class="proyectos" id="proyectos">
    <div class="container text-center pb-sm-5">
        <h3 class="text-center py-4">NUESTROS PROYECTOS</h3>
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12 pb-sm-5 pb-4">
                <ul class="nav text-center justify-content-center" id="myTab" role="tablist">
                    <?php $terms = get_terms(array('taxonomy' => 'tipo_proyecto','hide_empty' => false));
                    foreach ($terms as $index => $term) { ?>
                        <li class="col-sm-3 col-6 mb-4 mb-sm-0">
                            <a href="#<?= $term->slug; ?>" class="nav-link btn-catego <?php echo ($index== 0)?'active':''; ?>"  data-toggle="tab" role="tab" aria-controls="<?php echo $term->slug; ?>" aria-selected="<?php echo ($index == 0 ? 'true' : 'false'); ?>">
                                <?= $term->name; ?> 
                            </a>
                        </li>
                    <?php }  ?>
                </ul>
            </div>
            <div class="col"></div>
        </div>
        <div class="row mb-sm-5">
            <div class="col-12 col-xl-12 col-md-12 col-sm-12">
                <div class="tab-content" id="myTabContent">
                    <?php foreach ($terms as $index => $term) : ?>
                        <div class="tab-pane fade <?= $index == 0 ? 'show active' : '' ?>" id="<?= $term->slug ?>" role="tabpanel" aria-labelledby="<?= $term->slug ?>-tab">
                            <div class="row justify-content-center">
                                <div class="project-slider" data-projects-count="<?= count(get_posts(array('post_type' => 'proyectos', 'tax_query' => array(array('taxonomy' => 'tipo_proyecto', 'field' => 'slug', 'terms' => $term->slug))))) ?>">
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
                                    if ($query->have_posts()) :
                                        while ($query->have_posts()) : $query->the_post();
                                            $estado = wp_get_post_terms(get_the_ID(), 'estado_proyecto')[0]->name ?? 'No especificado';
                                            $ciudad = wp_get_post_terms(get_the_ID(), 'ciudad_proyecto')[0]->name ?? 'No especificado'; ?>
                                            <div class="col-sm-6 col-12 div-cont-pro cont-pro-1 pl-sm-0 px-0 pr-sm-3 project-item">
                                                <div class="hover-infoproyecto px-sm-5 px-4 pt-sm-4 pb-sm-5">
                                                    <div class="row">
                                                        <div class="col-sm-7 col-12 py-3">
                                                            <span class="txt-estado"><?= $estado; ?></span>
                                                            <?= get_the_post_thumbnail(get_the_ID(), 'medium', array('class' => 'img-desc-proyecto img-fluid w-100')); ?>
                                                            <span class="icono-mas">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 133 133" fill="none">
                                                                  <path d="M0 0H133V133H0V0Z" fill="#FC7416"/>
                                                                  <path d="M85 60.0726H73V46.6909C73 44.9163 72.3678 43.2145 71.2426 41.9597C70.1174 40.7049 68.5913 40 67 40C65.4087 40 63.8826 40.7049 62.7574 41.9597C61.6322 43.2145 61 44.9163 61 46.6909L61.213 60.0726H49C47.4087 60.0726 45.8826 60.7775 44.7574 62.0323C43.6322 63.287 43 64.9889 43 66.7634C43 68.5379 43.6322 70.2398 44.7574 71.4946C45.8826 72.7493 47.4087 73.4543 49 73.4543L61.213 73.2167L61 86.836C61 88.6105 61.6322 90.3123 62.7574 91.5671C63.8826 92.8219 65.4087 93.5268 67 93.5268C68.5913 93.5268 70.1174 92.8219 71.2426 91.5671C72.3678 90.3123 73 88.6105 73 86.836V73.2167L85 73.4543C86.5913 73.4543 88.1174 72.7493 89.2426 71.4946C90.3678 70.2398 91 68.5379 91 66.7634C91 64.9889 90.3678 63.287 89.2426 62.0323C88.1174 60.7775 86.5913 60.0726 85 60.0726Z" fill="white"/>
                                                                </svg>
                                                            </span>
                                                            <h2>HOTELERO</h2>
                                                            <h3><?= get_the_title(); ?></h3>
                                                            <h4>Ciudad: <?= $ciudad; ?></h4>
                                                            <p><?= get_the_excerpt(); ?></p>
                                                        </div>
                                                        <div class="col-sm-5 col-12 align-self-center py-sm-5 py-0">
                                                            <a class="btn-verproyecto mt-sm-3 mb-sm-5 mb-3" href="#">Ver proyecto</a>
                                                            <img class="img-fluid w-75 mt-sm-4 mt-2 d-none d-sm-block" src="<?php echo get_template_directory_uri() ?>/img/logo-skema.png" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile;
                                    else :
                                        echo "<p>No hay proyectos disponibles para este tipo de proyecto.</p>";
                                    endif; 
                                    wp_reset_postdata(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>    





<section class="proyectos" id="proyectos">
    <div class="container text-center pb-sm-5">
        <h3 class="text-center py-4">NUESTROS PROYECTOS</h3>
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12 pb-sm-5 pb-4">
                <ul class="nav text-center justify-content-center" id="myTab" role="tablist">
                    <?php $terms = get_terms(array('taxonomy' => 'tipo_proyecto','hide_empty' => false));
                    foreach ($terms as $index => $term) { ?>
                        <li class="col-sm-3 col-6 mb-4 mb-sm-0">
                            <a href="#<?= $term->slug; ?>" class="nav-link btn-catego <?php echo ($index== 0)?'active':''; ?>"  data-toggle="tab" role="tab" aria-controls="<?php echo $term->slug; ?>" aria-selected="<?php echo ($index == 0 ? 'true' : 'false'); ?>">
                                <?= $term->name; ?> 
                            </a>
                        </li>
                    <?php }  ?>
                </ul>
            </div>
            <div class="col"></div>
        </div>
        <div class="row mb-sm-5">
            <div class="col-12 col-xl-12 col-md-12 col-sm-12">
                <!-- <div class="row justify-content-center"> -->
                    <div class="tab-content" id="myTabContent">
                    
                        <?php foreach ($terms as $index => $term) : ?>
                        
                            <div class="tab-pane fade <?= $index == 0 ? 'show active' : '' ?>" id="<?= $term->slug ?>" role="tabpanel" aria-labelledby="<?= $term->slug ?>-tab">
                                <div class="project-slider" data-projects-count="<?= count(get_posts(array('post_type' => 'proyectos', 'tax_query' => array(array('taxonomy' => 'tipo_proyecto', 'field' => 'slug', 'terms' => $term->slug))))) ?>">
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
                                    if ($query->have_posts()) :
                                        while ($query->have_posts()) : $query->the_post();
                                            $estado = wp_get_post_terms(get_the_ID(), 'estado_proyecto')[0]->name ?? 'No especificado';
                                            $ciudad = wp_get_post_terms(get_the_ID(), 'ciudad_proyecto')[0]->name ?? 'No especificado';
                                            ?>
                                            <div class="project-item">
                                                <?= get_the_post_thumbnail(get_the_ID(), 'medium'); ?>
                                                <h4><?= get_the_title(); ?></h4>
                                                <p><?= get_the_excerpt(); ?></p>
                                                <p>Estado: <?= $estado; ?></p>
                                                <p>Ciudad: <?= $ciudad; ?></p>
                                            </div>
                                        <?php endwhile;
                                    else :
                                        echo "<p>No hay proyectos disponibles para este tipo de proyecto.</p>";
                                    endif;
                                    wp_reset_postdata();
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <!-- </div> -->
            </div>
        </div>
    </div>
</section>     




<section class="proyectos" id="proyectos">
    <div class="container text-center pb-sm-5">
        <h3 class="text-center py-4">NUESTROS PROYECTOS</h3>
        <div class="row">
            <div class="col-sm"></div>
            <div class="col-sm-10 col-12 pb-sm-5 pb-4">
                <ul class="nav text-center justify-content-center">
                    <li class="col-sm-3 col-6 mb-4 mb-sm-0">
                        <a href="#tab-1" class="btn-catego show active"  data-toggle="pill">
                            Comerciales
                        </a>
                    </li>
                    <li class="col-sm-3 col-6">
                        <a href="#tab-1" class="btn-catego show"  data-toggle="pill">
                            Institucionales
                        </a>
                    </li>
                    <li class="col-sm-3 col-6">
                        <a href="#tab-1" class="btn-catego show"  data-toggle="pill">
                            Residenciales
                        </a>
                    </li>
                    <li class="col-sm-3 col-6">
                        <a href="#tab-1" class="btn-catego show"  data-toggle="pill">
                            Inversión
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col"></div>
        </div>
        <div class="row mb-sm-5">
            <div class="col-12 col-xl-12 col-md-12 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab-1">
                     
                        <div class="row justify-content-center" id="id_slider_proyectos">
                            <div class="col-sm-6 col-12 div-cont-pro cont-pro-1 pl-sm-0 px-0 pr-sm-3">
                                <span class="txt-estado">Ejecución</span>
                                <img class="img-desc-proyecto img-fluid w-100" src="<?php echo get_template_directory_uri() ?>/img/mama.jpg" alt="">
                                <span class="icono-mas">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 133 133" fill="none">
                                      <path d="M0 0H133V133H0V0Z" fill="#FC7416"/>
                                      <path d="M85 60.0726H73V46.6909C73 44.9163 72.3678 43.2145 71.2426 41.9597C70.1174 40.7049 68.5913 40 67 40C65.4087 40 63.8826 40.7049 62.7574 41.9597C61.6322 43.2145 61 44.9163 61 46.6909L61.213 60.0726H49C47.4087 60.0726 45.8826 60.7775 44.7574 62.0323C43.6322 63.287 43 64.9889 43 66.7634C43 68.5379 43.6322 70.2398 44.7574 71.4946C45.8826 72.7493 47.4087 73.4543 49 73.4543L61.213 73.2167L61 86.836C61 88.6105 61.6322 90.3123 62.7574 91.5671C63.8826 92.8219 65.4087 93.5268 67 93.5268C68.5913 93.5268 70.1174 92.8219 71.2426 91.5671C72.3678 90.3123 73 88.6105 73 86.836V73.2167L85 73.4543C86.5913 73.4543 88.1174 72.7493 89.2426 71.4946C90.3678 70.2398 91 68.5379 91 66.7634C91 64.9889 90.3678 63.287 89.2426 62.0323C88.1174 60.7775 86.5913 60.0726 85 60.0726Z" fill="white"/>
                                    </svg>
                                </span>
                                <div class="hover-infoproyecto px-sm-5 px-4 pt-sm-4 pb-sm-5">
                                    <div class="row">
                                        <div class="col-sm-7 col-12 py-3">
                                            <h2>HOTELERO</h2>
                                            <h3>MAMA SHELTER</h3>
                                            <h4>Medellín</h4>
                                            <p>El proyecto hotelero y de entretnimiento más importante de la ciudad</p>
                                        </div>
                                        <div class="col-sm-5 col-12 align-self-center py-sm-5 py-0">
                                            <a class="btn-verproyecto mt-sm-3 mb-sm-5 mb-3" href="#">Ver proyecto</a>
                                            <img class="img-fluid w-75 mt-sm-4 mt-2 d-none d-sm-block" src="<?php echo get_template_directory_uri() ?>/img/logo-skema.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-sm-6 col-12 div-cont-pro cont-pro-2 pr-sm-0 px-0 pl-sm-3">
                                <span class="txt-estado">Terminado</span>
                                <img class="img-desc-proyecto img-fluid w-100" src="<?php echo get_template_directory_uri() ?>/img/victoria.jpg" alt="">
                                <span class="icono-mas">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" viewBox="0 0 133 133" fill="none">
                                      <path d="M0 0H133V133H0V0Z" fill="#FC7416"/>
                                      <path d="M85 60.0726H73V46.6909C73 44.9163 72.3678 43.2145 71.2426 41.9597C70.1174 40.7049 68.5913 40 67 40C65.4087 40 63.8826 40.7049 62.7574 41.9597C61.6322 43.2145 61 44.9163 61 46.6909L61.213 60.0726H49C47.4087 60.0726 45.8826 60.7775 44.7574 62.0323C43.6322 63.287 43 64.9889 43 66.7634C43 68.5379 43.6322 70.2398 44.7574 71.4946C45.8826 72.7493 47.4087 73.4543 49 73.4543L61.213 73.2167L61 86.836C61 88.6105 61.6322 90.3123 62.7574 91.5671C63.8826 92.8219 65.4087 93.5268 67 93.5268C68.5913 93.5268 70.1174 92.8219 71.2426 91.5671C72.3678 90.3123 73 88.6105 73 86.836V73.2167L85 73.4543C86.5913 73.4543 88.1174 72.7493 89.2426 71.4946C90.3678 70.2398 91 68.5379 91 66.7634C91 64.9889 90.3678 63.287 89.2426 62.0323C88.1174 60.7775 86.5913 60.0726 85 60.0726Z" fill="white"/>
                                    </svg>
                                </span>
                                <div class="hover-infoproyecto px-sm-5 px-4 pt-sm-4 pb-sm-5">
                                    <div class="row">
                                        <div class="col-sm-7 col-12 py-sm-3 py-3">
                                            <h2>COMERCIAL</h2>
                                            <h3>C.C. VICTORIA</h3>
                                            <h4>Pereira</h4>
                                            <p>El proyecto Centro Comercial Victoria ubicado en la ciuda de Pereira</p>
                                        </div>
                                        <div class="col-sm-5 col-12 align-self-center py-sm-5">
                                            <a class="btn-verproyecto mt-sm-3 mb-sm-5 mb-3" href="#">Ver proyecto</a>
                                            <img class="img-fluid w-75 mt-4  d-none d-sm-block" src="<?php echo get_template_directory_uri() ?>/img/logo-skema.png" alt="">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="flechas-slider-proyect d-flex d-sm-none">
                            <button class="proyect-prev">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none">
                                  <ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/>
                                  <path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/>
                                </svg>
                            </button>
                            <button class="proyect-next">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none">
                                <ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/>
                                <path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/>
                                </svg>    
                            </button>
                            </div>   
                       
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>