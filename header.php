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

$skema_is_landing_nav = is_singular( 'landings' );
$skema_logo_marca     = function_exists( 'get_field' ) ? get_field( 'logo_marca', 'option' ) : '';

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
<div id="page" class="site">
<header id="masthead" class="site-header<?php echo $skema_is_landing_nav ? ' header-area header-one skema-landing-header-area' : ''; ?>">
	<nav class="navbar fixed-top <?php echo $skema_is_landing_nav ? 'navbar-expand-xl' : 'navbar-expand-lg'; ?> navbar-light slide-in-top<?php echo $skema_is_landing_nav ? ' skema-nav-renvia-landings' : ''; ?>" role="navigation" id="principal-menu">
		<?php if ( ! $skema_is_landing_nav ) : ?>
		<button class="btn-abrir d-sm-none" type="button">
			<?php echo file_get_contents( get_template_directory() . '/img/btn-abrir.svg' ); ?>
		</button>
		<?php endif; ?>

		<?php if ( $skema_logo_marca && ! $skema_is_landing_nav ) : ?>
		<div class="logo-skema d-flex d-sm-none justify-content-center w-100">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
				<img width="472" height="91" src="<?php echo esc_url( $skema_logo_marca ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url( $skema_logo_marca ); ?> 472w,<?php echo esc_url( $skema_logo_marca ); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
			</a>
		</div>
		<?php endif; ?>

		<?php if ( $skema_logo_marca && $skema_is_landing_nav ) : ?>
		<div class="logo-skema d-flex d-xl-none justify-content-center w-100 skema-landing-nav-logo--bar">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home" aria-current="page">
				<img width="472" height="91" src="<?php echo esc_url( $skema_logo_marca ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url( $skema_logo_marca ); ?> 472w,<?php echo esc_url( $skema_logo_marca ); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
			</a>
		</div>
		<?php endif; ?>

		<?php if ( $skema_is_landing_nav ) { ?>
		<div class="container-fluid skema-renvia-header-fluid">
			<div class="header-navigation">
				<div class="primary-menu">
					<?php if ( $skema_logo_marca ) { ?>
					<div class="site-branding d-none d-xl-flex align-items-center">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link brand-logo" rel="home" aria-current="page">
							<img width="472" height="91" src="<?php echo esc_url( $skema_logo_marca ); ?>" class="custom-logo" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" decoding="async" srcset="<?php echo esc_url( $skema_logo_marca ); ?> 472w,<?php echo esc_url( $skema_logo_marca ); ?> 300w" sizes="(max-width: 472px) 100vw, 472px">
						</a>
					</div>
					<?php } ?>
		<?php } ?>

		<div class="navbar-collapse justify-content-sm-between<?php echo $skema_is_landing_nav ? ' theme-nav-menu' : ''; ?>" id="menuPrincipal">
			<button class="btn-cerrar<?php echo $skema_is_landing_nav ? ' d-xl-none' : ' d-sm-none'; ?>" type="button">
				<i class="bi bi-x"></i>
			</button>

			<?php if ( $skema_logo_marca ) : ?>
			<div class="logo-skema position-absolute<?php echo $skema_is_landing_nav ? ' skema-landing-drawer-logo d-xl-none' : ''; ?>">
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

			<?php if ( $skema_is_landing_nav ) : ?>
			<div class="redes skema-landing-redes--drawer d-xl-none">
				<?php if ( get_field( 'face', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'face', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<i class="bi bi-facebook" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
				<?php if ( get_field( 'inst', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'inst', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<i class="bi bi-instagram" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
			</div>
			<?php else : ?>
			<div class="redes">
				<?php if ( get_field( 'face', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'face', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<i class="bi bi-facebook" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
				<?php if ( get_field( 'inst', 'option' ) ) : ?>
				<a href="<?php echo esc_url( get_field( 'inst', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<i class="bi bi-instagram" aria-hidden="true"></i>
				</a>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>

		<?php if ( $skema_is_landing_nav ) { ?>
					<div class="nav-right-item d-flex align-items-center flex-shrink-0">
						<div class="redes header-nav-social d-none d-md-flex align-items-center">
							<?php if ( get_field( 'face', 'option' ) ) : ?>
							<a href="<?php echo esc_url( get_field( 'face', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
								<i class="bi bi-facebook" aria-hidden="true"></i>
							</a>
							<?php endif; ?>
							<?php if ( get_field( 'inst', 'option' ) ) : ?>
							<a href="<?php echo esc_url( get_field( 'inst', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
								<i class="bi bi-instagram" aria-hidden="true"></i>
							</a>
							<?php endif; ?>
						</div>
						<button class="btn-abrir btn-abrir--renvia d-xl-none" type="button" aria-controls="menuPrincipal" aria-expanded="false">
							<?php echo file_get_contents( get_template_directory() . '/img/btn-abrir.svg' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</nav>
</header>
<div class="bnts_wa_tel">
	<?php if ( get_field( 'wp', 'option' ) ) : ?>
		<div class="btn_wa">
			<a href="https://wa.me/<?php echo esc_attr( get_field( 'wp', 'option' ) ); ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp"></i></a>
		</div>
	<?php endif; ?>
	<?php if ( get_field( 'tele', 'option' ) ) : ?>
		<div class="btn_tel d-flex d-sm-none">
			<a href="tel:<?php echo esc_attr( get_field( 'tele', 'option' ) ); ?>"><i class="bi bi-phone"></i></a>
		</div>
	<?php endif; ?>
</div>
