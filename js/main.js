jQuery(document).ready(function($) {
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 50) { // Ajusta este valor según la altura deseada para iniciar el cambio
            $("#principal-menu").addClass("menu-scrolled");
        } else {
            $("#principal-menu").removeClass("menu-scrolled");
        }
    });
    $('.slick-slider-banner:not(.slick-slider-banner--youtube)').each(function () {
        var $slider = $(this);
        var $dotsContainer = $slider.closest('.slider-banner').find('.home-hero-dots').first();
        var sliderConfig = {
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: false,
            dots: $dotsContainer.length > 0
        };
        if ($dotsContainer.length) {
            sliderConfig.appendDots = $dotsContainer;
        }
        $slider.slick(sliderConfig);
    });
    var $ytBanners = $('.slick-slider-banner--youtube');
    if ($ytBanners.length) {
        $ytBanners.on('init afterChange', function (event, slick, currentSlide) {
            $('#principal-menu').addClass('navbar-glass-youtube');
            var idx = typeof currentSlide === 'number' ? currentSlide : (slick && typeof slick.currentSlide === 'number' ? slick.currentSlide : 0);
            $(this).find('.hero-yt-slide').each(function () {
                var $wrap = $(this);
                var slideIndex = parseInt($wrap.attr('data-slide-index'), 10);
                var vid = $wrap.attr('data-youtube-id');
                if (!vid || isNaN(slideIndex)) {
                    return;
                }
                var ap = slideIndex === idx ? '1' : '0';
                var url = 'https://www.youtube-nocookie.com/embed/' + vid + '?rel=0&controls=0&fs=0&disablekb=1&iv_load_policy=3&modestbranding=1&playsinline=1&mute=1&autoplay=' + ap;
                $wrap.find('iframe').attr('src', url);
            });
        });
        $ytBanners.each(function () {
            var $slider = $(this);
            var $dotsContainer = $slider.closest('.slider-banner').find('.home-hero-dots').first();
            var sliderConfig = {
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: false,
                autoplaySpeed: 5000,
                infinite: false,
                arrows: false,
                dots: $dotsContainer.length > 0
            };
            if ($dotsContainer.length) {
                sliderConfig.appendDots = $dotsContainer;
            }
            $slider.slick(sliderConfig);
        });
    }

    function initInicioDestacadosSlick(width) {
        var $d = $('.project-slider--destacados-inicio');
        if (!$d.length) {
            return;
        }
        var projectCount = parseInt($d.data('projects-count'), 10) || 0;
        if ($d.hasClass('slick-initialized')) {
            $d.slick('unslick');
        }
        if (projectCount < 1) {
            return;
        }
        if (width < 700) {
            if (projectCount > 1) {
                if (!$d.hasClass('slick-initialized')) {
                    $d.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        prevArrow: '<button type="button" class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                        nextArrow: '<button type="button" class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>'
                    });
                } else {
                    $d.slick('setPosition');
                }
            } else if (projectCount === 1) {
                if (!$d.hasClass('slick-initialized')) {
                    $d.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        prevArrow: '',
                        nextArrow: '',
                        draggable: false
                    });
                } else {
                    $d.slick('setPosition');
                }
            }
        } else {
            if (projectCount > 2) {
                if (!$d.hasClass('slick-initialized')) {
                    $d.slick({
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        prevArrow: '<button type="button" class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                        nextArrow: '<button type="button" class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>'
                    });
                } else {
                    $d.slick('setPosition');
                }
            }
        }
    }

    function initSkemaInicioRenviaServiceSlick() {
        var $s = $('.skema-proyectos__slider');
        if (!$s.length) {
            return;
        }
        var n = parseInt($s.data('slides-count'), 10) || 0;
        if (n < 1) {
            return;
        }
        if ($s.hasClass('slick-initialized')) {
            $s.slick('setPosition');
            return;
        }
        var show4 = Math.min(4, Math.max(1, n));
        var show3 = Math.min(3, Math.max(1, n));
        var show2 = Math.min(2, Math.max(1, n));
        var arrowPrev = '<button type="button" class="proyect-prev skema-renvia-slick-arrow" aria-label="Anterior"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>';
        var arrowNext = '<button type="button" class="proyect-next skema-renvia-slick-arrow" aria-label="Siguiente"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>';
        try {
            $s.slick({
                slidesToShow: show4,
                slidesToScroll: 1,
                infinite: false,
                dots: false,
                prevArrow: arrowPrev,
                nextArrow: arrowNext,
                responsive: [
                    {
                        breakpoint: 1400,
                        settings: {
                            slidesToShow: show3,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: show2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        } catch (err) {
            if (window.console && console.warn) {
                console.warn('Skema inicio renvia slick:', err);
            }
        }
    }

    /**
     * Carrusel Slick de "Proyectos similares" en single-proyectos.
     * Selector acotado a .similares para no interferir con .skema-proyectos ni otros sliders.
     */
    function initProyectosSimilaresSlick(width) {
        var $slider = $('.similares .slider_project--similares');
        if (!$slider.length) {
            return;
        }
        var projectCount = parseInt($slider.attr('data-projects-count'), 10) || 0;
        if (projectCount < 1) {
            return;
        }
        var prevArrow = '<button class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>';
        var nextArrow = ' <button class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>';

        if (width < 700) {
            if (projectCount > 1) {
                if (!$slider.hasClass('slick-initialized')) {
                    $slider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        prevArrow: prevArrow,
                        nextArrow: nextArrow
                    });
                } else {
                    $slider.slick('setPosition');
                }
            } else if (projectCount === 1) {
                if (!$slider.hasClass('slick-initialized')) {
                    $slider.slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        prevArrow: '',
                        nextArrow: '',
                        draggable: false
                    });
                } else {
                    $slider.slick('setPosition');
                }
            }
        } else {
            if (projectCount > 2) {
                if (!$slider.hasClass('slick-initialized')) {
                    $slider.slick({
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        prevArrow: prevArrow,
                        nextArrow: nextArrow
                    });
                } else {
                    $slider.slick('setPosition');
                }
            }
        }
    }

    $('.slider-oportu').slick({
        dots: false,
        infinite: true,
       autoplay: true,
       autoplaySpeed: 2000,
        slidesToShow: 1,
        arrows: true,
        slidesToScroll: 1,
        prevArrow: $('.custom-prev'),
        nextArrow: $('.custom-next'),
    });
    $('.slider-equipo').slick({
        dots: false,
        infinite: true,
        autoplay: true,
        autoplaySpeed: 2000,
        slidesToShow: 5,
        arrows: true,
        slidesToScroll: 1,
        prevArrow: $('.f-prev'),
        nextArrow: $('.f-next'),
    });
    $('.slider-necesitas').slick({
        dots: false,
        infinite: true,
       //autoplay: true,
       //autoplaySpeed: 2000,
        slidesToShow: 1,
        arrows: true,
        slidesToScroll: 1,
        prevArrow: $('.nec-prev'),
        nextArrow: $('.nec-next'),
    });


   // Mostrar el mapa seleccionado al cargar
$('#' + $('#selectMapas').val()).fadeIn();

// Al cambiar el valor del select
$('#selectMapas').on('change', function() {
    // Ocultar todos los divs primero con fade
    $('.divMapa').fadeOut(100);

    // Mostrar el div seleccionado con fade
    const selected = $(this).val();
    setTimeout(function() {
        $('#' + selected).fadeIn(100);
    }, 100); // Espera a que se desvanezcan antes de mostrar el nuevo
});

// Rotar flecha cuando se hace clic
$('#selectMapas').on('click', function() {
    $(this).parent('.div-select').toggleClass('rotated');
});

// Quitar rotación al perder foco o cambiar
$(document).on('blur change', '#selectMapas', function() {
    $(this).parent('.div-select').removeClass('rotated');
});



        $('.divContact').hide();
  $('.' + $('#selectContact').val()).fadeIn(300);

  $('#selectContact').on('change', function() {
    // Ocultar todos con fade
    $('.divContact').fadeOut(300);

    // Mostrar el div seleccionado con fade
    $('.' + $(this).val()).fadeIn(300);
  }); 

    $('.btn-cerrar').click(function() {
        $('.navbar-collapse').css({'right':'-100%'});
    });
    $('.btn-abrir').click(function(){
		$('.navbar-collapse').css({'right':'0'});
        
	});
    // $('.navbar-toggler').click(function() {
    //       // Toggle menu visibility
    //     $('#menuPrincipal').collapse('toggle');
    // });

    // Cerrar el menú al hacer clic en un enlace
    $('.navbar-nav a').click(function() {
        $('.navbar-collapse').css({'right':'-100%'});
        // Cambiar el ícono a menú de hamburguesa
    });


    var lastScrollTop = 0;
    var navbar = $('#principal-menu');

    $(window).scroll(function() {
        // Solo ejecuta el código si el ancho de la pantalla es menor o igual a 992px
        if ($(window).width() <= 500) {
            var scrollTop = $(this).scrollTop();

            if (scrollTop > lastScrollTop) {
                // Desplazamiento hacia abajo
                
                navbar.css('top', '-100px'); // Ajusta esto según la altura de tu navbar
            } else {
                // Desplazamiento hacia arriba
                navbar.css('top', '0');
               // $("#menuPrincipal").removeClass("show");
            }
            lastScrollTop = scrollTop;
        } else {
            // Asegura que el menú esté visible cuando la pantalla sea más grande
            navbar.css('top', '0');
        }
    });
    
    $(window).on('resize', function() {
        var width = $(window).width();
        if (width < 700) {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href"); // Activated tab
                var $slider = $(target).find('.project-slider');
                var projectCount = $slider.data('projects-count');
        
                if (projectCount > 1) {
                    
                    if (!$slider.hasClass('slick-initialized')) {
                        
                        $slider.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            //autoplay: true,
                            //autoplaySpeed: 2000,
                            prevArrow: '<button class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                            nextArrow: ' <button class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>',
                        });
                    } else {
                        $slider.slick('setPosition');
                        
                    }
                } else if (projectCount === 1) {
                    
                    if (!$slider.hasClass('slick-initialized')) {
                        //$('.flechas-slider-proyect').hide();
                        $slider.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            //autoplay: true,
                            //autoplaySpeed: 2000,
                            prevArrow: '',
                            nextArrow: '',
                            draggable: false, // Opcional: deshabilita el arrastre si solo hay un slide
                        });
                    } else {
                        $slider.slick('setPosition');
                    }
                }
            });
        
            // Inicializar el primer slider si es necesario
            var $firstSlider = $('#myTabContent .tab-pane.active .project-slider');
            if ($firstSlider.data('projects-count') > 1 && !$firstSlider.hasClass('slick-initialized')) {
                $firstSlider.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    //autoplay: true,
                    //autoplaySpeed: 2000,
                    prevArrow: '<button class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                    nextArrow: ' <button class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>',
                        
                });
                
            }
        } else {
    
            // $('#id_slider_proyectos').removeClass('slide-proyectos');
            //$('#recetas_result').removeClass('slide-recetas');
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href"); // Activated tab
                var $slider = $(target).find('.project-slider');
                var projectCount = $slider.data('projects-count');
        
                if (projectCount > 2) {
                    if (!$slider.hasClass('slick-initialized')) {
                        $slider.slick({
                            slidesToShow: 2,
                            slidesToScroll: 2,
                            //autoplay: true,
                            //autoplaySpeed: 2000,
                            prevArrow: '<button class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                            nextArrow: ' <button class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>',
                        
                        });
                    } else {
                        $slider.slick('setPosition');
                    }
                }
            });
        
            // Inicializar el primer slider si es necesario
            var $firstSlider = $('#myTabContent .tab-pane.active .project-slider');
            if ($firstSlider.data('projects-count') > 2 && !$firstSlider.hasClass('slick-initialized')) {
                $firstSlider.slick({
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    //autoplay: true,
                    //autoplaySpeed: 2000,
                    prevArrow: '<button class="proyect-prev"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 53 53" fill="none"><ellipse cx="26.2061" cy="26.6026" rx="26.2141" ry="26.1988" fill="#FC7416"/><path d="M28.4076 7.39893L10.9769 26.8252L28.4076 46.2515L34.9441 38.9667L24.0499 26.8252L34.9441 14.6838L28.4076 7.39893Z" fill="white"/></svg></button>',
                    nextArrow: ' <button class="proyect-next"><svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 54 53" fill="none"><ellipse cx="27.2027" cy="26.6025" rx="26.2141" ry="26.1988" transform="rotate(-180 27.2027 26.6025)" fill="#FC7416"/><path d="M25.0013 45.8059L42.4319 26.3796L25.0013 6.95331L18.4648 14.2382L29.359 26.3796L18.4648 38.521L25.0013 45.8059Z" fill="white"/></svg></button>',
                });
            }
          
        }
        initProyectosSimilaresSlick(width);
        initInicioDestacadosSlick(width);
        initSkemaInicioRenviaServiceSlick();
      }).trigger('resize');

    initSkemaInicioRenviaServiceSlick();



      // Inicialización condicional de Slick en cada cambio de tab
    

});



