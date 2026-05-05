<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package skema
 */
get_header();
$post_id         = get_queried_object_id();
$titulo_proyecto = get_the_title( $post_id );
$banner_alt      = sprintf(
	/* translators: %s: nombre del proyecto */
	__( 'Banner del proyecto %s', 'theme_skema' ),
	$titulo_proyecto
);
?>
<main id="primary" class="site-main">
    <section class="banner-nosotros">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 px-0">
                    <?php $url_img = get_field('ban-proyect'); 
                if ($url_img) : ?>
                    <img src="<?php echo esc_url($url_img); ?>" alt="<?php echo esc_attr( $banner_alt ); ?>"
                        class="w-100 img-fluid d-none d-sm-block">
                    <?php endif; ?>
                    <?php $banner_movil = get_field('banner_movil');
                if ($banner_movil) : ?>
                    <img src="<?php echo esc_url($banner_movil); ?>" alt="<?php echo esc_attr( $banner_alt ); ?>"
                        class="img-fluid d-sm-none d-block w-100">
                    <?php endif; ?>
                    <div
                        class="card-img-overlay p-sm-5 px-5 d-flex align-items-sm-center align-items-end  justify-content-sm-start justify-content-center w-100">
                        <div class="text-content text-white pl-sm-5">
                            <h1><?php the_title(); ?></h1>
                            <p class="px-4 px-sm-0"><?= get_the_excerpt(); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<?php
	$skema_logo_proyecto = '';
	if ( function_exists( 'theme_skema_acf_value_to_image_url' ) ) {
		$skema_logo_proyecto = theme_skema_acf_value_to_image_url( get_field( 'logo_proyecto', $post_id ) );
	}
	if ( $skema_logo_proyecto ) :
		$skema_logo_alt = sprintf(
			/* translators: %s: project title */
			__( 'Logo — %s', 'theme_skema' ),
			$titulo_proyecto
		);
		?>
	<div class="proyecto-project-logo-wrap">
		<img src="<?php echo esc_url( $skema_logo_proyecto ); ?>" class="proyecto-project-logo img-fluid" alt="<?php echo esc_attr( $skema_logo_alt ); ?>" loading="lazy" decoding="async" />
	</div>
	<?php endif; ?>
    <section class="contenido_proy px-sm-5 px-4 mt-sm-5 mt-4">
        <div class="container-fluid px-sm-5">
            <h2 class="pb-sm-5 pb-3 text-center"><?php the_field('tt_proyect'); ?></h2>
            <div class="row justify-content-center">
                <div class="col-sm-6 px-sm-5 mb-sm-0 mb-4">
                    <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail( 'full', array( 'class' => 'img-fluid w-100', 'alt' => esc_attr( $titulo_proyecto ) ) ); ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6 pl-sm-4 pr-sm-5 mb-sm-0 mb-4 align-self-center">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </section>
    <section class="ubica_video px-sm-5 px-4 pt-sm-4 mt-sm-5">
        <div class="container-fluid px-sm-5">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-12 px-sm-5">
                    <?php if(get_field('txt_ubic')): ?>
                    <h2 class="text-center mb-4"><i class="bi bi-geo-alt-fill"></i><?php the_field('txt_ubic'); ?></h2>
                    <?php endif; 
                $video    = get_field('video_proy');
                $location = get_field('iframe_mapa');
                $video_embed_title = sprintf(
                    /* translators: %s: project title */
                    __( 'Video del proyecto %s', 'theme_skema' ),
                    $titulo_proyecto
                );
                $map_embed_title = sprintf(
                    /* translators: %s: project title */
                    __( 'Mapa de ubicación del proyecto %s', 'theme_skema' ),
                    $titulo_proyecto
                );
                $video_embed_html = function_exists( 'theme_skema_accessible_embed_html' )
                    ? theme_skema_accessible_embed_html( $video, $video_embed_title )
                    : ( is_string( $video ) ? $video : '' );
                $map_embed_html   = function_exists( 'theme_skema_accessible_embed_html' )
                    ? theme_skema_accessible_embed_html( $location, $map_embed_title )
                    : ( is_string( $location ) ? $location : '' );
                if ( $video ) : ?>
                    <div id="video_proy" class="embed-responsive embed-responsive-16by9">
                        <?php echo $video_embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <?php endif; 
                if ( $location ) : ?>
                    <div id="ubica">
                        <?php echo $map_embed_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row mt-sm-5 mt-4 justify-content-between">
                <div class="col-sm-6 col-12 text-center text-sm-left mb-4 mb-sm-0">
                    <?php if ( $video && $location ) : ?>
                    <!-- Ambos existen: mostramos ambos botones -->
                    <button class="btn-ubica mr-3">Ver ubicación</button>
                    <button class="btn-video">Video</button>

                    <?php elseif ( $location ) : ?>
                    <!-- Solo ubicación: botón único activo -->
                    <button class="btn-ubica active">Ver ubicación</button>

                    <?php elseif ( $video ) : ?>
                    <!-- Solo vídeo: botón único activo -->
                    <button class="btn-video active">Video</button>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6 col-12 text-sm-right text-center align-self-center mb-4 mb-sm-0">
                    <?php $estado = wp_get_post_terms(get_the_ID(), 'estado_proyecto');
                if (!empty($estado) && !is_wp_error($estado)) { ?>
                    <p class="m-0">Estado del proyecto: <span
                            class="ml-sm-4 ml-3 estado-proyecto"><?php echo esc_html($estado[0]->name); ?></span></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php if(get_field('area_const') || get_field('area_priv') || get_field('imagenes')): ?>
    <section class="carac_zonas px-sm-5 pt-sm-4 mt-sm-5">
        <div class="container-fluid text-center px-sm-5 pb-sm-5">
            <div class="row mb-sm-5 mb-4">
                <div class="col-sm"></div>
                <div class="col-sm-10 col-12">
                    <ul class="nav text-center justify-content-between" id="myTab" role="tablist">
                        <?php if(get_field('area_const') || get_field('area_priv')): ?>
                        <li class="col-sm-4 col-5 px-0 px-sm-3">
                            <a id="caract-tab" href="#caract" class="nav-link btn-catego active" data-toggle="tab" role="tab"
                                aria-controls="caract" aria-selected="true">
                                Caracteristicas
                            </a>

                        </li>
                        <?php endif; ?>
                        <?php if(get_field('imagenes')): ?>
                        <li class="col-sm-4 col-5 px-0 px-sm-3">
                            <a id="zonas-tab" href="#zonas" class="nav-link btn-catego" data-toggle="tab" role="tab"
                                aria-controls="zonas" aria-selected="false">
                                Zonas comunes
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col"></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-10 col-12">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="caract" role="tabpanel" aria-labelledby="caract-tab">
                            <div class="row justify-content-center">
                                <?php if(get_field('area_const')): ?>
                                <div class="col-sm-5 col-6">
                                    <?php echo file_get_contents(get_template_directory() . '/img/area-construida.svg'); ?>
                                    <p class="m-0 mt-4 text-center">
                                        Área Construida
                                    </p>
                                    <span>
                                        Desde <?php the_field('area_const'); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php if(get_field('area_priv')): ?>
                                <div class="col-sm-5 col-6">
                                    <?php echo file_get_contents(get_template_directory() . '/img/area-privada.svg'); ?>
                                    <p class="mb-0 mt-4 text-center">
                                        Área Privada
                                    </p>
                                    <span>
                                        Desde <?php the_field('area_priv'); ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="zonas" role="tabpanel" aria-labelledby="zonas-tab">
                            <div class="row justify-content-center">
                                <div class="col-sm-12">
                                    <?php if(get_field('imagenes')): ?>
                                    <?php $galeria = get_field('imagenes'); ?>
                                    <div class="slider-zonas">
                                        <?php
										$iz = 1;
										foreach ( $galeria as $img_url ) :
											$alt_zona = sprintf(
												/* translators: 1: número de imagen, 2: nombre del proyecto */
												__( 'Zona común %1$d — %2$s', 'theme_skema' ),
												$iz,
												$titulo_proyecto
											);
											?>
                                        <div><img src="<?= esc_url($img_url); ?>" class="img-fluid w-100"
                                                alt="<?php echo esc_attr( $alt_zona ); ?>"></div>
                                        <?php
											$iz++;
										endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <?php $tipo = wp_get_post_terms(get_the_ID(), 'tipo_proyecto');
if (!empty($tipo) && !is_wp_error($tipo)) { $tipo_slug = $tipo[0]->slug; } 
$args = array(
    'post_type' => 'proyectos',
    'posts_per_page' => -1,
    'post__not_in' => array(get_the_ID()),
    'tax_query' => array(
        array(
            'taxonomy' => 'tipo_proyecto',
            'field' => 'slug',
            'terms' => $tipo_slug
        )
    )
);
$query = new WP_Query($args);
if ($query->have_posts()) :  $cont = 1; 
    $cuantos = $query->found_posts;  ?>
    <section class="similares mt-5">
        <div class="container-fluid">
            <h2 class="pb-sm-5 pb-4 text-center">PROYECTOS SIMILARES</h2>
            <div class="row justify-content-center">
                <div class="col-sm-11 px-sm-5">
                    <div class="slider_project slider_project--similares row justify-content-center"
                        data-projects-count="<?php echo esc_attr( (string) (int) $cuantos ); ?>">
                        <?php while ($query->have_posts()) : $query->the_post();
                            $estado = wp_get_post_terms(get_the_ID(), 'estado_proyecto')[0]->name ?? 'No especificado';
                            $tipo_proyecto = wp_get_post_terms(get_the_ID(), 'tipo_proyecto')[0]->name ?? 'No especificado';
                            $ciudad = wp_get_post_terms(get_the_ID(), 'ciudad_proyecto')[0]->name ?? 'No especificado'; ?>
                        <div
                            class="col-sm-6 col-12 div-cont-pro cont-pro-<?php echo $cont; ?> <?= $cuantos == 1 ? 'solo-uno' : '' ?> project-item">
                            <span class="txt-estado"><?= $estado; ?></span>
                            <?= get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'img-desc-proyecto img-fluid w-100', 'alt' => esc_attr( get_the_title() ) ) ); ?>
                            <span class="icono-mas">
                                <?php echo file_get_contents(get_template_directory() . '/img/ico-mas.svg'); ?>
                            </span>
                            <div class="hover-infoproyecto px-sm-5 px-4 pt-sm-4 pb-sm-5">
                                <div class="row">
                                    <div class="col-sm-7 col-12 py-3">
                                        <p class="tipo-proyecto-meta"><?= esc_html( $tipo_proyecto ); ?></p>
                                        <h3><?= esc_html( get_the_title() ); ?></h3>
                                        <p class="ciudad-proyecto-meta"><?= esc_html( $ciudad ); ?></p>
                                        <p><?= esc_html( get_the_excerpt() ); ?></p>
                                    </div>
                                    <div class="col-sm-5 col-12 align-self-center py-sm-5 py-0 text-center">
                                        <a class="btn-verproyecto mt-sm-3 mb-sm-5 mb-3"
                                            href="<?php the_permalink(); ?>">Ver proyecto</a>
                                        <img class="img-fluid w-75 mt-sm-4 mt-2 d-none d-sm-block rounded-0"
                                            src="<?php echo get_template_directory_uri() ?>/img/logo-skema.png"
                                            alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $cont++;
                        endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif;  wp_reset_postdata(); ?>
    <?php if(have_rows('avance')): ?>
    <section class="avance px-sm-5 mt-4">
        <div class="container-fluid">
            <h2 class="pb-sm-4 pb-3 text-center">AVANCE DE OBRA</h2>
            <div class="row justify-content-center">
                <div class="col-sm-7 px-sm-0 d-sm-flex align-items-sm-end justify-content-sm-around">
                    <p>Seleccione el mes que desea visualizar:</p>
                    <div class="custom-select-wrapper mb-sm-4 mb-3 mx-sm-0 mx-auto">
                        <label for="selectAvance" class="screen-reader-text">
                            <?php esc_html_e( 'Selecciona el mes del avance de obra', 'theme_skema' ); ?>
                        </label>
                        <select id="selectAvance" class="custom-select-control">
                            <?php if (have_rows('avance')) :
                                $i = 0;
                                while (have_rows('avance')) : the_row();
                                    $mes_ano = get_sub_field('mes_ano'); ?>
                            <option value="avance-<?php echo $i; ?>"><?php echo esc_html($mes_ano); ?></option>
                            <?php $i++;
                                endwhile;
                            endif; ?>
                        </select>

                        <span class="custom-select-arrow">
                            <?php echo file_get_contents(get_template_directory() . '/img/f-select.svg'); ?>
                        </span>
                    </div>
                </div>
                <div class="col-sm-11 px-sm-0">
                    <?php if (have_rows('avance')) :
                    $i = 0;
                    while (have_rows('avance')) : the_row();
                        $galeria = get_sub_field('imgs'); ?>
                    <div class="avance-galeria <?= $i == 0 ? 'active' : ''; ?>" id="avance-<?php echo $i; ?>"
                        style="<?= $i != 0 ? 'display:none;' : ''; ?>">
                        <div class="slider-avance">
                            <?php
								$ia = 1;
								foreach ( $galeria as $img_url ) :
									$alt_av = sprintf(
										/* translators: 1: número de imagen, 2: nombre del proyecto */
										__( 'Avance de obra %1$d — %2$s', 'theme_skema' ),
										$ia,
										$titulo_proyecto
									);
									?>
                            <div><img src="<?= esc_url($img_url); ?>" class="img-fluid w-100"
                                    alt="<?php echo esc_attr( $alt_av ); ?>"></div>
                            <?php
									$ia++;
								endforeach; ?>
                        </div>
                    </div>
                    <?php $i++;
                    endwhile;
                endif; ?>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-7 col-12">
                    <a href="#info" class="btn-intere text-center">Estoy interesado en un proyecto</a>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php get_template_part( 'template-parts/content', 'fcontacto' ); ?>
</main>
<?php get_footer(); ?>