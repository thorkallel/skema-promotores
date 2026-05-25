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

		$wrap.data('plantasSlickReady', true);
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
		if (!isPlantasOrAvanceTabTarget(target)) {
			return;
		}

		var $pane = $(target);
		if (!$pane.length) {
			return;
		}

		var $wrap = $pane.find('.skema-landing-plantas-wrap').first();
		initPlantas($wrap);
	}

	$(document).on('shown.bs.tab', '[data-toggle="tab"]', onTabShown);

	$(function () {
		var $active = $('.tab-pane.active');
		var id = $active.attr('id');
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
