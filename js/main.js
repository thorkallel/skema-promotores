jQuery(document).ready(function($) {
    /**
     * En móvil el chrome del navegador (y la barra de admin) tapa un header
     * fixed en top: 0 justo cuando el scroll vuelve al tope.
     */
    function readMobileHeaderTopInset() {
        var isMobile = window.matchMedia('(max-width: 719px)').matches;
        if (!isMobile) {
            return 0;
        }
        var inset = 0;
        if (window.visualViewport && window.visualViewport.offsetTop > 0) {
            inset = window.visualViewport.offsetTop;
        }
        var adminBar = document.getElementById('wpadminbar');
        var adminCoversHeader = adminBar && document.body.classList.contains('admin-bar') && window.matchMedia('(max-width: 782px)').matches;
        if (adminCoversHeader) {
            inset += adminBar.offsetHeight;
        }
        return inset;
    }

    function pinMobileHeaderToVisibleTop() {
        var nav = document.getElementById('principal-menu');
        if (!nav) {
            return;
        }
        var isMobile = window.matchMedia('(max-width: 719px)').matches;
        if (!isMobile) {
            nav.style.top = '';
            return;
        }
        nav.style.top = readMobileHeaderTopInset() + 'px';
    }

    /**
     * Inicio en móvil: deja el slider y el resto del contenido debajo del header fijo.
     */
    function syncInicioHeaderOffset() {
        var main = document.querySelector('.site-main--inicio');
        var nav = document.getElementById('principal-menu');
        if (!main || !nav) {
            return;
        }
        var isMobile = window.matchMedia('(max-width: 719px)').matches;
        var offset = isMobile ? readMobileHeaderTopInset() + nav.offsetHeight : 0;
        document.documentElement.style.setProperty('--skema-home-header-offset', offset + 'px');
    }
    pinMobileHeaderToVisibleTop();
    syncInicioHeaderOffset();
    $(window).on('resize.skemaHomeHeaderOffset load.skemaHomeHeaderOffset scroll.skemaHomeHeaderOffset', function () {
        pinMobileHeaderToVisibleTop();
        syncInicioHeaderOffset();
    });
    if (window.visualViewport) {
        window.visualViewport.addEventListener('resize', function () {
            pinMobileHeaderToVisibleTop();
            syncInicioHeaderOffset();
        });
        window.visualViewport.addEventListener('scroll', function () {
            pinMobileHeaderToVisibleTop();
            syncInicioHeaderOffset();
        });
    }
    if (window.ResizeObserver) {
        var inicioNav = document.getElementById('principal-menu');
        if (inicioNav && document.querySelector('.site-main--inicio')) {
            var inicioNavObserver = new ResizeObserver(syncInicioHeaderOffset);
            inicioNavObserver.observe(inicioNav);
        }
    }

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
        $region = $('<span class="screen-reader-text sr-only slick-live-region" aria-live="off" aria-atomic="true"></span>');
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
        if ($slider.data('skemaUserSlide') !== true) {
            $region.attr('aria-live', 'off');
            return;
        }
        $slider.data('skemaUserSlide', false);
        var $current = $slider.find('.slick-slide[data-slick-index="' + index + '"]').not('.slick-cloned').first();
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
        var message = contextualTitle ? (baseMessage + ': ' + contextualTitle) : baseMessage;
        $region.attr('aria-live', 'off').text('');
        window.setTimeout(function () {
            $region.attr('aria-live', 'polite').text(message);
        }, 40);
    }

    function skemaPrefersReducedMotion() {
        return document.documentElement.classList.contains('skema-reduced-motion')
            || (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
    }

    function skemaMarkUserSlide(slider) {
        if (!slider) {
            return;
        }
        var $slider = $(slider);
        var token = Date.now();
        $slider.data('skemaUserSlide', true);
        $slider.data('skemaUserSlideToken', token);
        window.setTimeout(function () {
            if ($slider.data('skemaUserSlideToken') === token) {
                $slider.data('skemaUserSlide', false);
            }
        }, 800);
    }

    function skemaCarouselName(slider) {
        if (slider.getAttribute('aria-label')) {
            return slider.getAttribute('aria-label');
        }
        var names = {
            'slick-slider-banner': 'Cabecera',
            'project-slider--destacados-inicio': 'Proyectos destacados',
            'skema-proyectos__slider': 'Proyectos',
            'slider_project--similares': 'Proyectos similares',
            'slider-avance': 'Avance de obra',
            'slider-zonas': 'Zonas comunes',
            'slider-oportu': 'Oportunidades',
            'slider-equipo': 'Equipo',
            'slider-necesitas': 'Lo que necesitas',
            'skema-landing-aliados-slider': 'Aliados',
            'plantas-slider-main': 'Planos del proyecto',
            'plantas-slider-nav': 'Miniaturas de planos'
        };
        var className;
        for (className in names) {
            if (slider.classList.contains(className)) {
                return names[className];
            }
        }
        return 'Carrusel';
    }

    function skemaSetSlideFocusable(slide, isFocusable) {
        var controls = slide.querySelectorAll('a, button, input, select, textarea');
        var i;
        for (i = 0; i < controls.length; i++) {
            var control = controls[i];
            if (control.classList.contains('slick-arrow')) {
                continue;
            }
            if (isFocusable) {
                if (control.getAttribute('data-skema-tab-locked') === '1') {
                    control.removeAttribute('tabindex');
                    control.removeAttribute('data-skema-tab-locked');
                }
                continue;
            }
            if (control.getAttribute('tabindex') !== '-1') {
                control.setAttribute('data-skema-tab-locked', '1');
            }
            control.setAttribute('tabindex', '-1');
        }
    }

    function skemaLabelCarouselSlides($slider, slick) {
        if (!slick) {
            return;
        }
        var total = slick.slideCount || 0;
        var visibleCount = slick.options && slick.options.slidesToShow ? slick.options.slidesToShow : 1;
        var current = typeof slick.currentSlide === 'number' ? slick.currentSlide : 0;
        $slider.find('.slick-slide').each(function () {
            var index = parseInt(this.getAttribute('data-slick-index'), 10);
            var isClone = this.classList.contains('slick-cloned') || isNaN(index) || index < 0;
            if (isClone) {
                this.setAttribute('aria-hidden', 'true');
                skemaSetSlideFocusable(this, false);
                return;
            }
            var isVisible = index >= current && index < current + visibleCount;
            this.setAttribute('role', 'group');
            this.setAttribute('aria-roledescription', 'diapositiva');
            this.setAttribute('aria-label', (index + 1) + ' de ' + total);
            if (isVisible) {
                this.removeAttribute('aria-hidden');
                skemaSetSlideFocusable(this, true);
                return;
            }
            this.setAttribute('aria-hidden', 'true');
            skemaSetSlideFocusable(this, false);
        });
    }

    function skemaAttachCarouselPause($slider, slick) {
        if (!$slider || !$slider.length || $slider.data('skemaPauseBound')) {
            return;
        }
        if (!slick || !slick.options || !slick.options.autoplay || skemaPrefersReducedMotion()) {
            return;
        }
        var parent = $slider.parent().get(0);
        if (!parent) {
            return;
        }
        $slider.data('skemaPauseBound', true);
        parent.classList.add('skema-has-pause');
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'skema-carousel-pause';
        button.setAttribute('aria-pressed', 'false');
        button.textContent = 'Pausar';
        parent.appendChild(button);
        button.addEventListener('click', function () {
            var isPaused = button.getAttribute('aria-pressed') === 'true';
            if (isPaused) {
                $slider.slick('slickPlay');
                button.setAttribute('aria-pressed', 'false');
                button.textContent = 'Pausar';
                return;
            }
            $slider.slick('slickPause');
            button.setAttribute('aria-pressed', 'true');
            button.textContent = 'Reproducir';
        });
    }

    function skemaDecorateCarousel($slider, slick) {
        var slider = $slider.get(0);
        if (!slider) {
            return;
        }
        slider.setAttribute('role', 'region');
        slider.setAttribute('aria-roledescription', 'carrusel');
        if (!slider.getAttribute('aria-label')) {
            slider.setAttribute('aria-label', skemaCarouselName(slider));
        }
        if (skemaPrefersReducedMotion() && slick && slick.options && slick.options.autoplay) {
            window.setTimeout(function () {
                if ($slider.hasClass('slick-initialized')) {
                    $slider.slick('slickPause');
                }
            }, 0);
        }
        skemaLabelCarouselSlides($slider, slick);
        skemaAttachCarouselPause($slider, slick);
        skemaSyncLandingHeroCopy($slider, slick);
        // Slick llama a initADA() después del evento init/afterChange y pisa el aria-label.
        window.setTimeout(function () {
            skemaLabelHeroDots($slider);
        }, 0);
    }

    function skemaHeroDotLabel(index, total) {
        return 'Ir a la diapositiva ' + (index + 1) + ' de ' + total;
    }

    function skemaHeroDotPaging(slider, index) {
        var total = slider.slideCount || 0;
        return '<button type="button" aria-label="' + skemaHeroDotLabel(index, total) + '"></button>';
    }

    function skemaLabelHeroDots($slider) {
        var $items = $slider.closest('.slider-banner').find('.home-hero-dots .slick-dots > li');
        if (!$items.length) {
            $items = $slider.siblings('.home-hero-dots').find('.slick-dots > li');
        }
        if (!$items.length) {
            $items = $slider.find('.slick-dots > li');
        }
        var total = $items.length;
        $items.each(function (index) {
            var button = this.querySelector('button');
            if (!button) {
                return;
            }
            button.setAttribute('aria-label', skemaHeroDotLabel(index, total));
            if (this.classList.contains('slick-active')) {
                button.setAttribute('aria-current', 'true');
                return;
            }
            button.removeAttribute('aria-current');
        });
    }

    function skemaWriteLandingHeroField(group, field, value) {
        var node = group.querySelector('[data-hero-field="' + field + '"]');
        if (!node) {
            return;
        }
        if (field === 'location') {
            var text = node.querySelector('.landing-full-hero__location-text');
            if (text) {
                text.textContent = value;
            }
        } else {
            node.textContent = value;
        }
        if (value === '') {
            node.classList.add('is-absent');
            return;
        }
        node.classList.remove('is-absent');
    }

    function skemaSyncLandingHeroCopy($slider, slick) {
        if (!$slider.hasClass('slick-slider-banner--landing-full') || !slick) {
            return;
        }
        var hero = $slider.closest('.landing-hero--landing-full').get(0);
        if (!hero) {
            return;
        }
        var banner = $slider.closest('.slider-banner').get(0);
        var slot = 'shared';
        if (banner && banner.classList.contains('d-sm-none')) {
            slot = 'mob';
        } else if (banner && banner.classList.contains('d-none')) {
            slot = 'desk';
        }
        var group = hero.querySelector('[data-hero-slot="' + slot + '"]');
        if (!group) {
            return;
        }
        var index = typeof slick.currentSlide === 'number' ? slick.currentSlide : 0;
        var slide = $slider.find('.slick-slide').filter(function () {
            return !this.classList.contains('slick-cloned') && this.getAttribute('data-slick-index') === String(index);
        }).get(0);
        if (slide && !slide.hasAttribute('data-slide-line')) {
            slide = slide.querySelector('.landing-full-slide');
        }
        if (!slide) {
            return;
        }
        skemaWriteLandingHeroField(group, 'line', slide.getAttribute('data-slide-line') || '');
        skemaWriteLandingHeroField(group, 'tagline', slide.getAttribute('data-slide-tagline') || '');
        skemaWriteLandingHeroField(group, 'location', slide.getAttribute('data-slide-location') || '');
    }

    $(document).on('init reInit afterChange', '.slick-slider', function (event, slick) {
        skemaDecorateCarousel($(this), slick);
    });

    document.addEventListener('pointerdown', function (event) {
        if (!event.target || !event.target.closest) {
            return;
        }
        if (event.target.closest('.skema-carousel-pause, .skema-ambient-pause')) {
            return;
        }
        var slider = event.target.closest('.slick-slider');
        if (!slider) {
            return;
        }
        skemaMarkUserSlide(slider);
    }, true);

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight' && event.key !== 'Home' && event.key !== 'End') {
            return;
        }
        if (!event.target || !event.target.closest) {
            return;
        }
        var slider = event.target.closest('.slick-slider');
        if (!slider) {
            return;
        }
        skemaMarkUserSlide(slider);
    }, true);

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

    function syncMenuScrolledState() {
        var scroll = $(window).scrollTop();
        var keepBarOnMobile = window.matchMedia('(max-width: 719px)').matches;
        if (keepBarOnMobile || scroll >= 50) {
            $("#principal-menu").addClass("menu-scrolled");
            return;
        }
        $("#principal-menu").removeClass("menu-scrolled");
    }
    $(window).on('scroll.skemaMenuBar resize.skemaMenuBar', syncMenuScrolledState);
    syncMenuScrolledState();
    $('.slick-slider-banner:not(.slick-slider-banner--youtube)').each(function () {
        var $slider = $(this);
        var $dotsContainer = $slider.closest('.slider-banner').find('.home-hero-dots').first();
        var isLandingFull = $slider.hasClass('slick-slider-banner--landing-full');
        var sliderConfig = {
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: !skemaPrefersReducedMotion(),
            autoplaySpeed: 3000,
            pauseOnHover: true,
            pauseOnFocus: true,
            arrows: false,
            adaptiveHeight: !isLandingFull,
            dots: $dotsContainer.length > 0,
            customPaging: skemaHeroDotPaging
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
                dots: $dotsContainer.length > 0,
                customPaging: skemaHeroDotPaging
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

    function skemaAttachYouTubePause($slider, players) {
        var banner = $slider.closest('.slider-banner').get(0);
        if (!banner || banner.querySelector('.skema-ambient-pause')) {
            return;
        }
        banner.classList.add('skema-has-pause');
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'skema-ambient-pause';
        var startPaused = skemaPrefersReducedMotion();
        $slider.data('skemaYtPaused', startPaused);
        button.setAttribute('aria-pressed', startPaused ? 'true' : 'false');
        button.textContent = startPaused ? 'Reproducir vídeo' : 'Pausar vídeo';
        banner.appendChild(button);
        button.addEventListener('click', function () {
            var isPaused = $slider.data('skemaYtPaused') === true;
            var current = $slider.hasClass('slick-initialized') ? $slider.slick('slickCurrentSlide') : 0;
            var player = players[current];
            if (isPaused) {
                $slider.data('skemaYtPaused', false);
                if (player && typeof player.playVideo === 'function') {
                    try { player.playVideo(); } catch (e) {}
                }
                button.setAttribute('aria-pressed', 'false');
                button.textContent = 'Pausar vídeo';
                return;
            }
            $slider.data('skemaYtPaused', true);
            if (player && typeof player.pauseVideo === 'function') {
                try { player.pauseVideo(); } catch (e2) {}
            }
            button.setAttribute('aria-pressed', 'true');
            button.textContent = 'Reproducir vídeo';
        });
    }

    function skemaBindAmbientVideos() {
        var groups = document.querySelectorAll('.video-content, .skema-ambient-host');
        var i;
        for (i = 0; i < groups.length; i++) {
            skemaBindAmbientGroup(groups[i]);
        }
    }

    function skemaBindAmbientGroup(container) {
        var videos = container.querySelectorAll('video.skema-ambient-video');
        if (!videos.length) {
            return;
        }
        var button = container.querySelector('.skema-ambient-pause');
        if (!button) {
            button = document.createElement('button');
            button.type = 'button';
            button.className = 'skema-ambient-pause';
            container.classList.add('skema-has-pause');
            container.appendChild(button);
        }
        function setPlaying(isPlaying) {
            var n;
            for (n = 0; n < videos.length; n++) {
                var isHidden = window.getComputedStyle(videos[n]).display === 'none';
                if (isPlaying && !isHidden) {
                    var playPromise = videos[n].play();
                    if (playPromise && typeof playPromise.catch === 'function') {
                        playPromise.catch(function () {});
                    }
                    continue;
                }
                videos[n].pause();
            }
            button.setAttribute('aria-pressed', isPlaying ? 'false' : 'true');
            button.textContent = isPlaying ? 'Pausar vídeo' : 'Reproducir vídeo';
        }
        setPlaying(!skemaPrefersReducedMotion());
        if (button.getAttribute('data-skema-bound') === '1') {
            return;
        }
        button.setAttribute('data-skema-bound', '1');
        button.addEventListener('click', function () {
            var isPaused = button.getAttribute('aria-pressed') === 'true';
            setPlaying(isPaused);
        });
    }

    /**
     * Reinicia un reproductor de YouTube desde el segundo 0.
     */
    function restartYouTubePlayer(player) {
        if (!player || typeof player.playVideo !== 'function') {
            return;
        }
        try {
            if (typeof player.seekTo === 'function') {
                player.seekTo(0, true);
            }
            player.playVideo();
        } catch (e) {}
    }

    /**
     * Pasa al siguiente vídeo. En el último vuelve al primero (bucle).
     * Un solo vídeo se reinicia en el mismo reproductor.
     * Slick sigue con infinite:false para no clonar iframes.
     */
    function advanceYouTubeSlider($slider, player, slideIndex, totalSlides) {
        if (skemaPrefersReducedMotion() || $slider.data('skemaYtPaused') === true) {
            return;
        }
        var hasNextSlide = slideIndex < totalSlides - 1;
        if (hasNextSlide) {
            $slider.slick('slickNext');
            return;
        }
        if (totalSlides <= 1) {
            restartYouTubePlayer(player);
            return;
        }
        $slider.slick('slickGoTo', 0);
    }

    /**
     * Avanza automáticamente al siguiente slide cuando termina el vídeo activo.
     * Al llegar al último, el recorrido vuelve al primero y sigue en bucle.
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
                            if (skemaPrefersReducedMotion() || $slider.data('skemaYtPaused') === true) {
                                try { event.target.pauseVideo(); } catch (e2) {}
                            }
                        },
                        onStateChange: function (event) {
                            if (event.data !== YT.PlayerState.ENDED) {
                                return;
                            }
                            advanceYouTubeSlider($slider, event.target, slideIndex, totalSlides);
                        }
                    }
                });
            });
            skemaAttachYouTubePause($slider, players);

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
                if (skemaPrefersReducedMotion() || $slider.data('skemaYtPaused') === true) {
                    try { nextPlayer.pauseVideo(); } catch (e) {}
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
    skemaBindAmbientVideos();

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
       autoplay: !skemaPrefersReducedMotion(),
       autoplaySpeed: 2000,
       pauseOnHover: true,
       pauseOnFocus: true,
        slidesToShow: 1,
        arrows: true,
        slidesToScroll: 1,
        prevArrow: $('.custom-prev'),
        nextArrow: $('.custom-next'),
    });
    $('.slider-equipo').slick({
        dots: false,
        infinite: true,
        autoplay: !skemaPrefersReducedMotion(),
        autoplaySpeed: 2000,
        pauseOnHover: true,
        pauseOnFocus: true,
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
    var mapLabel = $(this).find('option:selected').text();
    setTimeout(function() {
        $('#' + selected).fadeIn(100);
        $('#mapas-anuncio').text('Mostrando: ' + mapLabel);
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
    var contactLabel = $(this).find('option:selected').text();
    // Ocultar todos con fade
    $('.divContact').fadeOut(300);

    // Mostrar el div seleccionado con fade
    $('.' + $(this).val()).fadeIn(300);
    $('#contacto-anuncio').text('Mostrando: ' + contactLabel);
  }); 

    function skemaMenuIsOffCanvas() {
        var width = $(window).width();
        var isLandingsShell = $('body').hasClass('skema-landing-shell');
        if (isLandingsShell) {
            return width < 1200;
        }
        return width <= 719;
    }

    function skemaSetMobileMenuOpen(isOpen, returnFocus) {
        var menu = document.getElementById('menuPrincipal');
        var openButton = document.querySelector('.btn-abrir');
        if (!menu) {
            return;
        }
        if (!skemaMenuIsOffCanvas()) {
            menu.inert = false;
            menu.style.right = '';
            if (openButton) {
                openButton.setAttribute('aria-expanded', 'false');
            }
            return;
        }
        if (isOpen) {
            menu.style.right = '0';
            menu.inert = false;
            if (openButton) {
                openButton.setAttribute('aria-expanded', 'true');
            }
            var closeButton = menu.querySelector('.btn-cerrar');
            if (closeButton) {
                closeButton.focus();
            }
            return;
        }
        menu.style.right = '-100%';
        menu.inert = true;
        if (openButton) {
            openButton.setAttribute('aria-expanded', 'false');
            if (returnFocus) {
                openButton.focus();
            }
        }
    }

    skemaSetMobileMenuOpen(false, false);

    $('.btn-cerrar').click(function() {
        skemaSetMobileMenuOpen(false, true);
    });
    $('.btn-abrir').click(function(){
        skemaSetMobileMenuOpen(true, false);
	});
    $(document).on('keydown', function(event) {
        if (event.key !== 'Escape') {
            return;
        }
        if (!skemaMenuIsOffCanvas()) {
            return;
        }
        var openButton = document.querySelector('.btn-abrir');
        if (!openButton || openButton.getAttribute('aria-expanded') !== 'true') {
            return;
        }
        skemaSetMobileMenuOpen(false, true);
    });
    $(window).on('resize.skemaMenu', function() {
        var openButton = document.querySelector('.btn-abrir');
        var isOpen = openButton && openButton.getAttribute('aria-expanded') === 'true';
        skemaSetMobileMenuOpen(!!isOpen, false);
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
        skemaSetMobileMenuOpen(false, true);
    });

    $(window).on('resize', function() {
        var width = $(window).width();
        if (width < 700) {
            $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('href') || $(e.target).attr('data-target');
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
            $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr('href') || $(e.target).attr('data-target');
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
    $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr('href') || $(e.target).attr('data-target');

        if (target === '#zonas') {
            var $slider = $(target).find('.slider-zonas');

            // Solo inicializa si aún no ha sido inicializado
            if (!$slider.hasClass('slick-initialized')) {
                $slider.slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    dots: false,
                    autoplay: !skemaPrefersReducedMotion(),
                    autoplaySpeed: 3000,
                    pauseOnHover: true,
                    pauseOnFocus: true
                });
            } else {
                // Si ya está inicializado, forzar su actualización
                $slider.slick('setPosition');
            }
        }
    });
});

jQuery(document).on('keydown', '[role="tab"]', function (event) {
    var key = event.key;
    if (key !== 'ArrowRight' && key !== 'ArrowLeft' && key !== 'Home' && key !== 'End') {
        return;
    }
    var $tabs = jQuery(this).closest('[role="tablist"]').find('[role="tab"]');
    if (!$tabs.length) {
        return;
    }
    var index = $tabs.index(this);
    var nextIndex = index;
    if (key === 'ArrowRight') {
        nextIndex = (index + 1) % $tabs.length;
    }
    if (key === 'ArrowLeft') {
        nextIndex = (index - 1 + $tabs.length) % $tabs.length;
    }
    if (key === 'Home') {
        nextIndex = 0;
    }
    if (key === 'End') {
        nextIndex = $tabs.length - 1;
    }
    event.preventDefault();
    var nextTab = $tabs.get(nextIndex);
    if (!nextTab) {
        return;
    }
    jQuery(nextTab).tab('show').trigger('focus');
});
