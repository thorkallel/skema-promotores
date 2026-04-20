<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package skema
 */

get_header();
?>

	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>404</h1>
			</div>
			<h2>¡Ups, la página que estás buscando no se encuentra!</h2>
			<br>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="arrow"></span>Regresar a la página de inicio</a>
		</div>
	</div>

<?php
get_footer();
