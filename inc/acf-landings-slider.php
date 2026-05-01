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
			'title'                 => __( 'Cabecera (slider y logo)', 'theme_skema' ),
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
					'instructions'  => __( 'Opcional. Se muestra bajo la cabecera en la fase Prelanding.', 'theme_skema' ),
					'return_format' => 'array',
					'preview_size'  => 'medium',
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
