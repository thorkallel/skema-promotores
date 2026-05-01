<?php
/**
 * Custom Post Type: Landings (páginas de proyecto).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra el post type público `landings`.
 */
function theme_skema_register_landings_post_type() {
	$labels = array(
		'name'               => _x( 'Landings', 'Post type general name', 'theme_skema' ),
		'singular_name'      => _x( 'Landing', 'Post type singular name', 'theme_skema' ),
		'menu_name'          => _x( 'Landings', 'Admin Menu text', 'theme_skema' ),
		'name_admin_bar'     => _x( 'Landing', 'Add New on Toolbar', 'theme_skema' ),
		'add_new'            => __( 'Añadir nueva', 'theme_skema' ),
		'add_new_item'       => __( 'Añadir nueva landing', 'theme_skema' ),
		'new_item'           => __( 'Nueva landing', 'theme_skema' ),
		'edit_item'          => __( 'Editar landing', 'theme_skema' ),
		'view_item'          => __( 'Ver landing', 'theme_skema' ),
		'all_items'          => __( 'Todas las landings', 'theme_skema' ),
		'search_items'       => __( 'Buscar landings', 'theme_skema' ),
		'not_found'          => __( 'No se encontraron landings.', 'theme_skema' ),
		'not_found_in_trash' => __( 'No hay landings en la papelera.', 'theme_skema' ),
	);

	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'landings' ),
		'capability_type'     => 'post',
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-layout',
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' ),
		'show_in_rest'        => true,
	);

	register_post_type( 'landings', $args );
}
add_action( 'init', 'theme_skema_register_landings_post_type', 5 );
