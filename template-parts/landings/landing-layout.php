<?php
/**
 * Layout landing: contenido del editor a ancho completo.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

?>
<?php
if ( function_exists( 'theme_skema_render_landing_hero' ) ) {
	theme_skema_render_landing_hero();
}
$skema_landing_phase_layout = function_exists( 'theme_skema_get_landing_phase' )
	? theme_skema_get_landing_phase()
	: 'prelanding';
if (
	function_exists( 'theme_skema_render_landing_project_logo' )
	&& function_exists( 'theme_skema_should_render_landing_standalone_project_logo' )
	&& theme_skema_should_render_landing_standalone_project_logo()
) {
	theme_skema_render_landing_project_logo();
}
if ( function_exists( 'theme_skema_render_landing_project_characteristics' ) ) {
	theme_skema_render_landing_project_characteristics();
}
?>
<div class="landing-main landing-main--solo-contenido">
	<?php the_content(); ?>
</div>
<?php if ( 'landing' === $skema_landing_phase_layout ) : ?>
	<?php get_template_part( 'template-parts/landings/landing', 'content-box' ); ?>
<?php endif; ?>