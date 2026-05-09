<?php
/**
 * Landings: el menú y pantallas de administración del CPT solo para usuarios autorizados.
 *
 * Configuración:
 * - Opción `theme_skema_landings_menu_user_ids` (array de IDs enteros), p. ej. via WP-CLI:
 *   `wp option update theme_skema_landings_menu_user_ids '[5]' --format=json`
 * - O filtro `theme_skema_landings_menu_user_ids` para añadir IDs desde un plugin o child theme.
 *
 * Si hay al menos un ID en la lista (opción BD o filtro `theme_skema_landings_menu_user_ids`),
 * por defecto solo esos usuarios ven Landings: `manage_options` no amplía el acceso.
 * Para que todos los administradores sigan viendo Landings además de la lista, usad:
 * `add_filter( 'theme_skema_landings_menu_allow_manage_options_bypass', '__return_true' );`
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normaliza una lista de IDs de usuario.
 *
 * @param mixed $raw Lista cruda.
 * @return int[]
 */
function theme_skema_landings_normalize_user_id_list( $raw ) {
	if ( ! is_array( $raw ) ) {
		return array();
	}

	$out = array();
	foreach ( $raw as $id ) {
		$n = (int) $id;
		if ( $n > 0 ) {
			$out[] = $n;
		}
	}

	return array_values( array_unique( $out, SORT_NUMERIC ) );
}

/**
 * IDs de usuario que pueden ver y usar el admin de Landings.
 *
 * @return int[]
 */
function theme_skema_landings_get_menu_allowed_user_ids() {
	$from_option = get_option( 'theme_skema_landings_menu_user_ids', array() );
	if ( ! is_array( $from_option ) ) {
		$from_option = array();
	}

	$ids = theme_skema_landings_normalize_user_id_list( $from_option );

	return apply_filters( 'theme_skema_landings_menu_user_ids', $ids );
}

/**
 * Comprueba si el usuario puede acceder a listados, edición y menú de Landings en el admin.
 *
 * @param int|null $user_id ID de usuario o null para el usuario actual.
 * @return bool
 */
function theme_skema_user_may_access_landings_admin_ui( $user_id = null ) {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	$allowed_ids = theme_skema_landings_get_menu_allowed_user_ids();
	$has_allowlist = ! empty( $allowed_ids );

	// Lista vacía: comportamiento clásico (quien tenga manage_options ve Landings en admin).
	// Lista con IDs: solo esos usuarios, salvo que el filtro fuerce el bypass para manage_options.
	$default_manage_options_bypass = ! $has_allowlist;
	$allow_manage_options_bypass   = apply_filters(
		'theme_skema_landings_menu_allow_manage_options_bypass',
		$default_manage_options_bypass
	);

	if ( $allow_manage_options_bypass && user_can( $user_id, 'manage_options' ) ) {
		return true;
	}

	if ( ! $has_allowlist ) {
		return false;
	}

	return in_array( (int) $user_id, $allowed_ids, true );
}

/**
 * Quita el ítem de menú Landings para quien no esté autorizado.
 */
function theme_skema_landings_hide_admin_menu_for_unauthorized() {
	if ( theme_skema_user_may_access_landings_admin_ui() ) {
		return;
	}

	remove_menu_page( 'edit.php?post_type=landings' );
}
add_action( 'admin_menu', 'theme_skema_landings_hide_admin_menu_for_unauthorized', 999 );

/**
 * Quita «Landing» del menú «+ Nuevo» en la barra de administración.
 *
 * @param WP_Admin_Bar $wp_admin_bar Barra de administración.
 */
function theme_skema_landings_remove_admin_bar_new_node( $wp_admin_bar ) {
	if ( theme_skema_user_may_access_landings_admin_ui() ) {
		return;
	}

	$wp_admin_bar->remove_node( 'new-landings' );
}
add_action( 'admin_bar_menu', 'theme_skema_landings_remove_admin_bar_new_node', 999 );

/**
 * Evita abrir listado, alta o edición de landings por URL directa sin permiso de UI.
 */
function theme_skema_landings_block_admin_screens_for_unauthorized() {
	if ( theme_skema_user_may_access_landings_admin_ui() ) {
		return;
	}

	global $pagenow;

	$post_type_get = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
	if ( 'edit.php' === $pagenow && 'landings' === $post_type_get ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	if ( 'post-new.php' === $pagenow && 'landings' === $post_type_get ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	if ( 'post.php' !== $pagenow || ! isset( $_GET['post'] ) ) {
		return;
	}

	$post_id = (int) $_GET['post'];
	if ( $post_id <= 0 ) {
		return;
	}

	if ( 'landings' !== get_post_type( $post_id ) ) {
		return;
	}

	wp_safe_redirect( admin_url() );
	exit;
}
add_action( 'admin_init', 'theme_skema_landings_block_admin_screens_for_unauthorized' );
