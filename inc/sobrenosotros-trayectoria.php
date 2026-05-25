<?php
/**
 * Sobre nosotros — trayectoria (timeline): enlace al CPT proyectos y título oficial.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valor bruto del subcampo `proy` en la fila actual del repetidor (compatible con texto legado e ID).
 *
 * @return mixed
 */
function theme_skema_timeline_get_proy_raw() {
	$formatted = get_sub_field( 'proy' );
	if ( $formatted !== null && $formatted !== false && $formatted !== '' ) {
		return $formatted;
	}

	$unformatted = get_sub_field( 'proy', false );
	if ( $unformatted !== null && $unformatted !== false && $unformatted !== '' ) {
		return $unformatted;
	}

	$row = get_row( true );
	if ( ! is_array( $row ) || ! isset( $row['proy'] ) ) {
		return null;
	}

	$legacy = $row['proy'];
	if ( $legacy === null || $legacy === false || $legacy === '' ) {
		return null;
	}

	return $legacy;
}

/**
 * Convierte el subcampo ACF `proy` (post object, ID o texto legado) en un ID de proyecto.
 *
 * @param mixed $raw Valor de theme_skema_timeline_get_proy_raw().
 * @return int ID de entrada `proyectos` o 0.
 */
function theme_skema_timeline_resolve_proyecto_post_id( $raw ) {
	if ( $raw === null || $raw === false || $raw === '' ) {
		return 0;
	}

	if ( is_numeric( $raw ) ) {
		$post_id = absint( $raw );
		return ( 'proyectos' === get_post_type( $post_id ) ) ? $post_id : 0;
	}

	if ( $raw instanceof WP_Post ) {
		return ( 'proyectos' === $raw->post_type ) ? (int) $raw->ID : 0;
	}

	if ( is_array( $raw ) ) {
		if ( ! empty( $raw['ID'] ) ) {
			$post_id = absint( $raw['ID'] );
			return ( 'proyectos' === get_post_type( $post_id ) ) ? $post_id : 0;
		}
		if ( ! empty( $raw['id'] ) ) {
			$post_id = absint( $raw['id'] );
			return ( 'proyectos' === get_post_type( $post_id ) ) ? $post_id : 0;
		}
	}

	if ( ! is_string( $raw ) ) {
		return 0;
	}

	$legacy_title = trim( wp_strip_all_tags( $raw ) );
	if ( $legacy_title === '' ) {
		return 0;
	}

	return theme_skema_timeline_find_proyecto_id_by_title( $legacy_title );
}

/**
 * Busca un proyecto por título (exacto y, si falla, comparación sin tildes).
 *
 * @param string $title Título a buscar.
 * @return int ID o 0.
 */
function theme_skema_timeline_find_proyecto_id_by_title( $title ) {
	global $wpdb;

	$title = trim( $title );
	if ( $title === '' ) {
		return 0;
	}

	$post_id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'proyectos'
			AND post_status IN ('publish', 'private', 'draft')
			AND post_title = %s
			LIMIT 1",
			$title
		)
	);

	if ( $post_id > 0 && 'proyectos' === get_post_type( $post_id ) ) {
		return $post_id;
	}

	$needle = theme_skema_timeline_normalize_title_for_match( $title );
	if ( $needle === '' ) {
		return 0;
	}

	$candidates = get_posts(
		array(
			'post_type'              => 'proyectos',
			'post_status'            => array( 'publish', 'private', 'draft' ),
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $candidates as $candidate_id ) {
		$candidate_title = get_the_title( $candidate_id );
		if ( ! is_string( $candidate_title ) || $candidate_title === '' ) {
			continue;
		}
		if ( theme_skema_timeline_normalize_title_for_match( $candidate_title ) === $needle ) {
			return (int) $candidate_id;
		}
	}

	return 0;
}

/**
 * Normaliza un título para emparejar legado sin tildes con el título del CPT.
 *
 * @param string $title Texto original.
 * @return string
 */
function theme_skema_timeline_normalize_title_for_match( $title ) {
	$title = remove_accents( wp_strip_all_tags( $title ) );
	$title = strtolower( $title );
	return trim( preg_replace( '/\s+/', ' ', $title ) );
}

/**
 * Título para el timeline: título oficial del CPT si hay enlace; si no, texto guardado en `proy`.
 *
 * @param mixed $raw Valor de theme_skema_timeline_get_proy_raw().
 * @return string Título listo para esc_html (puede estar vacío).
 */
function theme_skema_timeline_get_proyecto_title( $raw ) {
	$post_id = theme_skema_timeline_resolve_proyecto_post_id( $raw );
	if ( $post_id > 0 ) {
		$title = get_the_title( $post_id );
		return is_string( $title ) ? trim( $title ) : '';
	}

	if ( is_string( $raw ) ) {
		return trim( wp_strip_all_tags( $raw ) );
	}

	return '';
}

/**
 * Ciudad en el timeline: taxonomía / ACF del proyecto enlazado; si no, campo manual `ciu-m`.
 *
 * @param int   $post_id  ID de proyecto enlazado (0 si no hay).
 * @param mixed $fallback Valor de get_sub_field( 'ciu-m' ).
 * @return string Etiqueta de ciudad o cadena vacía.
 */
function theme_skema_timeline_get_ciudad_label( $post_id, $fallback ) {
	$post_id = absint( $post_id );
	if ( $post_id > 0 ) {
		$ciudad_texto = get_field( 'ciudad_texto', $post_id );
		if ( is_string( $ciudad_texto ) && trim( $ciudad_texto ) !== '' ) {
			return trim( $ciudad_texto );
		}

		$terms = wp_get_post_terms( $post_id, 'ciudad_proyecto' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			return $terms[0]->name;
		}
	}

	if ( is_string( $fallback ) ) {
		return trim( wp_strip_all_tags( $fallback ) );
	}

	return '';
}

/**
 * En el escritorio, `proy` es selector de proyecto; en el front se mantiene la definición ACF original (texto/ID).
 *
 * @param array<string, mixed> $field Definición del campo ACF.
 * @return array<string, mixed>
 */
function theme_skema_acf_load_field_proy_timeline( $field ) {
	if ( ! is_admin() ) {
		return $field;
	}

	if ( ( $field['name'] ?? '' ) !== 'proy' ) {
		return $field;
	}

	$field['type']          = 'post_object';
	$field['label']         = __( 'Proyecto', 'theme_skema' );
	$field['post_type']     = array( 'proyectos' );
	$field['return_format'] = 'id';
	$field['allow_null']    = 1;
	$field['multiple']      = 0;
	$field['ui']            = 1;
	$field['instructions']  = __( 'Selecciona el proyecto. En la web se muestra el título oficial de la entrada.', 'theme_skema' );

	return $field;
}

add_filter( 'acf/load_field/name=proy', 'theme_skema_acf_load_field_proy_timeline', 20 );
