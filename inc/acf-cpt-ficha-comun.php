<?php
/**
 * Campos ACF comunes para ficha / carrusel: proyectos y landings.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Campos reutilizables (mismos nombres en ambos CPT para unificar post_meta).
 *
 * @param string $context 'proyectos' | 'landings'.
 * @return array<int, array<string, mixed>>
 */
function theme_skema_acf_ficha_comun_fields( $context ) {
	$is_proyectos = ( 'proyectos' === $context );

	$fields = array(
		array(
			'key'          => 'field_skema_ficha_msg_' . $context,
			'label'        => '',
			'name'         => 'skema_ficha_instrucciones_' . $context,
			'type'         => 'message',
			'message'      => $is_proyectos
				? __( '<strong>Datos mínimos de ficha</strong>: título del proyecto, <strong>imagen destacada</strong>, términos de taxonomía <em>Tipo de proyecto</em> y <em>Ciudad del proyecto</em> (o el campo Ciudad abajo), y los campos obligatorios de esta caja.', 'theme_skema' )
				: __( '<strong>Datos mínimos de ficha</strong>: título, <strong>imagen destacada</strong> o «Imagen carrusel», y rellena todos los campos obligatorios de esta caja (tipo, ciudad, superficie, tipología, precio).', 'theme_skema' ),
			'new_lines'    => 'wpautop',
			'esc_html'     => 0,
		),
		array(
			'key'           => 'field_skema_ficha_imagen_carrusel_' . $context,
			'label'         => __( 'Imagen carrusel / ficha (opcional)', 'theme_skema' ),
			'name'          => 'imagen_carrusel',
			'type'          => 'image',
			'instructions'  => __( 'Si la rellenas, sustituye a la imagen destacada en el carrusel de inicio. Formatos recomendados: horizontal, buena calidad.', 'theme_skema' ),
			'return_format' => 'array',
			'preview_size'  => 'medium',
			'required'      => 0,
		),
		array(
			'key'           => 'field_skema_ficha_precio_' . $context,
			'label'         => __( 'Precio desde', 'theme_skema' ),
			'name'          => 'precio_desde',
			'type'          => 'text',
			'instructions'  => __( 'Ej.: 685.000 $ o COP 685.000.000', 'theme_skema' ),
			'required'      => 1,
			'placeholder'   => __( 'Precio', 'theme_skema' ),
		),
		array(
			'key'           => 'field_skema_ficha_tipologia_' . $context,
			'label'         => __( 'Tipología', 'theme_skema' ),
			'name'          => 'tipologia',
			'type'          => 'textarea',
			'rows'          => 2,
			'instructions'  => __( 'Ej.: 3 hab. · 2 baños · terraza 18 m²', 'theme_skema' ),
			'required'      => 1,
		),
		array(
			'key'           => 'field_skema_ficha_area_priv_' . $context,
			'label'         => __( 'Superficie', 'theme_skema' ),
			'name'          => 'area_priv',
			'type'          => 'text',
			'instructions'  => __( 'Área principal en m² (número) o texto corto (ej. desde 95 m²).', 'theme_skema' ),
			'required'      => 1,
		),
		array(
			'key'           => 'field_skema_ficha_area_const_' . $context,
			'label'         => __( 'Superficie construida (opcional)', 'theme_skema' ),
			'name'          => 'area_const',
			'type'          => 'text',
			'instructions'  => __( 'Solo si quieres mostrar un segundo dato de superficie en la ficha interior.', 'theme_skema' ),
			'required'      => 0,
		),
	);

	if ( $is_proyectos ) {
		// Tras «Imagen carrusel»: logo del desarrollo (misma meta que usa el carrusel de inicio).
		array_splice(
			$fields,
			2,
			0,
			array(
				array(
					'key'           => 'field_skema_ficha_logo_proyecto',
					'label'         => __( 'Logo del proyecto', 'theme_skema' ),
					'name'          => 'logo_proyecto',
					'type'          => 'image',
					'instructions'  => __( 'Opcional. Marca del desarrollo: carrusel de inicio, fichas y cabecera de la ficha del proyecto.', 'theme_skema' ),
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'required'      => 0,
				),
			)
		);
		$fields[] = array(
			'key'           => 'field_skema_ficha_ciudad_txt_p',
			'label'         => __( 'Ciudad (texto alternativo)', 'theme_skema' ),
			'name'          => 'ciudad_texto',
			'type'          => 'text',
			'instructions'  => __( 'Opcional. Si no hay término en la taxonomía «Ciudad del proyecto», se usará este texto en el carrusel.', 'theme_skema' ),
			'required'      => 0,
		);
		$fields[] = array(
			'key'           => 'field_skema_ficha_tipo_txt_p',
			'label'         => __( 'Tipo de proyecto (texto alternativo)', 'theme_skema' ),
			'name'          => 'tipo_proyecto_text',
			'type'          => 'text',
			'instructions'  => __( 'Opcional. Si lo rellenas, sustituye al nombre del término de la taxonomía «Tipo de proyecto» en el carrusel.', 'theme_skema' ),
			'required'      => 0,
		);
	} else {
		$fields[] = array(
			'key'           => 'field_skema_ficha_tipo_txt_l',
			'label'         => __( 'Tipo de proyecto', 'theme_skema' ),
			'name'          => 'tipo_proyecto_text',
			'type'          => 'text',
			'instructions'  => __( 'Ej.: Residencial, Inversión, Comercial.', 'theme_skema' ),
			'required'      => 1,
		);
		$fields[] = array(
			'key'           => 'field_skema_ficha_ciudad_txt_l',
			'label'         => __( 'Ciudad / ubicación', 'theme_skema' ),
			'name'          => 'ciudad_texto',
			'type'          => 'text',
			'instructions'  => __( 'Ej.: Cali, Valle del Cauca', 'theme_skema' ),
			'required'      => 1,
		);
	}

	return $fields;
}

/**
 * Registra grupos ACF en proyectos y landings.
 */
function theme_skema_acf_register_cpt_ficha_comun() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_ficha_proyectos',
			'title'                 => __( 'Ficha y carrusel (proyecto)', 'theme_skema' ),
			'fields'                => theme_skema_acf_ficha_comun_fields( 'proyectos' ),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'proyectos',
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_ficha_landings',
			'title'                 => __( 'Ficha y carrusel (landing)', 'theme_skema' ),
			'fields'                => theme_skema_acf_ficha_comun_fields( 'landings' ),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'landings',
					),
				),
			),
			'menu_order'            => 2,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'theme_skema_acf_register_cpt_ficha_comun' );
