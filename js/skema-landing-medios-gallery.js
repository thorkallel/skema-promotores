/**
 * Galerías landings: modal Bootstrap + carrusel de diapositivas (Apto modelo, etc.).
 */
(function () {
	'use strict';

	function parseSlides(raw) {
		if (!raw || typeof raw !== 'object') {
			return [];
		}
		if (!Array.isArray(raw)) {
			return [];
		}
		return raw.filter(function (item) {
			return item && typeof item.src === 'string' && item.src.length > 0;
		});
	}

	function initOneModal(modalEl) {
		var scriptEl = modalEl.querySelector('script[type="application/json"].skema-landing-gallery-json');
		if (!scriptEl || !scriptEl.textContent) {
			return;
		}
		var slides;
		try {
			slides = JSON.parse(scriptEl.textContent);
		} catch (e) {
			return;
		}
		slides = parseSlides(slides);
		if (slides.length === 0) {
			return;
		}

		var prefix = modalEl.id;
		var imgEl = document.getElementById(prefix + '-img');
		var capEl = document.getElementById(prefix + '-caption');
		var ctrEl = document.getElementById(prefix + '-counter');
		var prevBtn = document.getElementById(prefix + '-prev');
		var nextBtn = document.getElementById(prefix + '-next');
		if (!imgEl || !capEl || !ctrEl || !prevBtn || !nextBtn) {
			return;
		}

		var current = 0;

		function render() {
			var item = slides[current];
			if (!item) {
				return;
			}
			imgEl.src = item.src;
			imgEl.alt = item.alt || '';
			capEl.textContent = item.caption || item.alt || '';
			ctrEl.textContent = current + 1 + ' / ' + slides.length;
			prevBtn.disabled = current <= 0;
			nextBtn.disabled = current >= slides.length - 1;
		}

		function onShowModal(e) {
			var t = e.relatedTarget;
			if (t && t.getAttribute('data-gallery-index') !== null) {
				var idx = parseInt(t.getAttribute('data-gallery-index'), 10);
				if (!isNaN(idx) && idx >= 0 && idx < slides.length) {
					current = idx;
				}
			}
			render();
		}

		/* Bootstrap 4 usa jQuery para disparar show.bs.modal; addEventListener nativo no siempre recibe el evento. */
		if (typeof jQuery !== 'undefined') {
			jQuery(modalEl).on('show.bs.modal', onShowModal);
		} else {
			modalEl.addEventListener('show.bs.modal', onShowModal);
		}

		prevBtn.addEventListener('click', function () {
			if (current > 0) {
				current -= 1;
				render();
			}
		});

		nextBtn.addEventListener('click', function () {
			if (current < slides.length - 1) {
				current += 1;
				render();
			}
		});

		modalEl.addEventListener('keydown', function (ev) {
			if (ev.key === 'ArrowLeft' && current > 0) {
				current -= 1;
				render();
			} else if (ev.key === 'ArrowRight' && current < slides.length - 1) {
				current += 1;
				render();
			}
		});
	}

	function boot() {
		document.querySelectorAll('.skema-landing-gallery-modal').forEach(function (el) {
			initOneModal(el);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
