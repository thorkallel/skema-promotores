<?php
/**
 * WhatsApp: número opcional por página, proyecto o landing (fallback a Opciones generales).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nombre del campo ACF en el contenido (post meta).
 */
function theme_skema_whatsapp_contextual_field_name() {
	return 'skema_whatsapp_numero';
}

/**
 * Deja solo dígitos para usar en https://wa.me/{digits}.
 *
 * @param mixed $raw Valor crudo (texto, número, null).
 * @return string Cadena numérica o vacío.
 */
function theme_skema_sanitize_whatsapp_wa_me_digits( $raw ) {
	if ( is_int( $raw ) || is_float( $raw ) ) {
		$raw = (string) $raw;
	}

	if ( ! is_string( $raw ) ) {
		return '';
	}

	$digits = preg_replace( '/\D+/', '', $raw );

	return is_string( $digits ) ? $digits : '';
}

/**
 * ID de la entrada actual si es página, proyecto o landing (singular).
 *
 * @return int 0 si no aplica.
 */
function theme_skema_resolve_whatsapp_context_post_id() {
	if ( ! is_singular() ) {
		return 0;
	}

	$post = get_queried_object();

	if ( ! $post instanceof WP_Post ) {
		return 0;
	}

	$allowed_types = array( 'page', 'proyectos', 'landings' );

	if ( ! in_array( $post->post_type, $allowed_types, true ) ) {
		return 0;
	}

	return (int) $post->ID;
}

/**
 * Dígitos del número de WhatsApp para wa.me: primero el campo del contenido, si no el global (Opciones).
 *
 * @param int|null $post_id ID explícito o null para deducir en singular.
 * @return string Vacío si no hay número usable.
 */
function theme_skema_get_whatsapp_wa_me_digits( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$resolved_id = $post_id;

	if ( null === $resolved_id ) {
		$resolved_id = theme_skema_resolve_whatsapp_context_post_id();
	}

	$from_post = '';

	if ( is_int( $resolved_id ) && $resolved_id > 0 ) {
		$raw_post = get_field( theme_skema_whatsapp_contextual_field_name(), $resolved_id );
		$from_post = theme_skema_sanitize_whatsapp_wa_me_digits( $raw_post );
	}

	if ( '' !== $from_post ) {
		return $from_post;
	}

	return theme_skema_sanitize_whatsapp_wa_me_digits( get_field( 'wp', 'option' ) );
}

/**
 * Registra el grupo ACF en páginas, proyectos y landings.
 */
function theme_skema_acf_register_whatsapp_contextual() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$field_name = theme_skema_whatsapp_contextual_field_name();

	acf_add_local_field_group(
		array(
			'key'                   => 'group_skema_whatsapp_contextual',
			'title'                 => __( 'WhatsApp (esta página o ficha)', 'theme_skema' ),
			'fields'                => array(
				array(
					'key'          => 'field_skema_whatsapp_contextual_numero',
					'label'        => __( 'Número de WhatsApp', 'theme_skema' ),
					'name'         => $field_name,
					'type'         => 'text',
					'instructions' => __( 'Opcional. Si lo rellenas, los enlaces de WhatsApp de esta vista usarán este número en lugar del de «Opciones generales». Solo dígitos, con código de país (ej.: 573001234567).', 'theme_skema' ),
					'required'     => 0,
					'placeholder'  => __( 'Ej.: 573001234567', 'theme_skema' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'proyectos',
					),
				),
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'landings',
					),
				),
			),
			'menu_order'            => 15,
			'position'              => 'side',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/init', 'theme_skema_acf_register_whatsapp_contextual' );
