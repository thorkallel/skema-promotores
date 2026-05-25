<?php
/**
 * ARCHIVO DE REFERENCIA — no se incluye en el tema.
 *
 * Fragmentos de header.php usados cuando is_singular( 'landings' ) era true
 * (cabecera tipo Renvia «header-one»). Archivado al unificar con el header global.
 *
 * @package skema
 */

defined( 'ABSPATH' ) || exit;

// Variables que usaba header.php:
// $skema_is_landing_nav = is_singular( 'landings' );
// $skema_logo_marca     = get_field( 'logo_marca', 'option' );

/*
 * Clases en <header> y <nav>:
 *   site-header header-area header-one skema-landing-header-area
 *   navbar-expand-xl skema-nav-renvia-landings
 *
 * Markup exclusivo landings (sustituido por el flujo estándar d-sm-none / navbar-expand-lg):
 */

?>
<!-- INICIO markup archivado (referencia) -->
<header id="masthead" class="site-header header-area header-one skema-landing-header-area">
	<nav class="navbar fixed-top navbar-expand-xl navbar-light slide-in-top skema-nav-renvia-landings" role="navigation" id="principal-menu">

		<div class="logo-skema d-flex d-xl-none justify-content-center w-100 skema-landing-nav-logo--bar">…</div>

		<div class="container-fluid skema-renvia-header-fluid">
			<div class="header-navigation">
				<div class="primary-menu">
					<div class="site-branding d-none d-xl-flex align-items-center">…logo…</div>

					<div class="navbar-collapse justify-content-sm-between theme-nav-menu" id="menuPrincipal">
						<button class="btn-cerrar d-xl-none">…</button>
						<div class="logo-skema position-absolute skema-landing-drawer-logo d-xl-none">…</div>
						<?php /* wp_nav_menu */ ?>
						<div class="redes skema-landing-redes--drawer d-xl-none">…</div>
					</div>

					<div class="nav-right-item d-flex align-items-center flex-shrink-0">
						<div class="redes header-nav-social d-none d-md-flex align-items-center">…</div>
						<button class="btn-abrir btn-abrir--renvia d-xl-none">…</button>
					</div>
				</div>
			</div>
		</div>
	</nav>
</header>
<!-- FIN markup archivado -->
