<?php
/**
 * Pie mínimo solo para singles del CPT «landings» (get_footer( 'landings' )).
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

$landing_id    = get_queried_object_id();
$landing_title = $landing_id ? get_the_title( $landing_id ) : '';
$home_url      = home_url( '/' );
$solicitar_href = $landing_id ? ( get_permalink( $landing_id ) . '#solicitar-informacion' ) : '#solicitar-informacion';
?>
<footer id="colophon" class="site-footer site-footer--landings main-footer footer-v1 py-3 py-md-4">
	<div class="container">
		<div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 text-center text-md-start">
			<p class="text-white mb-0 small skema-landing-footer__copy">
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: year, 2: site name, 3: landing title */
						__( '%1$s %2$s · %3$s', 'theme_skema' ),
						'© ' . gmdate( 'Y' ),
						get_bloginfo( 'name' ),
						$landing_title !== '' ? $landing_title : __( 'Ficha de proyecto', 'theme_skema' )
					)
				);
				?>
			</p>
			<div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-end column-gap-3 row-gap-1">
				<a class="text-white text-decoration-none small" href="<?php echo esc_url( $home_url ); ?>">
					<?php esc_html_e( 'Inicio', 'theme_skema' ); ?>
				</a>
				<a class="text-white text-decoration-none small" href="<?php echo esc_url( $solicitar_href ); ?>">
					<?php esc_html_e( 'Solicitar información', 'theme_skema' ); ?>
				</a>
			</div>
		</div>
	</div>
</footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
