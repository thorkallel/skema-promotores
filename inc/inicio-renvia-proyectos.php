<?php
/**
 * Inicio: bloque «Proyectos» (carrusel Renvia — ACF `inicio_renvia_*`).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normaliza un ítem del campo relationship ACF a ID entero.
 *
 * @param mixed $rid Objeto WP_Post, array con ID o escalar.
 * @return int ID o 0.
 */
function theme_skema_inicio_renvia_normalize_relationship_id( $rid ) {
	if ( is_object( $rid ) && isset( $rid->ID ) ) {
		return (int) $rid->ID;
	}

	if ( is_array( $rid ) && isset( $rid['ID'] ) ) {
		return (int) $rid['ID'];
	}

	return absint( $rid );
}

/**
 * Filtra IDs del relationship del inicio a entradas publicadas válidas para el carrusel.
 *
 * @param array<int|string|mixed> $raw_ids Valor de `inicio_renvia_relacion`.
 * @return array<int>
 */
function theme_skema_inicio_renvia_collect_valid_post_ids( array $raw_ids ) {
	$validos = array();

	foreach ( $raw_ids as $rid ) {
		$post_id = theme_skema_inicio_renvia_normalize_relationship_id( $rid );
		if ( ! $post_id ) {
			continue;
		}

		if ( 'publish' !== get_post_status( $post_id ) ) {
			continue;
		}

		$post_type = get_post_type( $post_id );
		if ( ! in_array( $post_type, array( 'proyectos', 'landings' ), true ) ) {
			continue;
		}

		if ( ! theme_skema_inicio_renvia_incluir_en_slider( $post_id ) ) {
			continue;
		}

		$validos[] = $post_id;
	}

	return $validos;
}

/**
 * Título por defecto del bloque proyectos (HTML permitido en front).
 *
 * @return string
 */
function theme_skema_inicio_renvia_default_title_html() {
	return sprintf(
		/* translators: %s: «PROYECTOS» en <strong>, salto de línea y «en construcción y ventas» en <span>. */
		__( 'CONOCE NUESTROS %s', 'theme_skema' ),
		'<strong>' . esc_html__( 'PROYECTOS', 'theme_skema' ) . '</strong><br /> <span class="skema-proyectos__h2-subline">' . esc_html__( 'en construcción y ventas', 'theme_skema' ) . '</span>'
	);
}

/**
 * Imprime la sección del carrusel de proyectos/landings del inicio.
 *
 * Campos ACF: `inicio_renvia_relacion`, `inicio_renvia_titulo`, `inicio_renvia_subtitulo`.
 *
 * @return void
 */
function theme_skema_render_inicio_renvia_proyectos_section() {
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$raw_ids = get_field( 'inicio_renvia_relacion' );
	if ( ! is_array( $raw_ids ) ) {
		$raw_ids = array();
	}

	$post_ids = theme_skema_inicio_renvia_collect_valid_post_ids( $raw_ids );
	$n_items  = count( $post_ids );

	if ( $n_items <= 0 ) {
		return;
	}

	$tit_renvia = get_field( 'inicio_renvia_titulo' );
	if ( ! is_string( $tit_renvia ) || $tit_renvia === '' ) {
		$tit_renvia = theme_skema_inicio_renvia_default_title_html();
	}

	$sub_renvia      = get_field( 'inicio_renvia_subtitulo' );
	$tit_renvia_safe = wp_kses_post( $tit_renvia );

	?>
<section class="skema-proyectos" id="proyectos"
    aria-label="<?php echo esc_attr( wp_strip_all_tags( $tit_renvia ) ); ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-11 col-11 px-sm-0 px-3 py-sm-4 py-1 item text-right">
                <p class="text-right"><b>02</b><?php echo esc_html( __( 'PROYECTOS', 'theme_skema' ) ); ?></p>
            </div>
            <div class="col-sm-1 px-0"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-sm-1 px-0"></div>
            <div class="col-sm-10 col-10 px-sm-0 px-3 py-sm-4 py-1 item">
                <div class="skema-proyectos__head">
                    <?php if ( is_string( $sub_renvia ) && $sub_renvia !== '' ) : ?>
                    <span class="skema-proyectos__subtitle"><?php echo esc_html( $sub_renvia ); ?></span>
                    <?php endif; ?>
                    <div class="skema-proyectos__head-main">
                        <h2><?php echo $tit_renvia_safe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </h2>
                        <a href="<?php echo esc_url( home_url( '/proyectos/' ) ); ?>"
                            class="btn-nosotros skema-proyectos__btn-nosotros"><?php esc_html_e( 'CONOCE NUESTRA TRAYECTORIA', 'theme_skema' ); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 px-0"></div>
        </div>
    </div>
    <div class="container-fluid skema-proyectos__fluid">
        <div class="skema-proyectos__slider" data-slides-count="<?php echo esc_attr( (string) $n_items ); ?>">
            <?php
				foreach ( $post_ids as $pid_renv ) {
					theme_skema_render_inicio_renvia_ficha_card( $pid_renv );
				}
				?>
        </div>
    </div>
</section>
<?php
}