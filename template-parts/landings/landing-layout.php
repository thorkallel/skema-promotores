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
if ( function_exists( 'theme_skema_render_landing_project_logo' ) && 'prelanding' !== $skema_landing_phase_layout ) {
	theme_skema_render_landing_project_logo();
}
?>
<div class="landing-main landing-main--solo-contenido">
	<?php the_content(); ?>
</div>
