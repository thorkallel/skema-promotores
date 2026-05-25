# Cabecera landings — variante Renvia (archivada)

Archivos guardados al unificar el header de landings/prelandings con el del resto del sitio (2026-05).

## Contenido

| Archivo | Uso original |
|---------|----------------|
| `skema-landings-header-renvia-desktop.css` | Estilos móvil + desktop xl para `body.skema-landing-shell` y clases `skema-nav-renvia-landings`, `skema-landing-header-area`, etc. |
| `template-parts/_archived/header-landings-renvia-markup.php` | Markup PHP que antes vivía en `header.php` cuando `is_singular( 'landings' )`. |

## Cómo reactivar (solo referencia)

1. Restaurar el CSS en `css/landings/` y volver a encolarlo en `inc/landings-variant.php` (bloque `theme_skema-landings-header-renvia`).
2. Reintegrar el markup desde `header-landings-renvia-markup.php` en `header.php` con `$skema_is_landing_nav = is_singular( 'landings' );`.

No se encola ni se incluye nada de esta carpeta en producción.
