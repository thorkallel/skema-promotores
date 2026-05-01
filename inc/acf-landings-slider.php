<?php
/**
 * Landings: cabecera (slider imágenes / YouTube + logo de proyecto) por fase editorial.
 * Campos en pestañas ACF: Prelanding y Landing completa.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registra grupo ACF en CPT landings.
 */
function theme_skema_acf_register_landing_hero_slider() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_landing_hero_slider',
			'title'                 => __( 'Cabecera (slider, copy prelanding y lead)', 'theme_skema' ),
			'fields'                => array(
				array(
					'key'   => 'field_skema_landing_hero_tab_pre',
					'label' => __( 'Prelanding', 'theme_skema' ),
					'name'  => '',
					'type'  => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => 'field_skema_lpre_hero_tipo',
					'label'         => __( 'Tipo de cabecera', 'theme_skema' ),
					'name'          => 'skema_lpre_hero_tipo',
					'type'          => 'select',
					'instructions'  => __( 'Misma lógica que el inicio: slider Slick con imágenes (escritorio + móvil opcional) o vídeos YouTube.', 'theme_skema' ),
					'choices'       => array(
						'none'    => __( 'Ninguna (solo contenido inferior)', 'theme_skema' ),
						'images'  => __( 'Slider de imágenes', 'theme_skema' ),
						'youtube' => __( 'Slider YouTube', 'theme_skema' ),
					),
					'default_value' => 'none',
					'allow_null'    => 0,
					'ui'            => 1,
					'return_format' => 'value',
				),
				array(
					'key'               => 'field_skema_lpre_slider_img',
					'label'             => __( 'Slides escritorio', 'theme_skema' ),
					'name'              => 'skema_lpre_slider_img',
					'type'              => 'repeater',
					'instructions'      => __( 'Imágenes a ancho completo (≥576px).', 'theme_skema' ),
					'min'               => 0,
					'max'               => 0,
					'layout'            => 'row',
					'button_label'      => __( 'Añadir slide', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'           => 'field_skema_lpre_slider_img_cell',
							'label'         => __( 'Imagen', 'theme_skema' ),
							'name'          => 'img',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'required'      => 1,
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lpre_hero_tipo',
								'operator' => '==',
								'value'    => 'images',
							),
						),
					),
				),
				array(
					'key'               => 'field_skema_lpre_slider_img_movil',
					'label'             => __( 'Slides móvil', 'theme_skema' ),
					'name'              => 'skema_lpre_slider_img_movil',
					'type'              => 'repeater',
					'instructions'      => __( 'Opcional: si queda vacío, en móvil se usan las mismas imágenes que en escritorio.', 'theme_skema' ),
					'layout'            => 'row',
					'button_label'      => __( 'Añadir slide móvil', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'           => 'field_skema_lpre_slider_img_movil_cell',
							'label'         => __( 'Imagen', 'theme_skema' ),
							'name'          => 'img',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'required'      => 1,
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lpre_hero_tipo',
								'operator' => '==',
								'value'    => 'images',
							),
						),
					),
				),
				array(
					'key'               => 'field_skema_lpre_slider_youtube',
					'label'             => __( 'Vídeos YouTube', 'theme_skema' ),
					'name'              => 'skema_lpre_slider_youtube',
					'type'              => 'repeater',
					'layout'            => 'row',
					'button_label'      => __( 'Añadir vídeo', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'   => 'field_skema_lpre_slider_youtube_url',
							'label' => __( 'URL del vídeo', 'theme_skema' ),
							'name'  => 'youtube_url',
							'type'  => 'url',
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lpre_hero_tipo',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				),
				array(
					'key'           => 'field_skema_lpre_project_logo',
					'label'         => __( 'Logo del proyecto', 'theme_skema' ),
					'name'          => 'skema_lpre_project_logo',
					'type'          => 'image',
					'instructions'  => __( 'Opcional. En Prelanding se muestra dentro del hero junto al título.', 'theme_skema' ),
					'return_format' => 'array',
					'preview_size'  => 'medium',
				),
				array(
					'key'          => 'field_skema_lpre_hero_tagline',
					'label'        => __( 'Línea superior (etiqueta)', 'theme_skema' ),
					'name'         => 'skema_lpre_hero_tagline',
					'type'         => 'text',
					'instructions' => __( 'Ej.: Prelanzamiento 2026 · Proyecto VIS', 'theme_skema' ),
				),
				array(
					'key'          => 'field_skema_lpre_hero_heading',
					'label'        => __( 'Título principal (H1)', 'theme_skema' ),
					'name'         => 'skema_lpre_hero_heading',
					'type'         => 'text',
					'instructions' => __( 'Si queda vacío, se usa el título de la entrada.', 'theme_skema' ),
				),
				array(
					'key'   => 'field_skema_lpre_hero_subtitle',
					'label' => __( 'Subtítulo', 'theme_skema' ),
					'name'  => 'skema_lpre_hero_subtitle',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_skema_lpre_hero_location',
					'label'        => __( 'Ubicación (línea corta)', 'theme_skema' ),
					'name'         => 'skema_lpre_hero_location',
					'type'         => 'text',
					'instructions' => __( 'Ej.: Valle del Cauca · Pacífico', 'theme_skema' ),
				),
				array(
					'key'   => 'field_skema_lpre_hero_intro',
					'label' => __( 'Párrafo introductorio', 'theme_skema' ),
					'name'  => 'skema_lpre_hero_intro',
					'type'  => 'textarea',
					'rows'  => 4,
				),
				array(
					'key'          => 'field_skema_lpre_hero_points',
					'label'        => __( 'Viñetas destacadas', 'theme_skema' ),
					'name'         => 'skema_lpre_hero_points',
					'type'         => 'repeater',
					'layout'       => 'row',
					'button_label' => __( 'Añadir viñeta', 'theme_skema' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_skema_lpre_hero_points_text',
							'label' => __( 'Texto', 'theme_skema' ),
							'name'  => 'point_text',
							'type'  => 'text',
						),
					),
				),
				array(
					'key'          => 'field_skema_lpre_lead_title',
					'label'        => __( 'Título del bloque de contacto', 'theme_skema' ),
					'name'         => 'skema_lpre_lead_title',
					'type'         => 'text',
					'instructions' => __( 'Si queda vacío y hay formulario, se usa «Solicita información».', 'theme_skema' ),
				),
				array(
					'key'   => 'field_skema_lpre_lead_text',
					'label' => __( 'Texto bajo el título del bloque', 'theme_skema' ),
					'name'  => 'skema_lpre_lead_text',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'          => 'field_skema_lpre_lead_cf7',
					'label'        => __( 'Shortcode Contact Form 7', 'theme_skema' ),
					'name'         => 'skema_lpre_lead_cf7',
					'type'         => 'textarea',
					'rows'         => 2,
					'instructions' => __( 'Pega el shortcode del formulario (Contacto → formularios → copiar shortcode). Solo se aceptan shortcodes que empiecen por [contact-form-7 …].', 'theme_skema' ),
				),
				array(
					'key'          => 'field_skema_lpre_lead_anchor_id',
					'label'        => __( 'ID HTML del bloque (ancla)', 'theme_skema' ),
					'name'         => 'skema_lpre_lead_anchor_id',
					'type'         => 'text',
					'instructions' => __( 'Opcional. Por defecto: solicitar-informacion. Solo letras, números y guiones.', 'theme_skema' ),
					'default_value' => 'solicitar-informacion',
				),
				array(
					'key'   => 'field_skema_landing_hero_tab_land',
					'label' => __( 'Landing completa', 'theme_skema' ),
					'name'  => '',
					'type'  => 'tab',
					'placement' => 'top',
				),
				array(
					'key'           => 'field_skema_lland_hero_tipo',
					'label'         => __( 'Tipo de cabecera', 'theme_skema' ),
					'name'          => 'skema_lland_hero_tipo',
					'type'          => 'select',
					'instructions'  => __( 'Configuración independiente de la pestaña Prelanding. Se usa cuando la fase de la entrada es «Landing completa».', 'theme_skema' ),
					'choices'       => array(
						'none'    => __( 'Ninguna (solo contenido inferior)', 'theme_skema' ),
						'images'  => __( 'Slider de imágenes', 'theme_skema' ),
						'youtube' => __( 'Slider YouTube', 'theme_skema' ),
					),
					'default_value' => 'none',
					'allow_null'    => 0,
					'ui'            => 1,
					'return_format' => 'value',
				),
				array(
					'key'               => 'field_skema_lland_slider_img',
					'label'             => __( 'Slides escritorio', 'theme_skema' ),
					'name'              => 'skema_lland_slider_img',
					'type'              => 'repeater',
					'layout'            => 'row',
					'button_label'      => __( 'Añadir slide', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'           => 'field_skema_lland_slider_img_cell',
							'label'         => __( 'Imagen', 'theme_skema' ),
							'name'          => 'img',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'required'      => 1,
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lland_hero_tipo',
								'operator' => '==',
								'value'    => 'images',
							),
						),
					),
				),
				array(
					'key'               => 'field_skema_lland_slider_img_movil',
					'label'             => __( 'Slides móvil', 'theme_skema' ),
					'name'              => 'skema_lland_slider_img_movil',
					'type'              => 'repeater',
					'instructions'      => __( 'Opcional: si queda vacío, en móvil se usan las mismas imágenes que en escritorio.', 'theme_skema' ),
					'layout'            => 'row',
					'button_label'      => __( 'Añadir slide móvil', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'           => 'field_skema_lland_slider_img_movil_cell',
							'label'         => __( 'Imagen', 'theme_skema' ),
							'name'          => 'img',
							'type'          => 'image',
							'return_format' => 'array',
							'preview_size'  => 'medium',
							'required'      => 1,
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lland_hero_tipo',
								'operator' => '==',
								'value'    => 'images',
							),
						),
					),
				),
				array(
					'key'               => 'field_skema_lland_slider_youtube',
					'label'             => __( 'Vídeos YouTube', 'theme_skema' ),
					'name'              => 'skema_lland_slider_youtube',
					'type'              => 'repeater',
					'layout'            => 'row',
					'button_label'      => __( 'Añadir vídeo', 'theme_skema' ),
					'sub_fields'        => array(
						array(
							'key'   => 'field_skema_lland_slider_youtube_url',
							'label' => __( 'URL del vídeo', 'theme_skema' ),
							'name'  => 'youtube_url',
							'type'  => 'url',
						),
					),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_skema_lland_hero_tipo',
								'operator' => '==',
								'value'    => 'youtube',
							),
						),
					),
				),
				array(
					'key'           => 'field_skema_lland_project_logo',
					'label'         => __( 'Logo del proyecto', 'theme_skema' ),
					'name'          => 'skema_lland_project_logo',
					'type'          => 'image',
					'instructions'  => __( 'Opcional. Se muestra bajo la cabecera en la fase Landing completa.', 'theme_skema' ),
					'return_format' => 'array',
					'preview_size'  => 'medium',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'landings',
					),
				),
			),
			'menu_order'            => 1,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'theme_skema_acf_register_landing_hero_slider' );
