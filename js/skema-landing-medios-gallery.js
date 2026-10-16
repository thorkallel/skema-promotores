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

	function preloadImageUrl(url) {
		return new Promise(function (resolve) {
			if (!url) {
				resolve();
				return;
			}
			var loader = new Image();
			loader.onload = loader.onerror = function () {
				resolve();
			};
			loader.src = url;
		});
	}

	function setStageLoading(stageEl, isLoading) {
		if (!stageEl) {
			return;
		}
		if (isLoading) {
			stageEl.classList.add('is-loading-media');
			return;
		}
		stageEl.classList.remove('is-loading-media');
	}

	function setImageLoading(imgEl, isLoading) {
		if (!imgEl) {
			return;
		}
		if (isLoading) {
			imgEl.classList.add('is-loading');
			return;
		}
		imgEl.classList.remove('is-loading');
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
		var stageEl = modalEl.querySelector('.modal-gallery-stage');
		if (!imgEl || !capEl || !ctrEl || !prevBtn || !nextBtn) {
			return;
		}

		var current = 0;
		var renderToken = 0;
		var lastTrigger = null;

		ctrEl.setAttribute('aria-live', 'polite');
		ctrEl.setAttribute('aria-atomic', 'true');
		capEl.setAttribute('aria-live', 'polite');
		capEl.setAttribute('aria-atomic', 'true');

		function preloadAdjacentSlides(index) {
			var indexes = [index - 1, index + 1];
			indexes.forEach(function (idx) {
				if (idx < 0 || idx >= slides.length) {
					return;
				}
				preloadImageUrl(slides[idx].src);
			});
		}

		function render() {
			var item = slides[current];
			if (!item) {
				return;
			}

			var token = ++renderToken;
			setStageLoading(stageEl, true);
			setImageLoading(imgEl, true);
			capEl.textContent = item.caption || item.alt || '';
			ctrEl.textContent = current + 1 + ' / ' + slides.length;
			prevBtn.disabled = current <= 0;
			nextBtn.disabled = current >= slides.length - 1;

			preloadImageUrl(item.src).then(function () {
				if (token !== renderToken) {
					return;
				}
				imgEl.src = item.src;
				imgEl.alt = item.alt || '';
				setImageLoading(imgEl, false);
				setStageLoading(stageEl, false);
				preloadAdjacentSlides(current);
			});
		}

		function onShowModal(e) {
			lastTrigger = (e && e.relatedTarget) || document.activeElement;
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
			jQuery(modalEl).on('hidden.bs.modal', function () {
				if (lastTrigger && typeof lastTrigger.focus === 'function') {
					lastTrigger.focus();
				}
			});
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
