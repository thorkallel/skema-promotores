<?php
/**
 * Campos ACF locales: hero YouTube y carrusel proyectos (inicio).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Añade la opción de cabecera con vídeos de YouTube al campo existente tipo_banner.
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
	if ( ! isset( $field['choices']['slider_youtube'] ) ) {
		$field['choices']['slider_youtube'] = __( 'Slider YouTube', 'theme_skema' );
	}
	return $field;
}
add_filter( 'acf/load_field/name=tipo_banner', 'theme_skema_acf_load_tipo_banner_choices', 20 );

/**
 * Registra el grupo de campos para la plantilla Theme Inicio.
 */
function theme_skema_acf_register_inicio_extendido() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_inicio_extendido',
			'title'                 => __( 'Inicio — YouTube y proyectos', 'theme_skema' ),
			'fields'                => array(
				array(
					'key'          => 'field_skema_slider_youtube',
					'label'        => __( 'Slides YouTube (cabecera)', 'theme_skema' ),
					'name'         => 'slider_youtube',
					'type'         => 'repeater',
					'instructions' => __( 'Usar cuando el tipo de banner sea «Slider YouTube». Una fila por cada vídeo.', 'theme_skema' ),
					'layout'       => 'block',
					'button_label' => __( 'Añadir slide', 'theme_skema' ),
					'sub_fields'   => array(
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
				),
				array(
					'key'           => 'field_skema_inicio_renvia_titulo',
					'label'         => __( 'Proyectos: título del bloque', 'theme_skema' ),
					'name'          => 'inicio_renvia_titulo',
					'type'          => 'text',
					'instructions'  => __( 'Si está vacío y hay entradas en el carrusel, se usa un título por defecto.', 'theme_skema' ),
					'default_value' => '',
				),
				array(
					'key'          => 'field_skema_inicio_renvia_subtitulo',
					'label'        => __( 'Proyectos: subtítulo', 'theme_skema' ),
					'name'         => 'inicio_renvia_subtitulo',
					'type'         => 'text',
					'instructions' => __( 'Texto pequeño encima del título (opcional).', 'theme_skema' ),
				),
				array(
					'key'           => 'field_skema_inicio_renvia_relacion',
					'label'         => __( 'Proyectos: carrusel (proyectos y landings)', 'theme_skema' ),
					'name'          => 'inicio_renvia_relacion',
					'type'          => 'relationship',
					'instructions'  => __( 'Hasta 4 visibles en escritorio; orden manual. Para excluir entradas sin precio ni tipología: add_filter( \'theme_skema_inicio_renvia_requiere_precio_tipologia\', \'__return_true\' ); en functions.php.', 'theme_skema' ),
					'post_type'     => array(
						'proyectos',
						'landings',
					),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'min'           => 0,
					'max'           => 0,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'theme_inicio.php',
					),
				),
			),
			'menu_order'            => 5,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'theme_skema_acf_register_inicio_extendido' );
