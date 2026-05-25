<?php
/**
 * Tab «Banner principal» (ACF en BD): extensión desde el tema.
 *
 * La pestaña usa la key del campo Tab definida en el escritorio de ACF.
 * Aquí se pueden añadir opciones al campo `tipo_banner` y registrar campos locales
 * dentro del mismo grupo (misma metabox), quedando visualmente bajo ese tab.
 *
 * El repeater `slider_youtube` se registra aquí (mismo grupo que el tab), con condicional
 * «tipo_banner == slider_youtube», para alinearlo con Vídeo y Slider.
 *
 * Campos extra (opcional), desde functions.php o un plugin:
 *
 *     add_filter( 'theme_skema_acf_banner_principal_extra_fields', function ( $fields ) {
 *         $fields[] = array(
 *             'key'   => 'field_skema_banner_mi_campo',
 *             'label' => __( 'Mi campo', 'theme_skema' ),
 *             'name'  => 'banner_mi_campo',
 *             'type'  => 'text',
 *         );
 *         return $fields;
 *     } );
 *
 * Opciones adicionales para `tipo_banner` (solo se añaden claves que no existan en ACF):
 *
 *     add_filter( 'theme_skema_acf_tipo_banner_extra_choices', function ( $choices ) {
 *         $choices['mi_valor'] = __( 'Mi tipo', 'theme_skema' );
 *         return $choices;
 *     } );
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Key del campo Tab «Banner principal» (inspector HTML: data-key en .acf-tab-button).
 *
 * Si en otro entorno el tab se recreó en ACF, sobrescribe la key sin tocar el tema:
 *
 *     add_filter( 'theme_skema_acf_tab_banner_principal_key', function () {
 *         return 'field_TU_KEY_DEL_TAB';
 *     } );
 *
 * Si esta key no existe en la BD, el tema intenta localizar el grupo de la página
 * `theme_inicio.php` que contenga el campo `tipo_banner`.
 *
 * @var string
 */
const THEME_SKEMA_ACF_TAB_BANNER_PRINCIPAL_KEY = 'field_66316be4548f5';

/**
 * Etiquetas por defecto que el tema conoce para `tipo_banner` (solo rellenan claves vacías en ACF).
 *
 * @return array<string, string>
 */
function theme_skema_acf_default_tipo_banner_labels_for_missing_keys() {
	return array(
		'Video'          => __( 'Vídeo', 'theme_skema' ),
		'Slider'         => __( 'Slider de imágenes', 'theme_skema' ),
		'slider_youtube' => __( 'Slider YouTube', 'theme_skema' ),
	);
}

/**
 * Completa opciones y etiquetas del campo `tipo_banner` sin borrar lo definido en el administrador.
 *
 * @param array $field Campo ACF.
 * @return array
 */
function theme_skema_acf_load_tipo_banner_choices( $field ) {
	if ( ! is_array( $field ) ) {
		return $field;
	}
	if ( 'tipo_banner' !== ( $field['name'] ?? '' ) ) {
		return $field;
	}
	if ( ! isset( $field['choices'] ) || ! is_array( $field['choices'] ) ) {
		$field['choices'] = array();
	}

	$extra = apply_filters( 'theme_skema_acf_tipo_banner_extra_choices', array() );
	if ( ! is_array( $extra ) ) {
		$extra = array();
	}
	$extra['slider_youtube'] = __( 'Slider YouTube', 'theme_skema' );

	foreach ( $extra as $value => $label ) {
		if ( is_string( $value ) && $value !== '' && ! isset( $field['choices'][ $value ] ) ) {
			$field['choices'][ $value ] = $label;
		}
	}

	$labels = theme_skema_acf_default_tipo_banner_labels_for_missing_keys();
	foreach ( $labels as $value => $label ) {
		if ( isset( $field['choices'][ $value ] ) && '' === trim( (string) $field['choices'][ $value ] ) ) {
			$field['choices'][ $value ] = $label;
		}
	}

	return $field;
}
add_filter( 'acf/load_field/name=tipo_banner', 'theme_skema_acf_load_tipo_banner_choices', 20 );

