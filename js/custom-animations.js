jQuery(document).ready(function($) {
    // Inicialización del controlador de ScrollMagic
var controller = new ScrollMagic.Controller();

// Hacerlo accesible globalmente si fuera necesario
window.site = window.site || {}; // Asegura que el objeto `site` esté definido
window.site.f = { smController: controller }; // Almacena el controlador

    // Asegúrate de que GSAP y ScrollMagic están cargados
    if (typeof ScrollMagic !== 'undefined' && typeof gsap !== 'undefined') {
        // Seleccionar el elemento
        var t = $(".img-mapa");

        var smController = new ScrollMagic.Controller();
        // Crear una nueva timeline con GSAP 3
        let tl = gsap.timeline();

        // Configurar la animación inicial
        tl.set(t, {
            y: 100,
            autoAlpha: 1
        })
        .to(t, {
            duration: 2,
            autoAlpha: 1
        })
        .to(t, {
            duration: 2,
            y: 0,
            onComplete: function() {
                // Configuraciones adicionales al completar la animación
                t.css({
                    transition: "1.5s"
                });
                // Crear una nueva escena de ScrollMagic
                var scene = new ScrollMagic.Scene({
                    triggerElement: t[0], // Necesitas pasar un elemento DOM aquí, no un objeto jQuery
                    triggerHook: 0.1,
                    duration: "100%"
                })
                .setTween(gsap.fromTo(t, {
                    y: 0
                }, {
                    duration: 1, // Define la duración dentro del objeto de animación en GSAP 3
                    y: 80
                }))
                .addTo(window.site.f.smController); // Asegúrate de que este controlador esté definido
            }
        });
    } else {
        console.log("GSAP or ScrollMagic is not defined.");
    }
});
