<?php
/**
 * Template Name: Theme Proyectos
 */

if ( ! function_exists( 'theme_skema_proyectos_resolve_active_tab' ) ) {
	/**
	 * Índice de la pestaña activa según URL (varios nombres de parámetro, slug o ID de término).
	 *
	 * @param WP_Term[] $terms Términos de tipo_proyecto.
	 * @return int Índice 0-based.
	 */
	function theme_skema_proyectos_resolve_active_tab( $terms ) {
		if ( empty( $terms ) ) {
			return 0;
		}

		$param_keys = array( 'skema_tipo', 'tab', 'tipo', 'tipo_proyecto' );
		$raw        = '';
		foreach ( $param_keys as $param_key ) {
			if ( isset( $_GET[ $param_key ] ) && (string) $_GET[ $param_key ] !== '' ) {
				$raw = wp_unslash( $_GET[ $param_key ] );
				break;
			}
		}
		if ( $raw === '' && ! empty( $_SERVER['QUERY_STRING'] ) ) {
			parse_str( (string) wp_unslash( $_SERVER['QUERY_STRING'] ), $qs_parsed );
			if ( is_array( $qs_parsed ) ) {
				foreach ( $param_keys as $param_key ) {
					if ( isset( $qs_parsed[ $param_key ] ) && (string) $qs_parsed[ $param_key ] !== '' ) {
						$raw = (string) $qs_parsed[ $param_key ];
						break;
					}
				}
			}
		}

		if ( $raw === '' ) {
			return 0;
		}

		$raw_trim = trim( (string) $raw );
		if ( $raw_trim === '' ) {
			return 0;
		}

		if ( ctype_digit( $raw_trim ) ) {
			$want_id = (int) $raw_trim;
			foreach ( $terms as $i => $t ) {
				if ( (int) $t->term_id === $want_id ) {
					return (int) $i;
				}
			}
			return 0;
		}

		$candidates   = array();
		$candidates[] = sanitize_text_field( $raw_trim );
		$candidates[] = sanitize_title( $raw_trim );
		$candidates   = array_unique( array_filter( $candidates ) );

		foreach ( $terms as $i => $t ) {
			foreach ( $candidates as $c ) {
				if ( strcasecmp( $t->slug, $c ) === 0 ) {
					return (int) $i;
				}
			}
		}

		$name_compare = wp_strip_all_tags( $raw_trim );
		$name_slug    = sanitize_title( $raw_trim );
		foreach ( $terms as $i => $t ) {
			$term_name = wp_strip_all_tags( $t->name );
			if ( strcasecmp( $term_name, $name_compare ) === 0 ) {
				return (int) $i;
			}
			if ( $name_slug !== '' && sanitize_title( $t->name ) === $name_slug ) {
				return (int) $i;
			}
		}

		return 0;
	}
}

if ( ! function_exists( 'theme_skema_proyectos_tab_url_sync' ) ) {
	/**
	 * Refuerzo en el cliente: caché sin Vary por query o enlaces solo con hash.
	 */
	function theme_skema_proyectos_tab_url_sync() {
		?>
<script>
jQuery(function($) {
    var $root = $('#myTab[data-skema-proyectos-tabs]');
    if (!$root.length) {
        return;
    }
    var p = new URLSearchParams(window.location.search);
    var s = (p.get('skema_tipo') || p.get('tab') || p.get('tipo') || p.get('tipo_proyecto') || '').trim()
        .toLowerCase();
    if (!s && window.location.hash) {
        s = decodeURIComponent(window.location.hash.replace(/^#/, '').trim()).toLowerCase();
    }
    if (!s || !/^[a-z0-9_-]+$/i.test(s)) {
        return;
    }
    var $a = $root.find('a.nav-link[href="#' + s + '"]');
    if (!$a.length) {
        return;
    }
    if (typeof $.fn.tab === 'function') {
        $a.tab('show');
        return;
    }
    $a[0].click();
});
</script>
<?php
	}
}
add_action( 'wp_footer', 'theme_skema_proyectos_tab_url_sync', 99 );

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
                    <img src="<?php echo esc_url($banner_movil); ?>"
                        alt="<?php echo esc_attr( sprintf( __( 'Banner de %s', 'theme_skema' ), get_the_title() ) ); ?>"
                        class="img-fluid d-sm-none d-block w-100">
                    <?php endif; ?>
                    <div
                        class="card-img-overlay p-sm-5 d-flex align-items-sm-center align-items-end justify-content-center w-100">
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
                    <ul class="nav text-center justify-content-center" id="myTab" role="tablist" data-skema-proyectos-tabs="1">
                        <?php
                    $terms = get_terms(array('taxonomy' => 'tipo_proyecto', 'hide_empty' => false));
                    if (is_wp_error($terms)) {
                        $terms = array();
                    }
                    $active_tab_index = theme_skema_proyectos_resolve_active_tab( $terms );
                    foreach ($terms as $index => $term) { ?>
                        <li class="col-sm-3 px-4 col-6 mb-4 mb-sm-0">
                            <a id="<?= $term->slug; ?>-tab" href="#<?= $term->slug; ?>"
                                class="nav-link btn-catego <?php echo ($index === $active_tab_index) ? 'active' : ''; ?>"
                                data-toggle="tab" role="tab" aria-controls="<?= $term->slug ?>"
                                aria-selected="<?= $index === $active_tab_index ? 'true' : 'false' ?>">
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
                        <div class="tab-pane fade <?= $index === $active_tab_index ? 'show active' : '' ?>"
                            id="<?= $term->slug ?>" role="tabpanel" aria-labelledby="<?= $term->slug ?>-tab">
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
                    <a href="#info" class="btn-intere">Estoy interesado en un proyecto</a>
                </div>
            </div>
        </div>
    </section>
    <?php get_template_part( 'template-parts/content', 'fcontacto' ); ?>

</main>
<?php get_footer(); ?>