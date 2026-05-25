<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package skema
 */

$skema_logo_marca = function_exists( 'get_field' ) ? get_field( 'logo_marca', 'option' ) : '';

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Saltar al contenido principal', 'theme_skema' ); ?></a>
<div id="page" class="site">
<header id="masthead" class="site-header">
	<nav class="navbar fixed-top navbar-expand-lg navbar-light slide-in-top" role="navigation" id="principal-menu">
		<button class="btn-abrir d-sm-none" type="button" aria-controls="menuPrincipal" aria-expanded="false"
			aria-label="<?php echo esc_attr__( 'Abrir menú principal', 'theme_skema' ); ?>">
			<?php echo file_get_contents( get_template_directory() . '/img/btn-abrir.svg' ); ?>
		</button>

		<?php if ( $skema_logo_marca ) : ?>
		<div class="logo-skema d-flex d-sm-none justify-content-center w-100">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
				<img width="472" height="91" src="<?php echo esc_url( $skema_logo_marca ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url( $skema_logo_marca ); ?> 472w,<?php echo esc_url( $skema_logo_marca ); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
			</a>
		</div>
		<?php endif; ?>

		<div class="navbar-collapse justify-content-sm-between" id="menuPrincipal">
			<button class="btn-cerrar d-sm-none" type="button"
				aria-controls="menuPrincipal" aria-expanded="true"
				aria-label="<?php echo esc_attr__( 'Cerrar menú principal', 'theme_skema' ); ?>">
				<i class="bi bi-x"></i>
			</button>

			<?php if ( $skema_logo_marca ) : ?>
			<div class="logo-skema position-absolute">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
					<img width="472" height="91" src="<?php echo esc_url( $skema_logo_marca ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url( $skema_logo_marca ); ?> 472w,<?php echo esc_url( $skema_logo_marca ); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
				</a>
			</div>
			<?php endif; ?>

			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-pincipal',
					'depth'          => 0,
					'container'      => false,
					'menu_class'     => 'navbar-nav align-items-sm-center',
					'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
					'walker'         => new WP_Bootstrap_Navwalker(),
				)
			);
			?>

			<div class="redes">
				<?php if ( get_field( 'face', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'face', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer"
					aria-label="<?php echo esc_attr__( 'Facebook', 'theme_skema' ); ?>">
					<i class="bi bi-facebook" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
				<?php if ( get_field( 'inst', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'inst', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer"
					aria-label="<?php echo esc_attr__( 'Instagram', 'theme_skema' ); ?>">
					<i class="bi bi-instagram" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>
</header>
<div class="bnts_wa_tel">
	<?php $skema_wa_digits = theme_skema_get_whatsapp_wa_me_digits(); ?>
	<?php if ( '' !== $skema_wa_digits ) : ?>
		<div class="btn_wa">
			<a href="https://wa.me/<?php echo esc_attr( $skema_wa_digits ); ?>" target="_blank" rel="noopener noreferrer"
				aria-label="<?php echo esc_attr__( 'Contactar por WhatsApp', 'theme_skema' ); ?>"><i class="bi bi-whatsapp"></i></a>
		</div>
	<?php endif; ?>
	<?php if ( get_field( 'tele', 'option' ) ) : ?>
		<div class="btn_tel d-flex d-sm-none">
			<a href="tel:<?php echo esc_attr( get_field( 'tele', 'option' ) ); ?>"
				aria-label="<?php echo esc_attr__( 'Llamar por teléfono', 'theme_skema' ); ?>"><i class="bi bi-phone"></i></a>
		</div>
	<?php endif; ?>
</div>
