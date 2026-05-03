<?php
/**
 * Landings: bloque «Información del proyecto» (lista etiqueta + valor, estilo Renvia).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Etiquetas HTML permitidas en el valor (enlaces, énfasis).
 *
 * @return array<string, array<string, bool>>
 */
function theme_skema_landing_info_proyecto_allowed_html() {
	return array(
		'a'      => array(
			'href'   => true,
			'title'  => true,
			'target' => true,
			'rel'    => true,
			'class'  => true,
		),
		'br'     => array(),
		'strong' => array(),
		'b'      => array(),
		'em'     => array(),
		'i'      => array(),
		'span'   => array( 'class' => true ),
	);
}

/**
 * Normaliza la etiqueta para mostrarla antes de los dos puntos (evita «Nombre::»).
 *
 * @param string $raw Texto del campo ACF.
 * @return string
 */
function theme_skema_landing_info_proyecto_format_label( $raw ) {
	$s = trim( (string) $raw );

	return rtrim( $s, " \t\n\r\0\x0B:" );
}

/**
 * Comprueba si una fila del repetidor es válida para listar.
 *
 * @param array<string, mixed> $row Fila ACF.
 * @return bool
 */
function theme_skema_landing_info_proyecto_row_is_valid( $row ) {
	if ( ! is_array( $row ) ) {
		return false;
	}

	$etiqueta = theme_skema_landing_info_proyecto_format_label( isset( $row['etiqueta'] ) ? (string) $row['etiqueta'] : '' );
	$valor    = isset( $row['valor'] ) ? trim( (string) $row['valor'] ) : '';

	return $etiqueta !== '' && $valor !== '';
}

/**
 * Filas listas para pintar (etiqueta formateada + valor en bruto para wp_kses).
 *
 * @param int $post_id ID de la entrada landings.
 * @return array<int, array{etiqueta: string, valor: string}>
 */
function theme_skema_landing_get_info_proyecto_rows_for_display( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'skema_lland_info_proyecto_items', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$out = array();
	foreach ( $rows as $row ) {
		if ( ! theme_skema_landing_info_proyecto_row_is_valid( $row ) ) {
			continue;
		}
		$out[] = array(
			'etiqueta' => theme_skema_landing_info_proyecto_format_label( $row['etiqueta'] ),
			'valor'    => (string) $row['valor'],
		);
	}

	return $out;
}
