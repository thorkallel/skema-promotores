<?php
/**
 * skema functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package skema
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

require_once get_template_directory() . '/inc/inicio-helpers.php';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function theme_skema_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on skema, use a find and replace
		* to change 'theme_skema' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'theme_skema', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-pincipal' => esc_html__( 'Primary', 'theme_skema' ),
			// 'menu-footer' => esc_html__( 'Footer', 'theme_skema' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'theme_skema_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	if( function_exists('acf_add_options_page') ) {
	
		acf_add_options_page(array(
			'page_title' 	=> 'Opciones generales',
			'menu_title'	=> 'Opciones generales',
			'menu_slug' 	=> 'opciones',
			'capability'	=> 'edit_posts',
			'redirect'		=> false
		));
	
	}
}
add_action( 'after_setup_theme', 'theme_skema_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function theme_skema_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'theme_skema_content_width', 640 );
}
add_action( 'after_setup_theme', 'theme_skema_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
//function theme_skema_widgets_init() {
//	register_sidebar(
//		array(
//			'name'          => esc_html__( 'Sidebar', 'theme_skema' ),
//			'id'            => 'sidebar-1',
//			'description'   => esc_html__( 'Add widgets here.', 'theme_skema' ),
//			'before_widget' => '<section id="%1$s" class="widget %2$s">',
//			'after_widget'  => '</section>',
//			'before_title'  => '<h2 class="widget-title">',
//			'after_title'   => '</h2>',
//		)
//	);
//}
//add_action( 'widgets_init', 'theme_skema_widgets_init' );


function wp_change_cat_checkboxes_to_radios(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($){
            // Lista de los IDs de los divs de las taxonomías que quieres modificar
            var taxonomies = ['tipo_proyectodiv', 'estado_proyectodiv', 'ciudad_proyectodiv']; // Asegúrate que estos son los IDs correctos

            // Cambiar cada checkbox de cada taxonomía a radio buttons
            taxonomies.forEach(function(taxonomy){
                $('#' + taxonomy).find('input[type=checkbox]').each(function(){
                    $(this).replaceWith($(this).clone(true).attr('type', 'radio'));
                });
            });
        });
    </script>
    <?php
}

add_action('admin_footer', 'wp_change_cat_checkboxes_to_radios');


/**
 * Enqueue scripts and styles.
 */
