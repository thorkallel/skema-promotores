jQuery(document).ready(function($) {
    // Si Slick no está cargado en esta vista, evitamos errores y mantenemos funcional el resto del JS.
    if (typeof $.fn.slick !== 'function') {
        $.fn.slick = function() {
            return this;
        };
    }

    /**
     * Accesibilidad: anuncia cambios de slide para lectores de pantalla.
     */
    function ensureSlickLiveRegion($slider) {
        if (!$slider || !$slider.length) {
            return null;
        }
        var $region = $slider.siblings('.sr-only.slick-live-region').first();
        if ($region.length) {
            return $region;
        }
        $region = $('<span class="screen-reader-text sr-only slick-live-region" aria-live="polite" aria-atomic="true"></span>');
        $slider.after($region);
        return $region;
    }

    function announceSlickSlide($slider, slick, currentSlide) {
        if (!$slider || !$slider.length || !slick) {
            return;
        }
        var total = typeof slick.slideCount === 'number' ? slick.slideCount : $slider.find('.slick-slide:not(.slick-cloned)').length;
        if (!total || total < 1) {
            return;
        }
        var index = typeof currentSlide === 'number' ? currentSlide : slick.currentSlide;
        if (typeof index !== 'number' || index < 0) {
            index = 0;
        }
        var $region = ensureSlickLiveRegion($slider);
        if (!$region || !$region.length) {
            return;
        }
        var $current = $slider.find('.slick-slide[data-slick-index="' + index + '"]').first();
        var contextualTitle = '';
        if ($current.length) {
            contextualTitle = $.trim(
                $current.find('h1, h2, h3, h4, h5, h6, .project-title, .skema-proyectos__title, .ciudad-proyecto-meta').first().text()
            );
            if (!contextualTitle) {
                contextualTitle = $.trim($current.find('img[alt]').first().attr('alt') || '');
            }
        }

        var baseMessage = 'Diapositiva ' + (index + 1) + ' de ' + total;
        $region.text(contextualTitle ? (baseMessage + ': ' + contextualTitle) : baseMessage);
    }

    function bindSlickLiveAnnouncements(selector) {
        if (typeof $.fn.slick !== 'function') {
            return;
        }
        $(document).on('init.a11yLive afterChange.a11yLive reInit.a11yLive', selector, function(event, slick, currentSlide) {
            announceSlickSlide($(this), slick, currentSlide);
        });
    }

    bindSlickLiveAnnouncements('.slick-slider-banner');
    bindSlickLiveAnnouncements('.project-slider--destacados-inicio');
    bindSlickLiveAnnouncements('.skema-proyectos__slider');
    bindSlickLiveAnnouncements('.slider_project--similares');
    bindSlickLiveAnnouncements('.slider-avance');
    bindSlickLiveAnnouncements('.slider-zonas');

    /**
     * Bootstrap 4: los submenús anidados en navbar llaman a Dropdown._clearMenus() dentro de toggle(),
     * lo que cierra el padre antes de abrir el hijo. Evitamos _clearMenus en el clic del toggle anidado
     * y sustituimos toggle() solo para enlaces dentro de #menuPrincipal .navbar-nav .dropdown-menu.
     */
    (function patchBootstrap4NestedNavbarDropdowns() {
        if (!$.fn.dropdown || !$.fn.dropdown.Constructor) {
            return;
        }
        var Dropdown = $.fn.dropdown.Constructor;
        var CLASS_SHOW = 'show';
        var MENU_PRINCIPAL = '#menuPrincipal';
        var NESTED_TOGGLE = MENU_PRINCIPAL + ' .navbar-nav .dropdown-menu a[data-toggle="dropdown"]';

        var originalClearMenus = Dropdown._clearMenus;
        Dropdown._clearMenus = function(event) {
            if (event && event.type === 'click' && event.target && typeof event.target.closest === 'function') {
                var nestedToggle = event.target.closest(NESTED_TOGGLE);
                if (nestedToggle) {
                    return;
                }
            }
            originalClearMenus.apply(this, arguments);
            $(MENU_PRINCIPAL + ' .navbar-nav .dropdown-menu .dropdown-menu').removeClass(CLASS_SHOW);
            $(MENU_PRINCIPAL + ' .navbar-nav .dropdown-menu li.dropdown').removeClass(CLASS_SHOW);
        };

        var originalToggle = Dropdown.prototype.toggle;
        Dropdown.prototype.toggle = function() {
            var isNestedInPrincipalMenu = $(this._element).closest(MENU_PRINCIPAL + ' .navbar-nav .dropdown-menu').length > 0;
            if (!isNestedInPrincipalMenu) {
                return originalToggle.call(this);
            }

            if (this._element.disabled || $(this._element).hasClass('disabled')) {
                return;
            }

            var $menu = $(this._menu);
            var isActive = $menu.hasClass(CLASS_SHOW);
            var parentLi = Dropdown._getParentFromElement(this._element);
            var $parentUl = $(this._element).closest('ul.dropdown-menu');

            $parentUl.children('li.dropdown').not(parentLi).each(function() {
                var $sibling = $(this);
                $sibling.removeClass(CLASS_SHOW);
                $sibling.find('> .dropdown-menu').removeClass(CLASS_SHOW);
                $sibling.find('> a[data-toggle="dropdown"]').attr('aria-expanded', 'false');
            });

            if (isActive) {
                $menu.removeClass(CLASS_SHOW);
                $(parentLi).removeClass(CLASS_SHOW);
                this._element.setAttribute('aria-expanded', 'false');
                if (this._popper) {
                    this._popper.destroy();
                    this._popper = null;
                }
                return;
            }

            $menu.addClass(CLASS_SHOW);
            $(parentLi).addClass(CLASS_SHOW);
            this._element.setAttribute('aria-expanded', 'true');
        };
    })();

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
        var isLandingFull = $slider.hasClass('slick-slider-banner--landing-full');
        var sliderConfig = {
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            arrows: false,
            adaptiveHeight: !isLandingFull,
            dots: $dotsContainer.length > 0
        };
        if ($dotsContainer.length) {
            sliderConfig.appendDots = $dotsContainer;
        }
        $slider.slick(sliderConfig);
    });
    var $ytBanners = $('.slick-slider-banner--youtube');
    if ($ytBanners.length) {
        // Aplica el efecto de fondo del menú cuando hay vídeo de YouTube en el hero
        $ytBanners.on('init afterChange', function () {
            $('#principal-menu').addClass('navbar-glass-youtube');
        });

        $ytBanners.each(function () {
            var $slider = $(this);
            var $dotsContainer = $slider.closest('.slider-banner').find('.home-hero-dots').first();
            var isLandingFullYt = $slider.hasClass('slick-slider-banner--landing-full');
            var sliderConfig = {
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: false,
                infinite: false,
                arrows: false,
                adaptiveHeight: !isLandingFullYt,
                dots: $dotsContainer.length > 0
            };
            if ($dotsContainer.length) {
                sliderConfig.appendDots = $dotsContainer;
            }
            $slider.slick(sliderConfig);
            bindYouTubeAutoAdvance($slider);
        });
    }

    /**
     * Carga la IFrame API de YouTube una sola vez y resuelve cuando esté lista.
     */
    function loadYouTubeIframeApi() {
        if (window.__skemaYtApiPromise) {
            return window.__skemaYtApiPromise;
        }
        window.__skemaYtApiPromise = new Promise(function (resolve) {
            if (window.YT && typeof window.YT.Player === 'function') {
                resolve(window.YT);
                return;
            }
            var previousReady = window.onYouTubeIframeAPIReady;
            window.onYouTubeIframeAPIReady = function () {
                if (typeof previousReady === 'function') {
                    try { previousReady(); } catch (e) {}
                }
                resolve(window.YT);
            };
            if (!document.querySelector('script[data-skema-yt-api]')) {
                var script = document.createElement('script');
                script.src = 'https://www.youtube.com/iframe_api';
                script.async = true;
                script.setAttribute('data-skema-yt-api', '1');
                (document.head || document.documentElement).appendChild(script);
            }
        });
        return window.__skemaYtApiPromise;
    }

    /**
     * Avanza automáticamente al siguiente slide cuando termina el vídeo activo.
     * Si es el último, se detiene (no hay loop).
     */
    function bindYouTubeAutoAdvance($slider) {
        var $slides = $slider.find('.hero-yt-slide');
        if (!$slides.length) {
            return;
        }

        loadYouTubeIframeApi().then(function (YT) {
            if (!YT || typeof YT.Player !== 'function') {
                return;
            }

            var totalSlides = $slides.length;
            var players = new Array(totalSlides);

            $slides.each(function () {
                var $slide = $(this);
                var iframeEl = $slide.find('iframe.hero-yt-iframe').get(0);
                if (!iframeEl) {
                    return;
                }
                var slideIndex = parseInt($slide.attr('data-slide-index'), 10);
                if (isNaN(slideIndex)) {
                    return;
                }
                players[slideIndex] = new YT.Player(iframeEl, {
                    events: {
                        onReady: function (event) {
                            try { event.target.mute(); } catch (e) {}
                        },
                        onStateChange: function (event) {
                            if (event.data !== YT.PlayerState.ENDED) {
                                return;
                            }
                            var isLastSlide = slideIndex >= totalSlides - 1;
                            if (isLastSlide) {
                                return;
                            }
                            $slider.slick('slickNext');
                        }
                    }
                });
            });

            $slider.on('beforeChange.skemaYt', function (event, slick, currentSlide) {
                var previousPlayer = players[currentSlide];
                if (!previousPlayer || typeof previousPlayer.pauseVideo !== 'function') {
                    return;
                }
                try { previousPlayer.pauseVideo(); } catch (e) {}
            });

            $slider.on('afterChange.skemaYt', function (event, slick, currentSlide) {
                var nextPlayer = players[currentSlide];
                if (!nextPlayer || typeof nextPlayer.playVideo !== 'function') {
                    return;
                }
                try {
                    if (typeof nextPlayer.seekTo === 'function') {
                        nextPlayer.seekTo(0, true);
                    }
                    nextPlayer.playVideo();
                } catch (e) {}
            });
        });
    }

    /**
     * Inicio y landings: ocultar puntos del hero cuando el .slider-banner deja de cruzar el viewport.
     */
    function bindSkemaHomeHeroDotsVisibility() {
        var $dotsList = $(
            '.site-main--inicio .home-hero-dots, ' +
            '.page-template-theme_inicio-php .home-hero-dots, ' +
            '.site-main--landings .landing-hero .home-hero-dots'
        );
        if (!$dotsList.length) {
            return;
        }

        function syncDotsToBanner($dots, bannerEl) {
            if (!bannerEl) {
                return;
            }
            var rect = bannerEl.getBoundingClientRect();
            var isIntersecting = rect.bottom > 0 && rect.top < window.innerHeight;
            $dots.toggleClass('home-hero-dots--hero-off', !isIntersecting);
        }

        if (typeof IntersectionObserver !== 'undefined') {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    var $banner = $(entry.target);
                    var $dots = $banner.find('.home-hero-dots').first();
                    if (!$dots.length) {
                        return;
                    }
                    $dots.toggleClass('home-hero-dots--hero-off', !entry.isIntersecting);
                });
            }, { root: null, rootMargin: '0px', threshold: 0 });

            $dotsList.each(function () {
                var $dots = $(this);
                var bannerEl = $dots.closest('.slider-banner').get(0);
                if (!bannerEl) {
                    return;
                }
                syncDotsToBanner($dots, bannerEl);
                io.observe(bannerEl);
            });
            return;
        }

        $dotsList.each(function () {
            var $dots = $(this);
            var bannerEl = $dots.closest('.slider-banner').get(0);
            if (!bannerEl) {
                return;
            }
            $(window).on('scroll.skemaHeroDots resize.skemaHeroDots', function () {
                syncDotsToBanner($dots, bannerEl);
            });
            syncDotsToBanner($dots, bannerEl);
        });
    }
    bindSkemaHomeHeroDotsVisibility();

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
        $('.btn-abrir').attr('aria-expanded', 'false');
        $(this).attr('aria-expanded', 'false');
    });
    $('.btn-abrir').click(function(){
		$('.navbar-collapse').css({'right':'0'});
        $(this).attr('aria-expanded', 'true');
        $('.btn-cerrar').attr('aria-expanded', 'true');
        
	});
    // $('.navbar-toggler').click(function() {
    //       // Toggle menu visibility
    //     $('#menuPrincipal').collapse('toggle');
    // });

    // Cerrar el drawer solo en viewport donde el menú es off-canvas (no tocar desktop horizontal).
    $('.navbar-nav a').click(function() {
        var $link = $(this);
        if ($link.hasClass('dropdown-toggle') || $link.is('[data-toggle="dropdown"], [data-bs-toggle="dropdown"]')) {
            return;
        }
        var w = $(window).width();
        var isLandingsShell = $('body').hasClass('skema-landing-shell');
        if (isLandingsShell && w >= 1200) {
            return;
        }
        if (!isLandingsShell && w > 719) {
            return;
        }
        $('.navbar-collapse').css({'right':'-100%'});
        $('.btn-abrir').attr('aria-expanded', 'false');
        $('.btn-cerrar').attr('aria-expanded', 'false');
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
