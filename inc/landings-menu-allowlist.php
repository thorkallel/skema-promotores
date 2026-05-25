<?php
/**
 * Landings: allowlist de IDs de usuario vía código (opción C — filtro).
 *
 * Se fusiona con la opción de base de datos `theme_skema_landings_menu_user_ids`
 * y con otros callbacks del mismo filtro.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * IDs de usuario que deben ver el menú Landings y poder abrir sus pantallas de admin.
 * Un ID por línea; solo números enteros positivos.
 *
 * Cómo obtener el ID: en WP Admin, Usuarios → Editar usuario; en la URL aparece user_id=12.
 *
 * @var int[]
 */
function theme_skema_landings_menu_get_ids_from_theme_config() {
	return array(
		1,
		6,
		// 12,
		// 34,
	);
}

/**
 * Mezcla los IDs configurados en el tema con los que ya vienen del filtro (p. ej. opción en BD).
 *
 * @param int[] $ids IDs acumulados hasta este filtro.
 * @return int[]
 */
function theme_skema_landings_menu_merge_allowlist_from_theme( $ids ) {
	if ( ! is_array( $ids ) ) {
		$ids = array();
	}

	$from_theme = theme_skema_landings_menu_get_ids_from_theme_config();
	if ( empty( $from_theme ) ) {
		return $ids;
	}

	$normalized = array();
	foreach ( $from_theme as $user_id ) {
		$n = (int) $user_id;
		if ( $n > 0 ) {
			$normalized[] = $n;
		}
	}

	if ( empty( $normalized ) ) {
		return $ids;
	}

	return array_values( array_unique( array_merge( $ids, $normalized ), SORT_NUMERIC ) );
}
add_filter( 'theme_skema_landings_menu_user_ids', 'theme_skema_landings_menu_merge_allowlist_from_theme' );