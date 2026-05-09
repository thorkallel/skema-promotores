<?php
/**
 * Landings: ficha resumen (características) encima del contenido del editor.
 *
 * Referencia visual: Renvia .project-meta-list + .renvia-info-item.style-three.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL de imagen por defecto para ítems sin icono (mismo criterio que plantilla estática).
 *
 * @return string
 */
function theme_skema_landing_project_meta_default_icon_url() {
	static $url = null;

	if ( null !== $url ) {
		return $url;
	}

	$path = get_template_directory() . '/images/landings/project-meta-icon-default.svg';
	if ( is_readable( $path ) ) {
		$url = get_template_directory_uri() . '/images/landings/project-meta-icon-default.svg';
		return $url;
	}

	$url = '';
	return $url;
}

/**
 * Comprueba si la fila del repetidor tiene datos mínimos para mostrarse.
 *
 * @param array<string, mixed> $row Fila ACF.
 * @return bool
 */
function theme_skema_landing_project_meta_row_is_valid( $row ) {
	if ( ! is_array( $row ) ) {
		return false;
	}

	$label = isset( $row['label'] ) ? trim( (string) $row['label'] ) : '';
	$value = isset( $row['value'] ) ? trim( (string) $row['value'] ) : '';

	return $label !== '' && $value !== '';
}

/**
 * Resuelve URL de icono desde subcampo imagen ACF.
 *
 * @param mixed $icon Campo imagen (array|int|null).
 * @return string URL segura o cadena vacía.
 */
function theme_skema_landing_project_meta_icon_url_from_field( $icon ) {
	if ( is_array( $icon ) && ! empty( $icon['url'] ) ) {
		return esc_url( (string) $icon['url'] );
	}

	if ( is_numeric( $icon ) && (int) $icon > 0 ) {
		$src = wp_get_attachment_image_url( (int) $icon, 'thumbnail' );
		return $src ? esc_url( $src ) : '';
	}

	return '';
}

/**
 * Texto introductorio compartido entre prelanding y landing completa (un solo campo en CMS).
 * Si existía contenido solo en el campo antiguo de landing, se sigue mostrando.
 *
 * @param int $post_id ID de la entrada landings.
 * @return string HTML o texto guardado en bruto (según ACF); cadena vacía si no hay nada.
 */
function theme_skema_get_landing_shared_intro_raw( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$primary = get_field( 'skema_lpre_hero_intro', $post_id, false );
	$primary = is_string( $primary ) ? trim( $primary ) : '';
	if ( $primary !== '' ) {
		return $primary;
	}

	$legacy = get_post_meta( $post_id, 'skema_lland_project_meta_intro', true );
	$legacy = is_string( $legacy ) ? trim( $legacy ) : '';

	return $legacy;
}

/**
 * Imprime la ficha resumen si aplica (CPT landings, fase landing, ACF activado).
 *
 * @param int|null $post_id ID de entrada; por defecto el actual en singular.
 * @return void
 */
function theme_skema_render_landing_project_characteristics( $post_id = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	if ( ! is_singular( 'landings' ) ) {
		return;
	}

	if ( ! function_exists( 'theme_skema_get_landing_phase' ) || 'landing' !== theme_skema_get_landing_phase( $post_id ) ) {
		return;
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return;
	}

	$enabled = get_field( 'skema_lland_project_meta_enabled', $post_id );
	if ( false === $enabled || '0' === $enabled || 0 === $enabled ) {
		return;
	}

	$rows  = get_field( 'skema_lland_project_meta_items', $post_id );
	$intro = theme_skema_get_landing_shared_intro_raw( $post_id );

	$valid_rows = array();
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( theme_skema_landing_project_meta_row_is_valid( $row ) ) {
				$valid_rows[] = $row;
			}
		}
	}

	$intro_html = is_string( $intro ) ? trim( $intro ) : '';
	if ( $intro_html === '' && count( $valid_rows ) === 0 ) {
		return;
	}

	$default_icon = theme_skema_landing_project_meta_default_icon_url();
	$section_label = __( 'Características del proyecto', 'theme_skema' );

	echo '<section class="skema-landing-project-details-sec" aria-label="' . esc_attr( $section_label ) . '">';
	echo '<div class="skema-landing-project-details-inner project-details-wrapper">';
	echo '<div class="project-content">';

	if ( count( $valid_rows ) > 0 ) {
		echo '<div class="project-meta-list" role="list">';
		foreach ( $valid_rows as $row ) {
			$label = trim( (string) $row['label'] );
			$value = trim( (string) $row['value'] );
			$icon  = isset( $row['icon'] ) ? $row['icon'] : null;
			$src   = theme_skema_landing_project_meta_icon_url_from_field( $icon );
			if ( $src === '' && $default_icon !== '' ) {
				$src = $default_icon;
			}

			echo '<div class="renvia-info-item style-three skema-landing-meta-item mb-30" role="listitem">';
			echo '<div class="icon">';
			if ( $src !== '' ) {
				echo '<img src="' . esc_url( $src ) . '" alt="" width="32" height="32" loading="lazy" decoding="async">';
			}
			echo '</div>';
			echo '<div class="info">';
			echo '<span>' . esc_html( $label ) . '</span>';
			echo '<h6>' . esc_html( $value ) . '</h6>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}

	if ( $intro_html !== '' ) {
		echo '<div class="skema-landing-project-intro entry-content">';
		echo apply_filters( 'the_content', $intro_html );
		echo '</div>';
	}

	echo '</div></div></section>';
}