jQuery(document).ready(function($) {
    $('.tab-pane').each(function() {
        const $pane = $(this);
        const $scrollContainer = $pane.find('.timeline-scroll');
        const scrollStep = 150; // píxeles por clic

        $pane.find('.arrow-down').on('click', function() {
            $scrollContainer.animate({
                scrollTop: $scrollContainer.scrollTop() + scrollStep
            }, 500);
        });

        $pane.find('.arrow-up').on('click', function() {
            $scrollContainer.animate({
                scrollTop: $scrollContainer.scrollTop() - scrollStep
            }, 500);
        });
    });
});
jQuery(document).ready(function($) {
    var hasVideo = $('#video_proy').length > 0;
    var hasMap   = $('#ubica').length > 0;
    // Si solo hay mapa, muéstralo
    if (!hasVideo && hasMap) {
        $('#ubica').show();
    }
    // Si solo hay vídeo, muéstralo
    else if (hasVideo && !hasMap) {
        $('#video_proy').show();
    }
    // Si hay ambos, inicializamos en vídeo
    else if (hasVideo && hasMap) {
        $('#video_proy').show();
        $('#ubica').hide();
        $('.btn-video').addClass('active');

        // Toggle
        $('.btn-ubica').on('click', function() {
            $('#video_proy').hide();
            $('#ubica').fadeIn();
            $('.btn-ubica').addClass('active');
            $('.btn-video').removeClass('active');
        });
        $('.btn-video').on('click', function() {
            $('#ubica').hide();
            $('#video_proy').fadeIn();
            $('.btn-video').addClass('active');
            $('.btn-ubica').removeClass('active');
        });
    }
});