function theme_skema_scripts() {
	$timestamp = time();
	wp_enqueue_style( 'theme_skema-style', get_stylesheet_uri(), array(), $timestamp  );
	//wp_style_add_data( 'theme_skema-style', 'rtl', 'replace' );

	wp_enqueue_style( 'theme_skema-bootstrap-css', get_template_directory_uri() . '/css/bootstrap.min.css', array(), _S_VERSION);
	wp_enqueue_style( 'theme_skema-fonts1', 'https://fonts.googleapis.com/css2?family=Readex+Pro:wght@160..700&display=swap', array(), _S_VERSION);
	wp_enqueue_style( 'theme_skema-fonts2', 'https://fonts.googleapis.com/css2?family=Arvo:ital,wght@0,400;0,700;1,400;1,700&display=swap', array(), _S_VERSION);
	
	//wp_enqueue_style( 'theme_skema-owl-css-carousel', get_template_directory_uri(). '/css/owl.carousel.min.css', array(), _S_VERSION);
	//wp_enqueue_style( 'theme_skema-owl-css-theme', get_template_directory_uri(). '/css/owl.theme.default.min.css', array(), _S_VERSION);
	wp_enqueue_style( 'theme_skema-bootstrap-icons', get_template_directory_uri() . '/css/bootstrap-icons.css', array(), _S_VERSION);
	wp_enqueue_style( 'theme_skema-slickslider-css', get_template_directory_uri(). '/css/slick.css', array(), _S_VERSION );
	wp_enqueue_style( 'theme_skema-responsive-css', get_template_directory_uri(). '/css/responsive.css', array(), $timestamp  );
	wp_enqueue_style( 'theme_skema-slickslider-theme', get_template_directory_uri(). '/css/slick-theme.css', array(), _S_VERSION );
	//wp_enqueue_style( 'theme_skema-timeline-css', get_template_directory_uri(). '/css/horizontal_timeline.2.0.min.css', array(), _S_VERSION );
	
	wp_enqueue_script( 'theme_skema-bootstrap-bundle-js', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array('jquery'), _S_VERSION, true );
	wp_enqueue_script( 'theme_skema-slickslider-js ', get_template_directory_uri() . '/js/slick.min.js', array('jquery'), _S_VERSION, true );
	wp_enqueue_script( 'theme_skema-owl-js ', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), _S_VERSION, true );
	//wp_enqueue_script( 'theme_skema-timeline-js' , get_template_directory_uri() . '/js/horizontal_timeline.2.0.min.js', array('jquery'), _S_VERSION, true );
	wp_enqueue_script( 'theme_skema-main-js', get_template_directory_uri() . '/js/main.js', array('jquery'), $timestamp, true );

	wp_enqueue_script( 'theme_skema-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( theme_skema_is_theme_inicio_template_active() ) {
		$home_css_uri = get_template_directory_uri() . '/css/home';
		wp_enqueue_style(
			'theme_skema-home-hero',
			$home_css_uri . '/skema-home-hero.css',
			array( 'theme_skema-style', 'theme_skema-slickslider-theme' ),
			_S_VERSION
		);
		wp_enqueue_style(
			'theme_skema-home-proyectos',
			$home_css_uri . '/skema-home-proyectos.css',
			array( 'theme_skema-style', 'theme_skema-bootstrap-css', 'theme_skema-bootstrap-icons', 'theme_skema-home-hero' ),
			_S_VERSION
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'theme_skema_scripts' );


function register_navwalker(){
	require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';
}
add_action( 'after_setup_theme', 'register_navwalker' );

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Opciones generales',
		'menu_title'	=> 'Opciones generales',
		'menu_slug' 	=> 'opciones',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

}
function disable_plugin_updates( $value ) {
    unset( $value->response['advanced-custom-fields-pro-master/acf.php'] );
    return $value;
}
add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );

function custom_mimes( $mimes = array() ) {
	// New allowed mime types.
    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'custom_mimes' );
/**
 * CPT Landings.
 */
require get_template_directory() . '/inc/cpt-landings.php';

/**
 * Landings: variante de plantilla (ACF) + CSS por capas + partial entry-{variante}.php.
 */
require get_template_directory() . '/inc/landings-variant.php';

/**
 * ACF: ficha común (precio, tipología, superficie, imagen) en proyectos y landings.
 */
require get_template_directory() . '/inc/acf-cpt-ficha-comun.php';

/**
 * Inicio: campos ACF locales (helpers en inc/inicio-helpers.php, cargado al inicio del tema).
 */
require get_template_directory() . '/inc/acf-inicio-extendido.php';

/**
 * Landings: cabecera slider (ACF + render; usa helpers de inicio-helpers).
 */
require get_template_directory() . '/inc/acf-landings-slider.php';
require get_template_directory() . '/inc/landings-hero-slider.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * SEO (meta, Open Graph, JSON-LD) cuando no hay plugin SEO activo.
 */
require get_template_directory() . '/inc/seo.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


function mi_tema_scrollreveal_scripts() {
    wp_enqueue_script( 'scrollreveal', 'https://unpkg.com/scrollreveal@4.0.9/dist/scrollreveal.js', array(), null, true );
    wp_enqueue_script( 'scrollreveal-init', get_template_directory_uri() . '/js/scrollreveal-init.js', array('scrollreveal'), null, true );
}
//add_action( 'wp_enqueue_scripts', 'mi_tema_scrollreveal_scripts' );

