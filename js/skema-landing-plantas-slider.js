/**
 * Landings: Slick en pestañas Plantas y Avance de obra (slider + miniaturas sincronizadas).
 * Bootstrap 4: shown.bs.tab al mostrar el panel.
 */
(function ($) {
	'use strict';

	function escapeAttr(value) {
		if (value === null || value === undefined) {
			return '';
		}
		return String(value)
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/</g, '&lt;');
	}

	function getI18n() {
		var pack = typeof window.skemaLandingPlantasI18n === 'object' && window.skemaLandingPlantasI18n
			? window.skemaLandingPlantasI18n
			: {};
		return {
			prevMain: pack.prevMain || 'Planta anterior',
			nextMain: pack.nextMain || 'Planta siguiente',
			prevNav: pack.prevNav || 'Miniaturas anteriores',
			nextNav: pack.nextNav || 'Miniaturas siguientes',
		};
	}

	function isImageLoaded(img) {
		return img.complete && img.naturalWidth > 0;
	}

	function markImageLoadingState(img) {
		if (!img || img.tagName !== 'IMG') {
			return;
		}

		var $inner = $(img).closest('.plantas-slide-inner');
		if (isImageLoaded(img)) {
			img.classList.remove('is-loading');
			img.classList.add('is-loaded');
			$inner.removeClass('is-loading-media');
			return;
		}

		img.classList.add('is-loading');
		$inner.addClass('is-loading-media');

		$(img)
			.one('load.skemaMedios error.skemaMedios', function () {
				img.classList.remove('is-loading');
				img.classList.add('is-loaded');
				$inner.removeClass('is-loading-media');
			});
	}

	function preloadImageUrl(url) {
		if (!url) {
			return $.Deferred().resolve().promise();
		}

		var deferred = $.Deferred();
		var loader = new Image();
		loader.onload = loader.onerror = function () {
			deferred.resolve();
		};
		loader.src = url;
		return deferred.promise();
	}

	function collectMainSlideImageUrls($wrap) {
		var urls = [];
		$wrap.find('.plantas-slider-main .plantas-slide img[src]').each(function () {
			var src = this.getAttribute('src');
			if (src && urls.indexOf(src) === -1) {
				urls.push(src);
			}
		});
		return urls;
	}

	function preloadWrapMainImages($wrap) {
		var urls = collectMainSlideImageUrls($wrap);
		if (urls.length === 0) {
			return $.Deferred().resolve().promise();
		}

		var tasks = urls.map(function (url) {
			return preloadImageUrl(url);
		});
		return $.when.apply($, tasks);
	}

	function preloadPaneImages($pane) {
		if (!$pane || !$pane.length) {
			return;
		}

		$pane.find('img[src]').each(function () {
			if (isImageLoaded(this)) {
				return;
			}
			preloadImageUrl(this.getAttribute('src'));
		});
	}

	function bindMainImageLoadHandlers($wrap) {
		var $main = $wrap.find('.plantas-slider-main');
		if (!$main.length) {
			return;
		}

		$main.find('img.skema-landing-medios-img').each(function () {
			markImageLoadingState(this);
		});

		$main.off('load.skemaMedios error.skemaMedios', 'img.skema-landing-medios-img');
		$main.on('load.skemaMedios error.skemaMedios', 'img.skema-landing-medios-img', function () {
			var $mainSlider = $wrap.find('.plantas-slider-main');
			if (!$mainSlider.hasClass('slick-initialized')) {
				return;
			}
			$mainSlider.slick('setPosition');
		});
	}

	function preloadAdjacentSlides($main, currentIndex) {
		if (!$main.hasClass('slick-initialized')) {
			return;
		}

		var slideCount = $main.slick('getSlick').slideCount;
		var indexes = [currentIndex - 1, currentIndex + 1];

		indexes.forEach(function (idx) {
			if (idx < 0 || idx >= slideCount) {
				return;
			}
			var $slide = $main.find('.slick-slide[data-slick-index="' + idx + '"]');
			var img = $slide.find('img.skema-landing-medios-img')[0];
			if (!img) {
				return;
			}
			markImageLoadingState(img);
			if (!isImageLoaded(img)) {
				preloadImageUrl(img.getAttribute('src'));
			}
		});
	}

	function initPlantas($wrap) {
		if (!$wrap || !$wrap.length) {
			return;
		}

		var $main = $wrap.find('.plantas-slider-main');
		var $nav = $wrap.find('.plantas-slider-nav');
		if (!$main.length || !$nav.length) {
			return;
		}

		if (typeof $.fn.slick === 'undefined') {
			return;
		}

		var mainId = $main.attr('id');
		var navId = $nav.attr('id');
		if (!mainId || !navId) {
			return;
		}

		var mainSel = '#' + mainId;
		var navSel = '#' + navId;

		bindMainImageLoadHandlers($wrap);

		if ($wrap.data('plantasSlickReady')) {
			if ($main.hasClass('slick-initialized')) {
				$main.slick('setPosition');
			}
			if ($nav.hasClass('slick-initialized')) {
				$nav.slick('setPosition');
			}
			return;
		}

		if ($main.hasClass('slick-initialized') || $nav.hasClass('slick-initialized')) {
			return;
		}

		var labels = getI18n();
		var prevMain =
			'<button type="button" class="slick-prev plantas-slick-btn plantas-slick-btn--main plantas-slick-btn--prev" aria-label="' +
			escapeAttr(labels.prevMain) +
			'"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>';
		var nextMain =
			'<button type="button" class="slick-next plantas-slick-btn plantas-slick-btn--main plantas-slick-btn--next" aria-label="' +
			escapeAttr(labels.nextMain) +
			'"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>';
		var prevNav =
			'<button type="button" class="slick-prev plantas-slick-btn plantas-slick-btn--nav plantas-slick-btn--prev" aria-label="' +
			escapeAttr(labels.prevNav) +
			'"><i class="fas fa-angle-left" aria-hidden="true"></i></button>';
		var nextNav =
			'<button type="button" class="slick-next plantas-slick-btn plantas-slick-btn--nav plantas-slick-btn--next" aria-label="' +
			escapeAttr(labels.nextNav) +
			'"><i class="fas fa-angle-right" aria-hidden="true"></i></button>';

		preloadWrapMainImages($wrap).always(function () {
			if ($wrap.data('plantasSlickReady') || $main.hasClass('slick-initialized')) {
				return;
			}

			$main.slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: true,
				infinite: false,
				speed: 450,
				adaptiveHeight: true,
				swipe: true,
				asNavFor: navSel,
				prevArrow: prevMain,
				nextArrow: nextMain,
			});

			$nav.slick({
				slidesToShow: 5,
				slidesToScroll: 1,
				asNavFor: mainSel,
				dots: false,
				arrows: true,
				infinite: false,
				focusOnSelect: true,
				speed: 450,
				prevArrow: prevNav,
				nextArrow: nextNav,
				responsive: [
					{
						breakpoint: 1200,
						settings: { slidesToShow: 4, slidesToScroll: 1 },
					},
					{
						breakpoint: 768,
						settings: { slidesToShow: 3, slidesToScroll: 1 },
					},
					{
						breakpoint: 480,
						settings: { slidesToShow: 2, slidesToScroll: 1 },
					},
				],
			});

			$main.on('beforeChange.skemaMedios', function (_event, _slick, _current, next) {
				preloadAdjacentSlides($main, next);
			});

			function syncPlantasCurrent() {
				if (!$main.hasClass('slick-initialized')) {
					return;
				}
				var current = $main.slick('slickCurrentSlide');
				$nav.find('.plantas-nav-thumb').each(function () {
					var slide = this.closest('.slick-slide');
					if (!slide || slide.classList.contains('slick-cloned')) {
						return;
					}
					var index = parseInt(slide.getAttribute('data-slick-index'), 10);
					if (index === current) {
						this.setAttribute('aria-current', 'true');
						return;
					}
					this.removeAttribute('aria-current');
				});
			}

			$main.on('afterChange.skemaPlantasCurrent', syncPlantasCurrent);
			window.setTimeout(syncPlantasCurrent, 0);

			$wrap.data('plantasSlickReady', true);
			$main.slick('setPosition');
			preloadAdjacentSlides($main, 0);
		});
	}

	function isPlantasOrAvanceTabTarget(target) {
		if (!target) {
			return false;
		}
		return target.indexOf('-tab-plantas') !== -1 || target.indexOf('-tab-avance-obra') !== -1;
	}

	function onTabShown(e) {
		var $trigger = $(e.target);
		var target = $trigger.attr('data-target') || $trigger.attr('href');
		if (!target) {
			return;
		}

		var $pane = $(target);
		if (!$pane.length) {
			return;
		}

		preloadPaneImages($pane);

		if (!isPlantasOrAvanceTabTarget(target)) {
			return;
		}

		var $wrap = $pane.find('.skema-landing-plantas-wrap').first();
		initPlantas($wrap);
	}

	$(document).on('shown.bs.tab', '.property-media-box [data-toggle="tab"]', onTabShown);

	$(function () {
		var $active = $('.property-media-box .tab-pane.active');
		var id = $active.attr('id');
		if (id) {
			preloadPaneImages($active);
		}
		if (id && isPlantasOrAvanceTabTarget(id)) {
			initPlantas($active.find('.skema-landing-plantas-wrap').first());
		}
	});

	var resizeTimer;
	$(window).on('resize', function () {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(function () {
			$('.skema-landing-plantas-wrap').each(function () {
				var $w = $(this);
				if (!$w.data('plantasSlickReady')) {
					return;
				}
				var $m = $w.find('.plantas-slider-main');
				var $n = $w.find('.plantas-slider-nav');
				if ($m.hasClass('slick-initialized')) {
					$m.slick('setPosition');
				}
				if ($n.hasClass('slick-initialized')) {
					$n.slick('setPosition');
				}
			});
		}, 150);
	});
})(jQuery);
