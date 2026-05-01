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
if ( function_exists( 'theme_skema_render_landing_project_logo' ) ) {
	theme_skema_render_landing_project_logo();
}
?>
<div class="landing-main landing-main--solo-contenido">
	<?php the_content(); ?>
</div>
