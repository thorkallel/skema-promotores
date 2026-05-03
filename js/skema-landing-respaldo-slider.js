/**
 * Landings: carrusel Slick «Con el respaldo de» (logos + epígrafe).
 * Misma lógica base que Renvia theme.js (.clients-slider): autoplay, sin flechas.
 */
(function ($) {
	'use strict';

	function initRespaldo($slider) {
		if (!$slider.length || typeof $.fn.slick === 'undefined') {
			return;
		}
		if ($slider.hasClass('slick-initialized')) {
			$slider.slick('setPosition');
			return;
		}

		$slider.slick({
			dots: false,
			arrows: false,
			infinite: true,
			speed: 800,
			autoplay: true,
			autoplaySpeed: 4200,
			slidesToShow: 6,
			slidesToScroll: 1,
			responsive: [
				{
					breakpoint: 1450,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 1,
					},
				},
				{
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 1,
					},
				},
				{
					breakpoint: 600,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 1,
					},
				},
				{
					breakpoint: 570,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
					},
				},
			],
		});
	}

	$(function () {
		$('.skema-landing-aliados-slider').each(function () {
			initRespaldo($(this));
		});
	});

	var resizeTimer;
	$(window).on('resize', function () {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(function () {
			$('.skema-landing-aliados-slider.slick-initialized').each(function () {
				$(this).slick('setPosition');
			});
		}, 150);
	});
})(jQuery);