function add_gsap() {
    // GSAP
    wp_enqueue_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/gsap.min.js', array(), '3.5.1', true);

    // ScrollMagic
    wp_enqueue_script('scrollmagic', 'https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/ScrollMagic.min.js', array(), '2.0.7', true);

    // Plugin de animación GSAP para ScrollMagic
    wp_enqueue_script('scrollmagic-gsap', 'https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.7/plugins/animation.gsap.min.js', array('gsap', 'scrollmagic'), '2.0.7', true);
}
// add_action('wp_enqueue_scripts', 'add_gsap');

function add_custom_js() {
    // Asegúrate de ajustar la ruta según donde coloques tu archivo JS personalizado
    wp_enqueue_script('custom-js', get_template_directory_uri() . '/js/custom-animations.js', array('gsap', 'scrollmagic'), '1.0.0', true);
}
// add_action('wp_enqueue_scripts', 'add_custom_js');


function mi_custom_login_logo() {
	$logo_url = esc_url( get_stylesheet_directory_uri() . '/img/logo-skema-color.png' ); 
    ?>
   <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo $logo_url; ?>);
          
            background-size: contain;
			width: 100%;
    height: 60px;
        }
		
    </style>
	<?php
}
add_action( 'login_enqueue_scripts', 'mi_custom_login_logo' );


function cambiar_url_logo_login() {
    return home_url(); // Esto hará que el logo enlace a la página de inicio de tu sitio
}
add_filter( 'login_headerurl', 'cambiar_url_logo_login' );

function cambiar_title_logo_login() {
    return get_bloginfo( 'name' ); // Cambia el atributo title al nombre de tu sitio
}
add_filter( 'login_headertext', 'cambiar_title_logo_login' );

// Agregar pie de página personalizado en la pantalla de inicio de sesión
function agregar_pie_de_pagina_login() {
    ?>
    <div class="container-fluid pb-sm-0 pb-0">
        <div class="row">
            <div class="col-12">
                <div class="copyrights">					
                    <a class="" href="https://himalayadigital.co/" target="_blank">
                        Diseñado y desarrollado por:
                        <img class="logo-himalaya" src="<?php echo esc_url(get_template_directory_uri() . '/img/logo-himalaya-azul.svg'); ?>" alt="logo-himalaya-sem" loading="lazy">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <style>
       .copyrights>a {
	font-size: 0.9em;
	color: #06379c;
	display: flex;
    width: 100%;
    align-items: center;
    justify-content: center;
	flex-direction: column;
	text-decoration: none;
  }
  .copyrights>a img{
	width: 150px;
  }
  .copyrights>a:hover{
	text-decoration: underline;
  }
 

  .copyrights {
	display: flex;
	justify-content: center;
  }
        
    </style>
    <?php
}
add_action('login_footer', 'agregar_pie_de_pagina_login');


// Crear un nuevo rol de usuario
function crear_rol_contenidos() {

	$admin_capabilities = get_role('administrator')->capabilities;
	if (!get_role('admin_contenidos')) {
        add_role(
            'admin_contenidos', // Slug del rol
            'AdminContenidos', // Nombre visible en el admin
            $admin_capabilities // Copia las capacidades del administrador
        );
    }
}
add_action('init', 'crear_rol_contenidos');


