<?php
/**
 * Landings (fase completa): ficha del apartamento con iconos Font Awesome (ACF).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitiza el atributo `class` para iconos Font Awesome: solo tokens que empiezan por "fa".
 *
 * @param mixed $raw Valor del campo ACF (texto libre).
 * @return string Clases unidas por un espacio o cadena vacía.
 */
function theme_skema_sanitize_fontawesome_class_attr( $raw ) {
	if ( ! is_string( $raw ) ) {
		return '';
	}

	$tokens = preg_split( '/\s+/', trim( $raw ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $tokens ) ) {
		return '';
	}

	$safe = array();
	foreach ( $tokens as $token ) {
		if ( ! is_string( $token ) || $token === '' ) {
			continue;
		}
		if ( preg_match( '/\Afa[a-z0-9-]*\z/', $token ) ) {
			$safe[] = $token;
		}
	}

	return implode( ' ', $safe );
}

/**
 * Comprueba si una fila de ítem de ficha tiene texto visible.
 *
 * @param array<string, mixed> $row Fila del repetidor interno.
 * @return bool
 */
function theme_skema_landing_ficha_apto_item_row_is_valid( $row ) {
	if ( ! is_array( $row ) ) {
		return false;
	}

	$texto = isset( $row['texto'] ) ? trim( (string) $row['texto'] ) : '';

	return $texto !== '';
}

/**
 * Devuelve categorías con ítems válidos para pintar la ficha del apartamento.
 *
 * @param int $post_id ID de la entrada landings.
 * @return array<int, array{titulo: string, items: array<int, array{fa_classes: string, texto: string}>}>
 */
function theme_skema_landing_get_ficha_apartamento_categorias_for_display( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_ficha_apto_categorias', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$out = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$titulo = isset( $row['titulo'] ) ? trim( (string) $row['titulo'] ) : '';
		if ( $titulo === '' ) {
			continue;
		}

		$inner = isset( $row['items'] ) ? $row['items'] : null;
		if ( ! is_array( $inner ) ) {
			continue;
		}

		$items = array();
		foreach ( $inner as $item_row ) {
			if ( ! theme_skema_landing_ficha_apto_item_row_is_valid( $item_row ) ) {
				continue;
			}
			$texto     = trim( (string) $item_row['texto'] );
			$fa_raw    = isset( $item_row['fa_classes'] ) ? $item_row['fa_classes'] : '';
			$fa_safe   = theme_skema_sanitize_fontawesome_class_attr( $fa_raw );
			$items[]   = array(
				'fa_classes' => $fa_safe,
				'texto'      => $texto,
			);
		}

		if ( count( $items ) === 0 ) {
			continue;
		}

		$out[] = array(
			'titulo' => $titulo,
			'items'  => $items,
		);
	}

	return $out;
}

/**
 * Ítems de la lista «Zonas sociales» (texto + opcional icono Font Awesome).
 *
 * @param int $post_id ID de la entrada landings.
 * @return array<int, array{fa_classes: string, texto: string}>
 */
function theme_skema_landing_get_zonas_sociales_items_for_display( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_zonas_items', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$out = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$texto = isset( $row['texto'] ) ? trim( (string) $row['texto'] ) : '';
		if ( $texto === '' ) {
			continue;
		}

		$fa_raw = isset( $row['fa_classes'] ) ? $row['fa_classes'] : '';
		$out[]  = array(
			'fa_classes' => theme_skema_sanitize_fontawesome_class_attr( $fa_raw ),
			'texto'      => $texto,
		);
	}

	return $out;
}

/**
 * Encola Font Awesome 6 (CSS completo) en single landings fase «landing».
 *
 * @return void
 */
function theme_skema_enqueue_fontawesome_on_landings() {
	wp_enqueue_style(
		'theme-skema-font-awesome-6',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',
		array(),
		'6.5.2'
	);
}
