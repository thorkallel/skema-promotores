<?php
/**
 * Template Name: Theme Inicio
 **/
get_header(); ?>
<main id="primary" class="site-main site-main--inicio">
    <?php
$home_h1 = trim( wp_strip_all_tags( (string) get_the_title() ) );
if ( '' === $home_h1 ) {
	$home_h1 = get_bloginfo( 'name' );
}
?>
    <h1 class="screen-reader-text"><?php echo esc_html( $home_h1 ); ?></h1>
    <?php
$tipo_banner = get_field('tipo_banner');

if ($tipo_banner === 'Video') : ?>
    <div class="video-content">
        <div class="video-background">
            <div class="video-foreground reveal">
                <video id="video-banner" class="d-none d-sm-block" src="<?php the_field('video-banner') ?>" autoplay
                    playsinline loop muted>
                    Your browser does not support HTML5 video.
                </video>
                <video id="video-banner-movil" class="d-sm-none d-block" src="<?php the_field('video-banner-movil') ?>"
                    autoplay playsinline loop muted>
                    Your browser does not support HTML5 video.
                </video>
            </div>
        </div>
    </div>
    <?php elseif ($tipo_banner === 'Slider') : ?>
    <div class="slider-banner d-none d-sm-block">
        <div class="slick-slider-banner">
            <?php
			$slide_i = 1;
			if ( have_rows( 'slider_img' ) ) :
				while ( have_rows( 'slider_img' ) ) :
					the_row();
					$img = get_sub_field( 'img' );
					if ( $img ) :
						$slide_alt = sprintf(
							/* translators: 1: slide number, 2: site name */
							__( 'Cabecera %1$d — %2$s', 'theme_skema' ),
							$slide_i,
							get_bloginfo( 'name' )
						);
						?>
            <div>
                <img src="<?= esc_url($img); ?>" class="img-fluid w-100" alt="<?php echo esc_attr( $slide_alt ); ?>">
            </div>
            <?php
						$slide_i++;
					endif;
				endwhile;
			endif; ?>
        </div>
        <div class="home-hero-dots" aria-hidden="true"></div>
    </div>
    <div class="slider-banner d-sm-none d-block">
        <div class="slick-slider-banner">
            <?php
			$slide_m = 1;
			if ( have_rows( 'slider_img_movil' ) ) :
				while ( have_rows( 'slider_img_movil' ) ) :
					the_row();
					$img = get_sub_field( 'img' );
					if ( $img ) :
						$slide_alt_m = sprintf(
							/* translators: 1: slide number, 2: site name */
							__( 'Cabecera móvil %1$d — %2$s', 'theme_skema' ),
							$slide_m,
							get_bloginfo( 'name' )
						);
						?>
            <div>
                <img src="<?= esc_url($img); ?>" class="img-fluid w-100" alt="<?php echo esc_attr( $slide_alt_m ); ?>">
            </div>
            <?php
						$slide_m++;
					endif;
				endwhile;
			endif; ?>
        </div>
    </div>
    <?php elseif ( 'slider_youtube' === $tipo_banner ) : ?>
    <?php
	$slides_yt = array();
	if ( have_rows( 'slider_youtube' ) ) {
		while ( have_rows( 'slider_youtube' ) ) {
			the_row();
			$yt_url = get_sub_field( 'youtube_url' );
			$yt_id = theme_skema_youtube_id_from_url( $yt_url );
			if ( $yt_id ) {
				$slides_yt[] = array( 'id' => $yt_id );
			}
		}
	}
	if ( ! empty( $slides_yt ) ) :
		?>
    <div class="slider-banner slider-banner--youtube">
        <div class="slick-slider-banner slick-slider-banner--youtube">
            <?php
			foreach ( $slides_yt as $idx => $slide_yt ) {
				$autoplay = 0 === $idx ? '1' : '0';
				$embed_src = sprintf(
					'https://www.youtube-nocookie.com/embed/%s?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&autoplay=%s',
					rawurlencode( $slide_yt['id'] ),
					$autoplay
				);
				$slide_title = sprintf(
					/* translators: %d: slide number */
					__( 'Cabecera vídeo %d', 'theme_skema' ),
					$idx + 1
				);
				?>
            <div class="hero-yt-slide" data-slide-index="<?php echo esc_attr( (string) $idx ); ?>"
                data-youtube-id="<?php echo esc_attr( $slide_yt['id'] ); ?>">
                <div class="hero-yt-embed">
                    <iframe class="hero-yt-iframe" src="<?php echo esc_url( $embed_src ); ?>"
                        title="<?php echo esc_attr( $slide_title ); ?>" width="560" height="315"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen
                        loading="<?php echo 0 === $idx ? 'eager' : 'lazy'; ?>"></iframe>
                </div>
            </div>
            <?php
			}
			?>
        </div>
        <div class="home-hero-dots" aria-hidden="true"></div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    <section class="somos-lideres py-sm-5 py-2" id="que-es-skema">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1 px-0"></div>
                <div class="col-sm-11 col-12 px-sm-0 px-3 py-sm-4 py-1 item">
                    <p><?php the_field('tt_01') ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1 px-sm-0"></div>
                <div class="col-sm-5 col-12 px-sm-3 px-4 py-sm-0 py-4 align-self-center">
                    <?php if( have_rows('txt_01') ): ?>
                    <?php while( have_rows('txt_01') ): the_row(); 
                        $sbtt = get_sub_field('sbtt'); 
                        $descr = get_sub_field('descr'); 
                        $txt_btn = get_sub_field('txt_btn'); 
                        $enlace = get_sub_field('enlace'); ?>
                    <h2 class="mb-2"><?php echo $sbtt; ?></h2>
                    <p class="mb-4 mt-4 mt-sm-0"><?php echo $descr; ?></p>
                    <a href="<?php echo $enlace; ?>" class="btn-nosotros"><?php echo $txt_btn; ?></a>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-5 col-12 px-5 px-sm-3">
                    <div class="card px-0 px-sm-3">
                        <?php $url_video = get_field('video-home'); 
                    if ($url_video) : ?>
                        <video id="video-home" src="<?php echo esc_url($url_video); ?>" autoplay="" playsinline=""
                            loop="" defaultmuted="" muted="">
                            Your browser does not support HTML5 video.
                        </video>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-1"></div>
            </div>
        </div>
    </section>
    <!-- <section class="oportu pt-3 pb-sm-4" id="oportunidad">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-1 px-0"></div>
                <div class="col-sm-11 col-12 px-0 py-sm-4 item">
                    <p><?php the_field('tt_02') ?></p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-12 px-0">
                    <div class="slider-oportu">
                        <?php if ( have_rows('slider_oportunidad') ) : ?>
                        <?php while( have_rows('slider_oportunidad') ) : the_row(); ?>
                        <div class="item-team">
                            <img src="<?php the_sub_field('img'); ?>" alt="" class="img-fluid w-100">
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div class="div-flechas">
                        <button class="custom-prev">
                            <?php echo file_get_contents(get_template_directory() . '/img/flecha-prev.svg'); ?>
                        </button>
                        <button class="custom-next">
                            <?php echo file_get_contents(get_template_directory() . '/img/flecha-next.svg'); ?>
                        </button>
                    </div>
                </div>
                <?php if( have_rows('txt_logos') ): ?>
                <?php while( have_rows('txt_logos') ): the_row(); 
                    $url_img_fondo = get_sub_field('img_fondo'); 
                    $logo_izq = get_sub_field('logo_izq'); 
                    $logo_dere = get_sub_field('logo_dere'); 
                    $sbtt = get_sub_field('sbtt'); 
                    $desc = get_sub_field('desc'); ?>
                <div class="col-sm-6 col-12 pt-3 pb-5 img-fondo"
                    style="background-image: url('<?php echo esc_url($url_img_fondo); ?>');">
                    <div class="row pt-sm-4 pb-sm-3">
                        <div class="col-sm-1 col-1 px-0"></div>
                        <div class="col-sm col-5 px-sm-3 px-3">
                            <img src="<?php echo esc_url($logo_izq); ?>" alt="<?php the_title_attribute(); ?>"
                                class="img-fluid w-100">
                        </div>
                        <div class="col-sm-6 col-5 pt-sm-0 pt-2 px-sm-5 px-2 tt-oportu">
                            <img src="<?php echo esc_url($logo_dere); ?>" alt="<?php the_title_attribute(); ?>"
                                class="img-fluid w-75">
                        </div>
                        <div class="col-sm-1 col-1 px-0"></div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-sm-1 px-0"></div>
                        <div class="col-sm-10 col-12 pl-sm-0 pr-sm-5 px-4 txt-oportu">
                            <h3 class="m-sm-0 mb-3"><?php echo $sbtt; ?></h3>
                            <?php echo $desc; ?>
                        </div>
                        <div class="col-sm-1 px-0"></div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </section> -->
    <?php
