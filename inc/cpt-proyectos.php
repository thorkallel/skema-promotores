<?php
/**
 * Proyectos: la URL pública es /proyectos/<slug>/.
 *
 * CPT UI conserva la ficha en la base de datos, pero este archivo la deja sin efecto
 * para que la misma clave no se registre dos veces. Las taxonomías siguen privadas
 * hasta la fase de listados por ciudad y tipo.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * CPT UI no debe volver a registrar proyectos ni sus taxonomías.
 *
 * @return bool
 */
function theme_skema_disable_cptui_proyecto_keys() {
	return true;
}
add_filter( 'cptui_disable_proyectos_cpt', 'theme_skema_disable_cptui_proyecto_keys' );
add_filter( 'cptui_disable_ciudad_proyecto_tax', 'theme_skema_disable_cptui_proyecto_keys' );
add_filter( 'cptui_disable_tipo_proyecto_tax', 'theme_skema_disable_cptui_proyecto_keys' );
add_filter( 'cptui_disable_estado_proyecto_tax', 'theme_skema_disable_cptui_proyecto_keys' );

/**
 * Registra el tipo de contenido y las tres taxonomías privadas.
 */
function theme_skema_register_proyectos_post_type() {
	register_post_type(
		'proyectos',
		array(
			'labels'             => array(
				'name'          => __( 'Proyectos', 'theme_skema' ),
				'singular_name' => __( 'Proyecto', 'theme_skema' ),
				'menu_name'     => __( 'Proyectos', 'theme_skema' ),
				'add_new_item'  => __( 'Añadir proyecto', 'theme_skema' ),
				'edit_item'     => __( 'Editar proyecto', 'theme_skema' ),
				'view_item'     => __( 'Ver proyecto', 'theme_skema' ),
				'search_items'  => __( 'Buscar proyectos', 'theme_skema' ),
				'not_found'     => __( 'No se encontraron proyectos.', 'theme_skema' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => 'proyectos',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_icon'          => 'dashicons-building',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		)
	);

	$shared_tax_args = array(
		'public'             => true,
		'publicly_queryable' => false,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
	);

	register_taxonomy(
		'ciudad_proyecto',
		array( 'proyectos' ),
		array_merge(
			$shared_tax_args,
			array(
				'labels' => array(
					'name'          => __( 'Ciudades de proyectos', 'theme_skema' ),
					'singular_name' => __( 'Ciudad de proyecto', 'theme_skema' ),
				),
			)
		)
	);

	register_taxonomy(
		'tipo_proyecto',
		array( 'proyectos' ),
		array_merge(
			$shared_tax_args,
			array(
				'labels' => array(
					'name'          => __( 'Tipos de proyectos', 'theme_skema' ),
					'singular_name' => __( 'Tipo de proyecto', 'theme_skema' ),
				),
			)
		)
	);

	register_taxonomy(
		'estado_proyecto',
		array( 'proyectos' ),
		array_merge(
			$shared_tax_args,
			array(
				'labels' => array(
					'name'          => __( 'Estados de proyectos', 'theme_skema' ),
					'singular_name' => __( 'Estado de proyecto', 'theme_skema' ),
				),
			)
		)
	);
}
add_action( 'init', 'theme_skema_register_proyectos_post_type', 8 );

/**
 * Impide que un proyecto use los slugs reservados para los listados de ciudad y tipo.
 *
 * @param string $slug          Slug propuesto.
 * @param int    $post_id       ID del post.
 * @param string $post_status   Estado.
 * @param string $post_type     Tipo.
 * @return string
 */
function theme_skema_reserve_proyecto_listing_slugs( $slug, $post_id, $post_status, $post_type ) {
	unset( $post_id, $post_status );

	if ( 'proyectos' !== $post_type ) {
		return $slug;
	}

	$reserved = array( 'ciudad', 'tipo' );
	if ( ! in_array( $slug, $reserved, true ) ) {
		return $slug;
	}

	return $slug . '-proyecto';
}
add_filter( 'wp_unique_post_slug', 'theme_skema_reserve_proyecto_listing_slugs', 10, 4 );

/**
 * Regenera las reglas una sola vez después de mover el slug.
 */
function theme_skema_flush_proyecto_rewrites_once() {
	$version = 'proyectos-slug-1';
	if ( get_option( 'theme_skema_rewrite_version' ) === $version ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'theme_skema_rewrite_version', $version, false );
}
add_action( 'init', 'theme_skema_flush_proyecto_rewrites_once', 99 );

/**
 * Campo que une una landing con un único proyecto.
 */
function theme_skema_register_landing_proyecto_field() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_skema_landing_proyecto',
			'title'    => __( 'Proyecto de esta landing', 'theme_skema' ),
			'fields'   => array(
				array(
					'key'           => 'field_skema_landing_proyecto',
					'label'         => __( 'Proyecto', 'theme_skema' ),
					'name'          => 'skema_landing_proyecto',
					'type'          => 'post_object',
					'post_type'     => array( 'proyectos' ),
					'return_format' => 'id',
					'ui'            => 1,
					'allow_null'    => 1,
					'instructions'  => __( 'La URL pública será la de este proyecto. Sin proyecto, la landing no se publica.', 'theme_skema' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'landings',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'theme_skema_register_landing_proyecto_field' );

/**
 * ID del proyecto guardado en la landing.
 *
 * @param int $landing_id ID de la landing.
 * @return int
 */
function theme_skema_landing_project_id( $landing_id ) {
	$landing_id = (int) $landing_id;
	if ( $landing_id <= 0 ) {
		return 0;
	}

	$project_id = 0;
	if ( function_exists( 'get_field' ) ) {
		$raw = get_field( 'skema_landing_proyecto', $landing_id );
		if ( $raw instanceof WP_Post ) {
			$project_id = (int) $raw->ID;
		} elseif ( is_numeric( $raw ) ) {
			$project_id = (int) $raw;
		}
	}

	if ( $project_id <= 0 ) {
		$project_id = (int) get_post_meta( $landing_id, 'skema_landing_proyecto', true );
	}

	if ( $project_id <= 0 || 'proyectos' !== get_post_type( $project_id ) ) {
		return 0;
	}

	return $project_id;
}

/**
 * Landing publicada vinculada a un proyecto.
 *
 * @param int $project_id ID del proyecto.
 * @return int
 */
function theme_skema_linked_landing_id( $project_id ) {
	$project_id = (int) $project_id;
	if ( $project_id <= 0 ) {
		return 0;
	}

	$ids = get_posts(
		array(
			'post_type'              => 'landings',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'meta_key'               => 'skema_landing_proyecto',
			'meta_value'             => (string) $project_id,
		)
	);

	if ( empty( $ids ) ) {
		return 0;
	}

	return (int) $ids[0];
}

/**
 * Landing que debe pintarse en la petición actual.
 *
 * @return int
 */
function theme_skema_viewing_landing_id() {
	if ( is_singular( 'landings' ) ) {
		return (int) get_queried_object_id();
	}

	if ( ! is_singular( 'proyectos' ) ) {
		return 0;
	}

	return theme_skema_linked_landing_id( get_queried_object_id() );
}

/**
 * La ficha de un proyecto con landing usa la URL del proyecto.
 *
 * @param string  $permalink URL calculada por WordPress.
 * @param WP_Post $post      Entrada.
 * @return string
 */
function theme_skema_landing_public_permalink( $permalink, $post ) {
	if ( ! $post instanceof WP_Post || 'landings' !== $post->post_type ) {
		return $permalink;
	}

	$project_id = theme_skema_landing_project_id( $post->ID );
	if ( $project_id <= 0 ) {
		return $permalink;
	}

	$project_url = get_permalink( $project_id );
	if ( ! is_string( $project_url ) || $project_url === '' ) {
		return $permalink;
	}

	return $project_url;
}
add_filter( 'post_type_link', 'theme_skema_landing_public_permalink', 10, 2 );

/**
 * Añade la query de la petición (gclid, utm, etc.) y quita claves que no deben viajar.
 *
 * @param string        $url URL de destino.
 * @param array<int, string> $drop_keys Claves de $_GET que no se reenvían.
 * @return string
 */
function theme_skema_url_with_public_query( $url, array $drop_keys = array() ) {
	if ( $url === '' || empty( $_GET ) || ! is_array( $_GET ) ) {
		return $url;
	}

	$args = array();
	foreach ( wp_unslash( $_GET ) as $key => $value ) {
		if ( ! is_string( $key ) || in_array( $key, $drop_keys, true ) ) {
			continue;
		}
		$args[ $key ] = $value;
	}

	if ( empty( $args ) ) {
		return $url;
	}

	return add_query_arg( $args, $url );
}

/**
 * 404 real cuando una landing pública no tiene proyecto publicado.
 */
function theme_skema_force_404() {
	global $wp_query;

	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();

	$template = get_404_template();
	if ( is_string( $template ) && $template !== '' ) {
		include $template;
	}
	exit;
}

/**
 * Una sola redirección desde /proyecto/<slug>/ o /landings/<slug>/ hacia /proyectos/<slug>/.
 * Conserva la query string. `/?landings=<slug>` también sale en un 301, salvo en la vista previa.
 */
function theme_skema_redirect_legacy_project_urls() {
	if ( is_admin() ) {
		return;
	}

	$request = '';
	if ( isset( $GLOBALS['wp'] ) && is_object( $GLOBALS['wp'] ) ) {
		$request = trim( (string) $GLOBALS['wp']->request, '/' );
	}

	$target = theme_skema_legacy_project_redirect_target( $request );
	if ( $target !== '' ) {
		wp_safe_redirect( theme_skema_url_with_public_query( $target, array( 'landings' ) ), 301 );
		exit;
	}

	theme_skema_redirect_queried_landing();
}
add_action( 'template_redirect', 'theme_skema_redirect_legacy_project_urls', 0 );

/**
 * La URL ?landings= no es canónica. Con proyecto publicado, 301; si no, 404.
 * La vista previa del editor no se redirige.
 */
function theme_skema_redirect_queried_landing() {
	if ( is_preview() || ! is_singular( 'landings' ) ) {
		return;
	}

	$landing_id = (int) get_queried_object_id();
	$project_id = theme_skema_landing_project_id( $landing_id );
	$project    = $project_id > 0 ? get_post( $project_id ) : null;
	if ( ! $project instanceof WP_Post || 'publish' !== $project->post_status ) {
		theme_skema_force_404();
	}

	$target = get_permalink( $project );
	if ( ! is_string( $target ) || $target === '' ) {
		theme_skema_force_404();
	}

	wp_safe_redirect( theme_skema_url_with_public_query( $target, array( 'landings' ) ), 301 );
	exit;
}

/**
 * Destino del 301, o cadena vacía si la ruta no es antigua.
 *
 * @param string $request Ruta sin dominio ni barras de los extremos.
 * @return string
 */
function theme_skema_legacy_project_redirect_target( $request ) {
	if ( preg_match( '#^proyecto/([^/]+)$#', $request, $matches ) ) {
		$project = get_page_by_path( $matches[1], OBJECT, 'proyectos' );
		if ( ! $project instanceof WP_Post || 'publish' !== $project->post_status ) {
			return '';
		}

		return home_url( '/proyectos/' . rawurlencode( $project->post_name ) . '/' );
	}

	if ( ! preg_match( '#^landings/([^/]+)$#', $request, $matches ) ) {
		return '';
	}

	$landing = get_page_by_path( $matches[1], OBJECT, 'landings' );
	if ( ! $landing instanceof WP_Post || 'publish' !== $landing->post_status ) {
		return '';
	}

	$project_id = theme_skema_landing_project_id( $landing->ID );
	if ( $project_id <= 0 ) {
		return '';
	}

	$project = get_post( $project_id );
	if ( ! $project instanceof WP_Post || $project->post_name === '' ) {
		return '';
	}

	return home_url( '/proyectos/' . rawurlencode( $project->post_name ) . '/' );
}

/**
 * La prelanding no se indexa, aunque se vea en la URL del proyecto.
 *
 * @return bool
 */
function theme_skema_viewing_prelanding() {
	$landing_id = theme_skema_viewing_landing_id();
	if ( $landing_id <= 0 || ! function_exists( 'theme_skema_get_landing_phase' ) ) {
		return false;
	}

	return 'prelanding' === theme_skema_get_landing_phase( $landing_id );
}

/**
 * @param array<string, bool|string> $robots Directivas de WordPress.
 * @return array<string, bool|string>
 */
function theme_skema_prelanding_wp_robots( $robots ) {
	if ( ! theme_skema_viewing_prelanding() ) {
		return $robots;
	}

	$robots['noindex'] = true;
	$robots['follow']  = true;
	unset( $robots['index'] );

	return $robots;
}
add_filter( 'wp_robots', 'theme_skema_prelanding_wp_robots' );

/**
 * SEOPress ya armó «index, follow». En prelanding lo sustituye por noindex.
 *
 * @param array<int, string> $attrs Fragmentos del content de robots.
 * @return array<int, string>
 */
function theme_skema_prelanding_seopress_robots( $attrs ) {
	if ( ! theme_skema_viewing_prelanding() || ! is_array( $attrs ) ) {
		return $attrs;
	}

	$next = array( 'noindex', 'follow' );
	foreach ( $attrs as $attr ) {
		if ( ! is_string( $attr ) ) {
			continue;
		}
		if ( $attr === 'index' || $attr === 'follow' || $attr === 'index, follow' || $attr === 'noindex' ) {
			continue;
		}
		if ( strpos( $attr, 'max-' ) === 0 ) {
			continue;
		}
		$next[] = $attr;
	}

	return $next;
}
add_filter( 'seopress_titles_robots_attrs', 'theme_skema_prelanding_seopress_robots' );

/**
 * Pinta la landing vinculada dentro de la URL del proyecto.
 *
 * @param int $landing_id ID de la landing publicada.
 */
function theme_skema_render_linked_landing( $landing_id ) {
	$landing = get_post( (int) $landing_id );
	if ( ! $landing instanceof WP_Post || 'landings' !== $landing->post_type ) {
		return;
	}

	global $post;
	$post = $landing;
	setup_postdata( $landing );

	$variant = function_exists( 'theme_skema_get_landing_variant' )
		? theme_skema_get_landing_variant( $landing->ID )
		: 'default';
	$phase = function_exists( 'theme_skema_get_landing_phase' )
		? theme_skema_get_landing_phase( $landing->ID )
		: 'prelanding';

	get_header();
	?>
<main id="primary"
    class="site-main site-main--landings landing-skin landing-skin--<?php echo esc_attr( $variant ); ?> landing-phase--<?php echo esc_attr( $phase ); ?>">
    <article id="post-<?php echo (int) $landing->ID; ?>" <?php post_class( '', $landing->ID ); ?>>
        <div class="entry-content">
            <?php
			if ( function_exists( 'theme_skema_landing_entry_template' ) ) {
				theme_skema_landing_entry_template();
			} else {
				echo apply_filters( 'the_content', $landing->post_content );
			}
			?>
        </div>
    </article>
</main>
	<?php
	get_footer( 'landings' );
	wp_reset_postdata();
}
