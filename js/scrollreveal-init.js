document.addEventListener("DOMContentLoaded", function() {
    ScrollReveal().reveal('.reveal', { 
        delay: 500,
        duration: 900,
        distance: '50px'
    });
    ScrollReveal().reveal('.slide-in-left', {
        origin: 'left',
        distance: '100%',
        duration: 900, // Duración en milisegundos
        easing: 'ease-in-out',
        opacity: 0,
        scale: 0.85
    });
    ScrollReveal().reveal('.slide-in-right', {
        origin: 'right',
        distance: '100%',
        duration: 900, // Duración en milisegundos
        easing: 'ease-in-out',
        opacity: 0,
        scale: 0.85
    }, {
        mobile: false // Desactiva ScrollReveal en móviles
    });
    ScrollReveal().reveal('.slide-in-top', {
        origin: 'top',
        distance: '100%',
        duration: 1000, // Duración en milisegundos
        easing: 'ease-in-out',
        opacity: 0,
        scale: 0.85
    });
    ScrollReveal().reveal('.slide-in-bottom', {
        origin: 'bottom',
        distance: '50%',
        duration: 1500, // Duración en milisegundos
        easing: 'ease-in-out',
        opacity: 0,
        scale: 0.7
    });
    
  });
  