/**
 * Resuelve la key del tab «Banner principal» (filtro o constante).
 *
 * @return string
 */
function theme_skema_acf_tab_banner_principal_field_key() {
	$key = apply_filters( 'theme_skema_acf_tab_banner_principal_key', THEME_SKEMA_ACF_TAB_BANNER_PRINCIPAL_KEY );
	return is_string( $key ) ? $key : '';
}

/**
 * Obtiene la key del grupo ACF padre a partir de la key de un campo Tab.
 *
 * @param string $tab_field_key Key del campo tipo `tab`.
 * @return string Cadena vacía si no existe.
 */
function theme_skema_acf_parent_group_key_from_tab_field( $tab_field_key ) {
	if ( ! function_exists( 'acf_get_field' ) ) {
		return '';
	}

	if ( ! is_string( $tab_field_key ) || $tab_field_key === '' ) {
		return '';
	}

	$tab = acf_get_field( $tab_field_key );
	if ( ! is_array( $tab ) ) {
		return '';
	}

	$parent = $tab['parent'] ?? '';
	return is_string( $parent ) && $parent !== '' ? $parent : '';
}

/**
 * Indica si un grupo ACF está ligado a la plantilla de página `theme_inicio.php`.
 *
 * @param array<string, mixed> $group Definición de grupo (`location`).
 * @return bool
 */