jQuery(document).ready(function($) {
    // Inicializar todos los sliders
    $('.slider-avance').each(function(){
        $(this).slick({
            slidesToShow: 1,
            arrows: true,
            dots: true,
        });
    });

    $('#selectAvance').on('change', function() {
        var selected = $(this).val();

        // Ocultar todas las galerías y destruir slick
        $('.avance-galeria').each(function(){
            var slider = $(this).find('.slider-avance');
            if (slider.hasClass('slick-initialized')) {
                slider.slick('unslick');
            }
            $(this).hide().removeClass('active');
        });

        // Mostrar la seleccionada y volver a iniciar slick
        $('#' + selected).show().addClass('active');
        $('#' + selected).find('.slider-avance').slick({
            slidesToShow: 1,
            arrows: true,
            dots: true,
        });
    });
});

jQuery(document).ready(function($) {
    var $selectWrapper = $('.custom-select-wrapper');

    $('#selectAvance').on('click', function() {
        $selectWrapper.toggleClass('rotated');
    });

    $('#selectAvance').on('blur change', function() {
        $selectWrapper.removeClass('rotated');
    });
});

jQuery(document).ready(function($) {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // ID del tab activado

        if (target === '#zonas') {
            var $slider = $(target).find('.slider-zonas');

            // Solo inicializa si aún no ha sido inicializado
            if (!$slider.hasClass('slick-initialized')) {
                $slider.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: false,
                    autoplay: true,
                    autoplaySpeed: 3000
                });
            } else {
                // Si ya está inicializado, forzar su actualización
                $slider.slick('setPosition');
            }
        }
    });
});