$renvia_slider_ids = get_field( 'inicio_renvia_relacion' );
if ( ! is_array( $renvia_slider_ids ) ) {
	$renvia_slider_ids = array();
}
$renvia_slider_validos = array();
foreach ( $renvia_slider_ids as $rid ) {
	if ( is_object( $rid ) && isset( $rid->ID ) ) {
		$rid = (int) $rid->ID;
	} elseif ( is_array( $rid ) && isset( $rid['ID'] ) ) {
		$rid = (int) $rid['ID'];
	} else {
		$rid = absint( $rid );
	}
	if ( ! $rid || 'publish' !== get_post_status( $rid ) ) {
		continue;
	}
	$pt_r = get_post_type( $rid );
	if ( ! in_array( $pt_r, array( 'proyectos', 'landings' ), true ) ) {
		continue;
	}
	if ( ! theme_skema_inicio_renvia_incluir_en_slider( $rid ) ) {
		continue;
	}
	$renvia_slider_validos[] = $rid;
}
$n_renvia = count( $renvia_slider_validos );
if ( $n_renvia > 0 ) :
	$tit_renvia = get_field( 'inicio_renvia_titulo' );
	if ( ! is_string( $tit_renvia ) || $tit_renvia === '' ) {
		$tit_renvia = sprintf(
			/* translators: %s: «PROYECTOS» en <strong>, salto de línea y «en construcción y ventas» en <span>. */
			__( 'CONOCE NUESTROS %s', 'theme_skema' ),
			'<strong>' . esc_html__( 'PROYECTOS', 'theme_skema' ) . '</strong><br /> <span class="skema-proyectos__h2-subline">' . esc_html__( 'en construcción y ventas', 'theme_skema' ) . '</span>'
		);
	}
	$sub_renvia = get_field( 'inicio_renvia_subtitulo' );
	$tit_renvia_safe = wp_kses_post( $tit_renvia );
	?>
    <section class="skema-proyectos" id="proyectos"
        aria-label="<?php echo esc_attr( wp_strip_all_tags( $tit_renvia ) ); ?>">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-11 col-12 px-sm-0 px-3 py-sm-4 py-1 item text-right">
                    <p class="text-right"><b>02</b><?php echo esc_html( __( 'PROYECTOS', 'theme_skema' ) ); ?></p>
                </div>
                <div class="col-sm-1 px-0"></div>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-1 px-0"></div>
                <div class="col-sm-11 col-12 px-sm-0 px-3 py-sm-4 py-1 item">
                    <div class="skema-proyectos__head">
                        <?php if ( is_string( $sub_renvia ) && $sub_renvia !== '' ) : ?>
                        <span class="skema-proyectos__subtitle"><?php echo esc_html( $sub_renvia ); ?></span>
                        <?php endif; ?>
                        <h2><?php echo $tit_renvia_safe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid skema-proyectos__fluid">
            <div class="skema-proyectos__slider" data-slides-count="<?php echo esc_attr( (string) $n_renvia ); ?>">
                <?php
			foreach ( $renvia_slider_validos as $pid_renv ) {
				theme_skema_render_inicio_renvia_ficha_card( $pid_renv );
			}
			?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <section class="mapa pt-sm-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4"></div>
                <div class="col-sm-8 col-12 pt-sm-3 pl-sm-5">
                    <div class="row row-select">
                        <div class="col-sm-4 col-12 pl-sm-4 py-4 py-sm-0 div-select align-self-center">
                            <label for="selectMapas" class="screen-reader-text">
                                <?php esc_html_e( 'Selecciona un tipo de proyecto para ver su mapa', 'theme_skema' ); ?>
                            </label>
                            <select id="selectMapas">
                                <?php if (have_rows('info_mapas')): $c = 0;
                				while (have_rows('info_mapas')): the_row(); ?>
                                <option value="map-<?php echo $c; ?>"><?php the_sub_field('tipo_proyecto'); ?></option>
                                <?php $c++; 
								endwhile;
            				endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-8 col-12 pl-sm-4 py-sm-4 p-4 div-pmapas">
                            <p class="pl-sm-3 m-0 px-5 px-sm-0">
                                <?php the_field('txt_mapas') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (have_rows('info_mapas')): $c = 0;
            while (have_rows('info_mapas')): the_row(); ?>
            <div id="map-<?php echo $c; ?>" class="divMapa row" style="display:none;">
                <div class="col-sm-5 col-12 pl-sm-5 order-1 order-sm-0 py-sm-0 py-4">
                    <img class="img-fluid pl-sm-5 w-100 img-mapa" src="<?php the_sub_field('img_mapa'); ?>"
                        alt="<?php echo esc_attr( sprintf( __( 'Mapa de proyecto %d', 'theme_skema' ), ( $c + 1 ) ) ); ?>">
                </div>
                <div class="col-sm-7 col-12 pt-sm-5 pr-sm-5 order-0 order-sm-1 py-4">
                    <div class="row pt-sm-4 pr-sm-3">
                        <div
                            class="col-sm-6 col-12 py-4 pl-5 d-flex flex-sm-column justify-content-start flex-row align-items-center">
                            <?php $ico_txt1 = get_field('ico_txt1'); 
                                if ($ico_txt1) :
                                    $ico = $ico_txt1['ico'];
                                    $ico_txt1_text = isset( $ico_txt1['text'] ) ? trim( (string) $ico_txt1['text'] ) : ''; ?>
                            <?php if ($ico) : ?>
                            <img class="ico-cons" src="<?= esc_url($ico); ?>"
                                alt="<?php echo esc_attr( $ico_txt1_text !== '' ? $ico_txt1_text : __( 'Ícono de característica', 'theme_skema' ) ); ?>">
                            <?php endif; ?>
                            <?php endif; ?>
                            <div class="ml-3 d-flex flex-column align-items-start align-items-sm-center">
                                <?php if ($ico_txt1) :
                                        $text = $ico_txt1['text']; 
                                        if ($text) : ?>
                                <h5 class="mb-0 mt-sm-3"><?= esc_html($text); ?></h5>
                                <?php endif;                                       
                                     endif; ?>
                                <hr class="d-none d-sm-block">
                                <p class="mb-0"><?php the_sub_field('const'); ?> m²</p>
                            </div>
                        </div>
                        <div
                            class="col-sm-6 col-12 py-4 pl-5 d-flex flex-sm-column justify-content-start flex-row align-items-center">
                            <?php $ico_txt2 = get_field('ico_txt2'); 
                                if ($ico_txt2) :
                                    $ico = $ico_txt2['ico'];
                                    $ico_txt2_text = isset( $ico_txt2['text'] ) ? trim( (string) $ico_txt2['text'] ) : ''; ?>
                            <?php if ($ico) : ?>
                            <img class="ico-proye" src="<?= esc_url($ico); ?>"
                                alt="<?php echo esc_attr( $ico_txt2_text !== '' ? $ico_txt2_text : __( 'Ícono de característica', 'theme_skema' ) ); ?>">
                            <?php endif; ?>
                            <?php endif; ?>
                            <div class="ml-3 d-flex flex-column align-items-start align-items-sm-center">
                                <?php if ($ico_txt2) :
                                        $text = $ico_txt2['text']; 
                                        if ($text) : ?>
                                <h5 class="mb-0 mt-sm-3"><?= esc_html($text); ?></h5>
                                <?php endif;                                       
                                     endif; ?>
                                <hr>
                                <p class="mb-0"><?php the_sub_field('ejec'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="row  pl-5 pl-sm-0 py-4 pt-sm-4 pt-3 mt-sm-4">
                        <div
                            class="col-sm-6 col-2 py-4 px-0 px-sm-3 d-flex flex-column align-items-sm-end justify-content-sm-center div-border">
                            <div class="d-flex flex-column justify-content-center">
                            <?php $ico_txt3 = get_field('ico_txt3'); 
                                if ($ico_txt3) :
                                    $ico = $ico_txt3['ico'];
                                    $ico_txt3_text = isset( $ico_txt3['text'] ) ? trim( (string) $ico_txt3['text'] ) : ''; ?>
                                <?php if ($ico) : ?>
                                <img class="ico-merc mx-auto" src="<?= esc_url($ico); ?>"
                                    alt="<?php echo esc_attr( $ico_txt3_text !== '' ? $ico_txt3_text : __( 'Ícono de característica', 'theme_skema' ) ); ?>">
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($ico_txt3) :
                                   $text = $ico_txt3['text']; 
                                   if ($text) : ?>
                                <h5 class="mb-0 mt-3 d-none d-sm-block"><?= esc_html($text); ?></h5>
                                <?php endif;                                       
                                endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6 col-8 d-flex flex-column justify-content-center">
                            <p class="mb-0"><?php the_sub_field('mer'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php $c++; 
		    endwhile;
        endif; ?>
        </div>
    </section>
    <?php
$img_fondo = get_field('img_fondo'); 
$img_fondo = $img_fondo ? esc_url($img_fondo) : '';
?>
    <!-- <section class="necesi py-sm-5" style="<?php echo $img_fondo ? "background-image: url('{$img_fondo}');" : ''; ?>">
        <div class="container-fluid px-sm-5">
            <div class="row">
                <div class="col-sm-1 col-1 align-self-center d-flex justify-content-center">
                    <button class="nec-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="46" viewBox="0 0 35 56" fill="none">
                            <path
                                d="M25.1089 2.52747e-07L2.52747e-07 28L25.1089 56L34.5248 45.5L18.8317 28L34.5248 10.5L25.1089 2.52747e-07Z"
                                fill="#00347A" />
                        </svg>
                    </button>
                </div>
                <div class="col-sm-10 col-10 py-sm-3 px-sm-0 pt-5 px-3">
                    <div class="slider-necesitas">
                        <?php if ( have_rows('slider_necesitas') ) : ?>
                        <?php
						while ( have_rows( 'slider_necesitas' ) ) :
							the_row();
							$txt_nec  = get_sub_field( 'txt' );
							$alt_nec  = $txt_nec ? wp_trim_words( wp_strip_all_tags( $txt_nec ), 18, '…' ) : get_bloginfo( 'name' );
							?>
                        <div class="item-nece">
                            <div class="row py-sm-5">
                                <div class="col-sm-4 col-12 px-5 pb-5 pb-sm-0 order-1 order-sm-0">
                                    <img src="<?php the_sub_field('img'); ?>" alt="<?php echo esc_attr( $alt_nec ); ?>"
                                        class="img-fluid w-100 m-auto">
                                </div>
                                <div class="col-sm-8 col-12 py-4 py-sm-0 align-self-center order-0 order-sm-1">
                                    <p><?php the_sub_field('txt'); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-1 col-1 align-self-center d-flex justify-content-center">
                    <button class="nec-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="46" viewBox="0 0 35 56" fill="none">
                            <path
                                d="M9.41587 56L34.5248 28L9.41587 -2.44784e-06L3.02699e-05 10.5L15.6931 28L2.7526e-05 45.5L9.41587 56Z"
                                fill="#072972" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </section> -->
    <?php get_template_part( 'template-parts/content', 'fcontacto' ); ?>
</main>
<?php get_footer(); ?>