function theme_skema_acf_field_group_targets_theme_inicio_template( array $group ) {
	$locations = $group['location'] ?? array();
	if ( ! is_array( $locations ) ) {
		return false;
	}

	foreach ( $locations as $or_group ) {
		if ( ! is_array( $or_group ) ) {
			continue;
		}

		foreach ( $or_group as $rule ) {
			if ( ! is_array( $rule ) ) {
				continue;
			}

			if ( 'page_template' !== ( $rule['param'] ?? '' ) ) {
				continue;
			}

			$value = $rule['value'] ?? '';
			if ( ! is_string( $value ) || $value === '' ) {
				continue;
			}

			if ( 'theme_inicio.php' === $value ) {
				return true;
			}

			if ( preg_match( '/(^|\/)theme_inicio\.php$/', $value ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Localiza un grupo de campos aplicado a la plantilla Theme Inicio que declara `tipo_banner`.
 *
 * @return string Key del grupo o cadena vacía.
 */
function theme_skema_acf_inicio_group_key_containing_tipo_banner() {
	if ( ! function_exists( 'acf_get_field_groups' ) ) {
		return '';
	}

	$groups = acf_get_field_groups();
	if ( ! is_array( $groups ) ) {
		return '';
	}

	foreach ( $groups as $group ) {
		if ( ! is_array( $group ) ) {
			continue;
		}

		if ( ! theme_skema_acf_field_group_targets_theme_inicio_template( $group ) ) {
			continue;
		}

		$key = $group['key'] ?? '';
		if ( ! is_string( $key ) || $key === '' ) {
			continue;
		}

		if ( theme_skema_acf_resolve_tipo_banner_field_key( $key ) !== '' ) {
			return $key;
		}
	}

	return '';
}

/**
 * Obtiene la key del grupo ACF padre del tab «Banner principal».
 *
 * @return string Cadena vacía si no se puede resolver.
 */
function theme_skema_acf_banner_principal_parent_group_key() {
	$tab_key = theme_skema_acf_tab_banner_principal_field_key();
	$parent  = theme_skema_acf_parent_group_key_from_tab_field( $tab_key );

	if ( $parent !== '' ) {
		return $parent;
	}

	return theme_skema_acf_inicio_group_key_containing_tipo_banner();
}

/**
 * Recorre un árbol de definiciones de campos ACF y devuelve la key del primero con `name` dado.
 *
 * @param array<int, mixed> $fields Campos (y subcampos).
 * @param string            $field_name Nombre del campo (p. ej. tipo_banner).
 * @return string Key ACF o cadena vacía.
 */
function theme_skema_acf_walk_fields_find_key_by_name( array $fields, $field_name ) {
	if ( ! is_string( $field_name ) || $field_name === '' ) {
		return '';
	}

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		if ( $field_name === ( $field['name'] ?? '' ) ) {
			$key = $field['key'] ?? '';
			return is_string( $key ) ? $key : '';
		}

		$sub = $field['sub_fields'] ?? null;
		if ( is_array( $sub ) && array() !== $sub ) {
			$found = theme_skema_acf_walk_fields_find_key_by_name( $sub, $field_name );
			if ( $found !== '' ) {
				return $found;
			}
		}
	}

	return '';
}

/**
 * Key del campo `tipo_banner` dentro del grupo de inicio (para condicionales ACF).
 *
 * Recorre subcampos (p. ej. campo Group) porque `acf_get_fields` solo devuelve un nivel.
 *
 * @param string $group_key Key del grupo ACF.
 * @return string
 */
function theme_skema_acf_resolve_tipo_banner_field_key( $group_key ) {
	if ( ! function_exists( 'acf_get_fields' ) ) {
		return '';
	}

	if ( ! is_string( $group_key ) || $group_key === '' ) {
		return '';
	}

	$fields = acf_get_fields( $group_key );
	if ( ! is_array( $fields ) ) {
		return '';
	}

	return theme_skema_acf_walk_fields_find_key_by_name( $fields, 'tipo_banner' );
}

/**
 * Mayor `menu_order` entre el tab «Banner principal» y el siguiente tab (campos ya guardados en BD).
 *
 * @param string $group_key Key del grupo ACF.
 * @return int
 */
function theme_skema_acf_banner_tab_section_max_menu_order( $group_key ) {
	if ( ! function_exists( 'acf_get_fields' ) ) {
		return 0;
	}

	$fields = acf_get_fields( $group_key );
	if ( ! is_array( $fields ) ) {
		return 0;
	}

	$inside_banner_tab = false;
	$max_order         = 0;
	$tab_key_current   = theme_skema_acf_tab_banner_principal_field_key();

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		$key  = $field['key'] ?? '';
		$type = $field['type'] ?? '';
		$ord  = isset( $field['menu_order'] ) ? (int) $field['menu_order'] : 0;

		if ( $tab_key_current !== '' && $tab_key_current === $key ) {
			$inside_banner_tab = true;
			continue;
		}

		if ( ! $inside_banner_tab ) {
			continue;
		}

		if ( 'tab' === $type ) {
			break;
		}

		if ( $ord > $max_order ) {
			$max_order = $ord;
		}
	}

	return $max_order;
}

/**
 * `menu_order` base para colocar campos extra justo después de `tipo_banner`.
 *
 * @param string $group_key Key del grupo ACF.
 * @return int
 */
function theme_skema_acf_banner_menu_order_after_tipo_banner( $group_key ) {
	if ( ! function_exists( 'acf_get_fields' ) ) {
		return 200;
	}

	$fields = acf_get_fields( $group_key );
	if ( ! is_array( $fields ) ) {
		return 200;
	}

	$tipo = theme_skema_acf_walk_fields_find_field_by_name( $fields, 'tipo_banner' );
	if ( is_array( $tipo ) ) {
		return isset( $tipo['menu_order'] ) ? (int) $tipo['menu_order'] + 1 : 1;
	}

	return 200;
}

/**
 * Devuelve el primer campo del árbol con el nombre indicado (para leer menu_order, etc.).
 *
 * @param array<int, mixed> $fields Árbol de campos ACF.
 * @param string            $field_name Nombre del campo.
 * @return array<string, mixed>|null
 */
function theme_skema_acf_walk_fields_find_field_by_name( array $fields, $field_name ) {
	if ( ! is_string( $field_name ) || $field_name === '' ) {
		return null;
	}

	foreach ( $fields as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}

		if ( $field_name === ( $field['name'] ?? '' ) ) {
			return $field;
		}

		$sub = $field['sub_fields'] ?? null;
		if ( is_array( $sub ) && array() !== $sub ) {
			$found = theme_skema_acf_walk_fields_find_field_by_name( $sub, $field_name );
			if ( null !== $found ) {
				return $found;
			}
		}
	}

	return null;
}

/**
 * Definición del repeater de slides YouTube (misma `name` que usa theme_inicio.php).
 *
 * @param string $group_key       Key del grupo ACF padre.
 * @param string $tipo_banner_key Key del campo tipo_banner (condicional).
 * @param int    $menu_order      Orden dentro del tab.
 * @return array<string, mixed>
 */
function theme_skema_acf_banner_principal_slider_youtube_field_def( $group_key, $tipo_banner_key, $menu_order ) {
	$conditional = array();
	if ( $tipo_banner_key !== '' ) {
		$conditional = array(
			array(
				array(
					'field'    => $tipo_banner_key,
					'operator' => '==',
					'value'    => 'slider_youtube',
				),
			),
		);
	}

	return array(
		'key'               => 'field_skema_slider_youtube',
		'label'             => __( 'Slides YouTube (cabecera)', 'theme_skema' ),
		'name'              => 'slider_youtube',
		'type'              => 'repeater',
		'parent'            => $group_key,
		'menu_order'        => $menu_order,
		'instructions'      => __( 'Una fila por cada vídeo. Visible cuando el tipo de banner es «Slider YouTube».', 'theme_skema' ),
		'layout'            => 'block',
		'button_label'      => __( 'Añadir slide', 'theme_skema' ),
		'conditional_logic' => $conditional,
		'sub_fields'        => array(
			array(
				'key'   => 'field_skema_slider_youtube_url',
				'label' => __( 'URL de YouTube', 'theme_skema' ),
				'name'  => 'youtube_url',
				'type'  => 'url',
			),
			array(
				'key'           => 'field_skema_slider_youtube_poster',
				'label'         => __( 'Imagen poster (opcional)', 'theme_skema' ),
				'name'          => 'poster',
				'type'          => 'image',
				'return_format' => 'url',
				'preview_size'  => 'medium',
			),
		),
	);
}

/**
 * Registra repeater YouTube + campos extra en el grupo del tab «Banner principal».
 */
function theme_skema_acf_register_banner_principal_local_fields() {
	if ( ! function_exists( 'acf_add_local_field' ) ) {
		return;
	}

	$group_key = theme_skema_acf_banner_principal_parent_group_key();
	if ( $group_key === '' ) {
		return;
	}

	$tipo_banner_key = theme_skema_acf_resolve_tipo_banner_field_key( $group_key );

	$section_max = theme_skema_acf_banner_tab_section_max_menu_order( $group_key );
	$yt_order    = $section_max + 1;

	if ( $section_max <= 0 ) {
		$fields_root = function_exists( 'acf_get_fields' ) ? acf_get_fields( $group_key ) : null;
		if ( is_array( $fields_root ) ) {
			$tipo_field = theme_skema_acf_walk_fields_find_field_by_name( $fields_root, 'tipo_banner' );
			if ( is_array( $tipo_field ) ) {
				$tipo_ord = isset( $tipo_field['menu_order'] ) ? (int) $tipo_field['menu_order'] : 0;
				$yt_order = $tipo_ord + 1;
			}
		}
	}

	if ( $tipo_banner_key !== '' ) {
		$yt_field = theme_skema_acf_banner_principal_slider_youtube_field_def( $group_key, $tipo_banner_key, $yt_order );
		acf_add_local_field( $yt_field );
	}

	$extra = apply_filters( 'theme_skema_acf_banner_principal_extra_fields', array() );
	if ( ! is_array( $extra ) || array() === $extra ) {
		return;
	}

	$base = $tipo_banner_key !== '' ? $yt_order + 1 : theme_skema_acf_banner_menu_order_after_tipo_banner( $group_key );
	$i    = 0;

	foreach ( $extra as $field ) {
		if ( ! is_array( $field ) ) {
			continue;
		}
		if ( empty( $field['key'] ) || empty( $field['name'] ) || empty( $field['type'] ) ) {
			continue;
		}

		$field['parent'] = $group_key;
		if ( ! isset( $field['menu_order'] ) ) {
			$field['menu_order'] = $base + $i;
			$i++;
		}

		acf_add_local_field( $field );
	}
}
add_action( 'acf/init', 'theme_skema_acf_register_banner_principal_local_fields', 25 );
