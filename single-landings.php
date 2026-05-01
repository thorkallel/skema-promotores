<?php
/**
 * Plantilla para una landing de proyecto (contenido del editor + campos ACF de layout).
 *
 * Variante y fase: ACF «skema_landing_variant» + «skema_landing_phase» (ver inc/landings-variant.php).
 *
 * @package skema
 */

get_header();

$skema_landing_variant = function_exists( 'theme_skema_get_landing_variant' )
	? theme_skema_get_landing_variant()
	: 'default';
$skema_landing_phase   = function_exists( 'theme_skema_get_landing_phase' )
	? theme_skema_get_landing_phase()
	: 'prelanding';
?>
<main id="primary"
    class="site-main site-main--landings landing-skin landing-skin--<?php echo esc_attr( $skema_landing_variant ); ?> landing-phase--<?php echo esc_attr( $skema_landing_phase ); ?>">
    <?php
	while ( have_posts() ) :
		the_post();
		?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="entry-content">
            <?php
				if ( function_exists( 'theme_skema_landing_entry_template' ) ) {
					theme_skema_landing_entry_template();
				} else {
					the_content();
				}

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'theme_skema' ),
						'after'  => '</div>',
					)
				);
				?>
        </div>
    </article>
    <?php
	endwhile;
	?>
</main>
<?php
get_footer( 'landings' );