// Ocultar opciones del menú para AdminContenidos
function ocultar_opciones_admin_contenidos() {
    // Verificar si el usuario tiene el rol admin_contenidos
    if (current_user_can('admin_contenidos')) {
        // Ejemplos de elementos a ocultar
        remove_menu_page('cptui_manage');            // CPT UI   
        remove_menu_page('admin.php?page=cptui_manage_post_types');            // CPT UI   
		remove_submenu_page('cptui_manage', 'cptui_manage_post_types'); // Submenú: Manage Post Types
        remove_submenu_page('cptui_manage', 'cptui_manage_taxonomies'); // Submenú: Manage Taxonomies
        remove_menu_page('edit.php?post_type=acf-field-group');      // ACF           
        remove_menu_page('wpcf7');               // Contacto
        //remove_menu_page('upload.php');               // Medios
        remove_menu_page('edit-comments.php');        // Comentarios
        remove_menu_page('tools.php');                // Herramientas
        remove_menu_page('plugins.php');              // Plugins
        //remove_menu_page('themes.php');               // Apariencia
        remove_menu_page('users.php');                // Usuarios
        remove_menu_page('options-general.php');      // Ajustes
        // Ejemplo para ocultar submenús
        remove_submenu_page('themes.php', 'site-editor.php?postType=wp_block'); // Nuevo post en "Entradas"
        remove_submenu_page('themes.php', 'customize.php'); // Nuevo post en "Entradas"

        // Plugins específicos
        // remove_menu_page('admin.php?page=rank-math');   // Rank Math SEO
        // remove_menu_page('admin.php?page=opciones');   // Plugin "Opciones"
    }
}
add_action('admin_menu', 'ocultar_opciones_admin_contenidos', 999);



// Bloquear acceso directo a páginas específicas
function bloquear_acceso_para_admin_contenidos() {
    if (current_user_can('admin_contenidos')) {
        // Lista de slugs de páginas bloqueadas
        $paginas_bloqueadas = array(
            //'edit.php',                  // Entradas
            'admin.php?page=wpcf7',               // Contacto
            //'upload.php',               // Medios
            'edit-comments.php',        // Comentarios
            'tools.php',                // Herramientas
            'plugins.php',              // Plugins
            'themes.php',               // Apariencia
            'users.php',                // Usuarios
            'options-general.php',      // Ajustes
            'admin.php?page=cptui_manage_post_types', // CPT UI
			'edit.php?post_type=acf-field-group', // ACF
            //'admin.php?page=opciones',  // Opciones del plugin
        );

        // Obtener el slug de la página actual
        $actual_pagina = isset($_GET['page']) ? 'admin.php?page=' . $_GET['page'] : basename($_SERVER['PHP_SELF']);

        // Redirigir si está intentando acceder a una página bloqueada
        if (in_array($actual_pagina, $paginas_bloqueadas)) {
            wp_redirect(admin_url()); // Redirigir al Dashboard
            exit;
        }
    }
}
add_action('admin_init', 'bloquear_acceso_para_admin_contenidos');



// Agregar CSS para ocultar opciones específicas en el admin
function ocultar_opciones_para_admin_contenidos() {
    if (current_user_can('admin_contenidos')) { // Verificar el rol
        echo '<style>
            /* Oculta elementos específicos */
            #menu-appearance .wp-submenu li:nth-child(1), 
			#menu-appearance .wp-submenu li:nth-child(2), 
			#menu-appearance .wp-submenu li:nth-child(3), 
			#menu-appearance .wp-submenu li:nth-child(4), 
			#menu-appearance .wp-submenu li:nth-child(6), 
			#menu-appearance .wp-submenu li:nth-child(7), 
			#menu-appearance .wp-submenu li:nth-child(8), 
			#menu-appearance .wp-submenu li:nth-child(9), 
			#menu-appearance .wp-submenu li:nth-child(10),
			.nav-tab-wrapper.wp-clearfix a:nth-child(2),
			#menu-settings .wp-submenu li:nth-child(1), 
			#menu-settings .wp-submenu li:nth-child(2),
			#toplevel_page_cptui_main_menu,
			#menu-posts .wp-submenu li:nth-child(5),
			#menu-dashboard .wp-submenu li:nth-child(3),
			#dashboard_site_health, #dashboard_widget,
			#wp-admin-bar-new-content-default #wp-admin-bar-new-user,
			a.dashicons.dashicons-admin-generic.acf-hndle-cog.acf-js-tooltip,
			#tagsdiv-post_tag, #menu-posts
			{ display: none!important; }
        </style>';
    }
}
add_action('admin_head', 'ocultar_opciones_para_admin_contenidos');
