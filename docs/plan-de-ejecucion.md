# Plan de ejecución: SEO, URLs, encabezados y accesibilidad

**Sitio:** Skema Promotora (tema `theme_skema`)
**Fecha:** 2026-10-13
**Insumo:** [auditoria-seo-arquitectura-urls-y-encabezados.md](auditoria-seo-arquitectura-urls-y-encabezados.md)

Los datos de la auditoría salen de la base local (`skema-02.local`). Antes de la fase 3 hay que repetir en producción el inventario de slugs, las páginas duplicadas y las opciones de SEOPress.

Parte del trabajo es código del tema. Otra parte es WordPress (SEOPress, borradores, Contact Form 7, Popup Builder), caché y Search Console.

---

## Decisiones

| ID | Decisión |
|---|---|
| D1 | La landing es la página comercial del proyecto. URL única: `/proyectos/<slug-del-proyecto>/`. `/proyecto/…` y `/landings/…` redirigen ahí. |
| D2 | La fase `prelanding` lleva `noindex` hasta pasar a `landing`. |
| D3 | No hay blog. Archivos de `post`, `category`, autor y fecha en `noindex` y fuera del sitemap. |
| D4 | Sí se publican listados por ciudad y por tipo. |
| D5 | El H1 de inicio sale del campo ACF `home_h1`. Respaldo editable: «Constructora y promotora inmobiliaria en Cali». |
| D6 | Conformidad objetivo: WCAG 2.2 AA. |
| D7 | El vídeo HTML y los iframes de YouTube del hero se tratan como ambientación. Si al implementar uno comunica contenido, ese caso pasa a controles completos. |
| D8 | `#fc7416` sobre blanco da 2,74:1: no pasa ni el 3:1 de texto grande. Todo texto naranja sobre blanco usa `--nara-contrast` (`#B64900`, ≈ 5,3:1). `#fc7416` solo como fondo con texto oscuro o en elementos decorativos. Ver R1. |

Hoy no existe un campo que una una landing con un proyecto. En local hay 17 proyectos y 2 landings: la mayoría de fichas siguen con `single-proyectos.php` hasta que se les vincule una landing.

## Arquitectura objetivo

```
/                                   Inicio
/proyectos/                         Hub (página 213, se mantiene)
/proyectos/<slug-proyecto>/         Ficha. Si hay landing vinculada, muestra ese layout
/proyectos/ciudad/<term>/           Listado por ciudad (fase 4)
/proyectos/tipo/<term>/             Listado por tipo (fase 4)
/sobre-nosotros/  /ofrece-tu-lote/  /contacto/  /politica-privacidad/
```

El dueño de `/proyectos/<slug>/` es el CPT `proyectos`. `has_archive` sigue en false para no pisar la página del hub. Los dos CPT no comparten `rewrite.slug`: WordPress solo resolvería uno. La landing no tiene URL propia.

- Campo ACF nuevo en landings: `skema_landing_proyecto` (un proyecto).
- `single-proyectos.php` incluye el layout de la landing cuando hay una publicada y vinculada.
- `get_permalink()` de esa landing devuelve `/proyectos/<slug-del-proyecto>/`.
- El CPT `landings` queda fuera del sitemap. SEOPress indexa el proyecto.
- Una landing sin proyecto vinculado no se publica. `/landings/1149/` se completa o pasa a borrador en la fase 1.

```
/proyecto/<slug>          --301-->  /proyectos/<slug>
/landings/<slug-landing>  --301-->  /proyectos/<slug-del-proyecto>
        |
        +-- landing en fase "landing"  --> layout comercial, indexable
        +-- landing en prelanding      --> mismo layout, noindex
        +-- sin landing vinculada      --> ficha actual del CPT proyectos
```

## Orden de entrega

| Orden | Fase | Depende de | Riesgo |
|---|---|---|---|
| 1 | 0. Línea base | — | — |
| 2 | 1. Quick wins | 0 para producción; el código de `robots.txt` puede ir en local antes | Bajo |
| 3 | 2 y 2b. Encabezados y accesibilidad, plantilla por plantilla | 0 | Bajo–medio |
| 4 | 3. Migración de permalinks, en un solo despliegue | 1, e inventario de la fase 0 | Alto |
| 5 | 4. Taxonomías públicas | 3 estable | Medio |
| 6 | 5. Migas y schema | 3 | Bajo |
| 7 | 6. Seguimiento 4–6 semanas | 3 | — |

---

## Fase 0. Línea base

Operación en **producción**, no solo en local. Sin esto no se ejecuta la fase 3.

- [ ] Backup con UpdraftPlus.
- [ ] Crawl con Screaming Frog: URLs indexadas, H1/H2 por URL, enlaces internos.
- [ ] Export del rendimiento por URL desde Search Console (Site Kit está activo).
- [x] Inventario de slugs de `proyectos` y `landings`, y pares landing → proyecto. Hecho en local el 2026-10-13. Hay que repetirlo en producción antes del despliegue.
- [x] Confirmar que ningún proyecto se llama `ciudad` ni `tipo`. En local, ninguno. Repetir en producción.

### Inventario local (2026-10-13)

17 proyectos publicados. Ningún slug coincide con una landing. Ninguno se llama `ciudad` ni `tipo`.

| Landing | Estado | Fase | Proyecto vinculado |
|---|---|---|---|
| 592 `tambora` — Tamborá Apartamentos | publicada | `landing` | Proyecto 1152, slug `tambora`, creado en local el 2026-10-13. El menú «Tamborá» apunta a ese proyecto. |
| 1149 | borrador | `prelanding` | Sin título y sin proyecto. Sigue sin publicarse. |

Los otros proyectos no tienen landing: conservan la ficha actual. El proyecto 1152 todavía no tiene ciudad ni tipo, así que no sale en la grilla del hub.

En local, el 2026-10-13, se reemplazaron las URLs `http://skema-02.local/proyecto/` y `http://skema-02.local/landings/tambora`. No se reemplazó el resto de `/landings/` a ciegas. Queda una mención en el popup 1136. Hay que repetir el reemplazo en producción, con `--dry-run` antes.

`cali-puerto-tejada` tiene un solo proyecto. En la fase 4 ese término lleva `noindex`.

## Fase 1. Quick wins, sin cambiar URLs

Hallazgos: P1, P2, P3.

### Código

- [x] En `inc/seo.php` (`theme_skema_robots_txt_sitemap`, líneas 310-321): si `theme_skema_seo_plugin_active()`, devolver `$output` sin añadir `Sitemap: /wp-sitemap.xml`. El `<link rel="sitemap">` y el JSON-LD del tema ya se apagan cuando SEOPress está activo.
- [x] Eliminar `pruebas.php` de la raíz del tema. No lo carga `functions.php`.

### WordPress (local y producción)

- [x] SEOPress → Sitemaps: incluir `proyectos`. Excluir `post`, `category` y `landings`. Hecho en local el 2026-10-13.
- [x] SEOPress → Titles → Archives: `noindex` en entradas, categorías, autor y fecha. Hecho en local el 2026-10-13.
- [x] Pasar a borrador `/inicio/` (página 485, slug `inicio-duplicado`) y dejar un 301 a `/`. Slug de la portada renombrado de `sample-page` a `inicio`. Hecho en local el 2026-10-13.
- [x] Papelera de `/hello-world/` y borrador de `/landings/1149/`. `noindex` de `/category/uncategorized/`. Hecho en local el 2026-10-13.

### Aceptación

- [x] `/robots.txt` tiene una sola línea `Sitemap:` y esa URL responde 200 con XML válido. Comprobado en local el 2026-10-13: `Sitemap: https://skema-02.local/sitemaps.xml`.
- [x] `/sitemaps.xml` lista los proyectos publicados y ninguna URL de ejemplo. Comprobado en local el 2026-10-13: solo `page-sitemap1.xml` y `proyectos-sitemap1.xml`.

## Fase 2. Encabezados

Hallazgos: H1–H19. H20 pasa a la fase 2b (A1). H18 se hace junto con A33.

Reglas: un solo `<h1>` visible por URL; no saltar niveles; datos (ciudad, año, valor, icono) en `<p>` o `<span>` con clase; mayúsculas solo con `text-transform` en CSS.

**Orden obligatorio:** primero pasar los selectores por etiqueta a clase, y después cambiar el HTML. Selectores ya localizados: `style.css` y `css/responsive.css` (`.site-footer h6`), `css/landings/phase-landing.css` y `css/landings/landings-typography.css` (`.project-info-box h5`). Revisar también `.prelanding-copy h1`.

- [x] CSS: selectores por etiqueta → selectores por clase, conservando el aspecto.
- [x] Inicio (`theme_inicio.php`): H1 visible en el hero desde `home_h1` (D5). Iconos `h5` → `p.h5`.
- [x] Tarjetas (`inc/inicio-helpers.php`): tipo y ciudad a `p`/`span`; título del proyecto a `h3`. El nombre de la ficha de servicio pasa a `h3` si la sección tiene `h2`.
- [x] Hub (`theme_proyectos.php`): H2 de sección antes de las tarjetas `h3`.
- [x] Ficha (`single-proyectos.php`): «Proyectos similares» y «Avance de obra» en formato de oración. No imprimir el H2 de ubicación si `txt_ubic` está vacío.
- [x] `theme_sobrenosotros.php`: el H1 sale del contenido del editor si ya existe; si no, `the_title()`. El año de la trayectoria pasa a `time`.
- [x] «Contacto» a `h2` en `theme_ofrecetulote.php`, `theme_contacto.php` y `template-parts/content-fcontacto.php`.
- [x] Footer (`footer.php`): `h6` → `p.h6`.
- [x] Landings (`inc/landings-hero-slider.php`): un H1 visible en el primer slide de escritorio. El hero móvil usa `h2` con la misma clase visual. El lead `h5` pasa a `h2`.
- [x] `template-parts/landings/landing-content-box.php`: secciones principales de `h3` a `h2`; categoría del apartamento a `h3`; «Solicitar información» a `h2`.
- [x] Título del modal de la galería: `h2.modal-title.h5`.

### Aceptación

- [ ] Mismo aspecto en móvil y escritorio: inicio, hub, una ficha, una landing `prelanding` y una `landing` (variante default y promo).
- [x] Un solo H1 en el HTML de inicio, `/proyectos/`, `/sobre-nosotros/`, una ficha, `/landings/tambora/` y `/ofrece-tu-lote/`. Comprobado en local el 2026-10-13. No hay página publicada con la plantilla de contacto.

## Fase 2b. Accesibilidad (WCAG 2.2 AA)

Mismo ciclo que la fase 2, porque comparten plantillas. Orden interno: atributos y alt, después teclado, después movimiento, al final contraste y formularios.

No reescribir lo que ya está bien: `language_attributes()`, skip link a `#primary`, `<main id="primary">`, `aria-controls` del menú, `aria-label` de redes y WhatsApp, `<label>` de los `<select>`, `title` de los iframes, teclado del lightbox y flechas de slider en `<button>`.

Regla: un `<button>` o `<nav>` no lleva `role` si el HTML nativo ya lo dice. Todo estado que cambie con JavaScript se refleja en ARIA.

### Atributos, alt y enlaces

- [x] A1. `header.php` (líneas 38 y 53): `aria-current="page"` en el logo solo si `is_front_page()`.
- [x] A2. Quitar `role="navigation"` del `<nav>` y añadir `aria-label="Menú principal"`. Si el footer tiene otro `<nav>`, otro nombre.
- [x] A4. Quitar `aria-expanded` del botón cerrar. Solo lo lleva el botón que abre.
- [x] A5. `aria-hidden="true"` en los iconos de WhatsApp y teléfono.
- [x] A6. El enlace de WhatsApp avisa que abre una pestaña nueva.
- [x] A21. `theme_sobrenosotros.php`: sustituir `alt="Proyecto"` por el nombre del término (las imágenes llegan como URL, sin ID de adjunto).
- [x] A22. Slider de oportunidades en inicio: alt del adjunto, no `alt=""`.
- [x] A23. Iconos de inicio cuyo alt repite el texto de al lado: `alt=""`.
- [x] A24. Logo de Skema en la tarjeta: se mantiene `alt=""`.
- [x] A25. Logo del proyecto en la ficha de servicio: `alt="Logo de <proyecto>"`, salvo que el nombre ya esté al lado como texto.
- [x] A26. Banners de contacto y del hub: `alt=""` si el H1 ya está encima.
- [x] A27. Logo del hero de landing: no repetir el texto del H1.
- [ ] A28. Campos ACF de imagen que devuelven solo URL: cambiar el retorno a array o ID y usar `wp_get_attachment_image()`. Revisar cada plantilla que hoy espera un string, para no romper el `src`. Pendiente: el cambio de formato rompería las plantillas que imprimen la URL como texto.
- [x] A29. `footer.php`: `rel="noopener noreferrer"`, aviso de pestaña nueva, y no imprimir el enlace si el texto ACF está vacío.
- [x] A30. CTA del brochure: texto accesible «(PDF, abre en nueva pestaña)».
- [x] A31. `aria-label="Ver proyecto %s"` también en `inc/inicio-helpers.php`.
- [x] A33 + H18. Características del proyecto en `<dl>`: `<dt>` con el nombre y `<dd>` con el valor (`inc/landings-project-characteristics.php`).

### Foco y teclado

- [x] A3. Menú móvil cerrado: `inert` (o `visibility: hidden`) en `#menuPrincipal`. Al abrir, el foco va al botón cerrar. `Esc` cierra. Al cerrar, el foco vuelve a `.btn-abrir`. `aria-expanded` sigue al estado real (`js/main.js`, `header.php`).
- [x] A7. Sustituir `outline: none` en `css/landings/phase-prelanding.css`, `css/landings/phase-landing.css` y los botones de Slick por `:focus-visible` con contorno de contraste ≥ 3:1.
- [x] A8. `scroll-padding-top` en `html` igual a la altura del header fijo.
- [x] A16. Pestañas de `single-proyectos.php`: `<button type="button">`, `<li role="presentation">`, IDs únicos y el texto «Características».
- [x] A17. Pestañas de medios en `landing-content-box.php`: `aria-controls` apunta al panel, cada `tabpanel` tiene `aria-labelledby`, y las flechas mueven la pestaña activa.
- [x] A18. `#selectMapas`: el contenedor que cambia lleva `aria-live="polite"`.
- [x] A19. `#selectContact`: igual. Teléfono → `<a href="tel:">`. Correo → `<a href="mailto:">`.
- [x] A20. Modal de la galería: `aria-labelledby` ligado al título, botón cerrar con `aria-label="Cerrar"`, y el foco vuelve al disparador.

### Movimiento

- [x] A9. `prefers-reduced-motion: reduce` apaga scrollreveal, animaciones `slide-in-top` / `reveal` y el autoplay.
- [x] A10. Botón Pausar/Reproducir en los sliders con autoplay (`js/main.js`, `js/skema-landing-respaldo-slider.js`). Activar `pauseOnFocus` y `pauseOnHover`. Sin autoplay si el usuario pide menos movimiento.
- [x] A11. La región `aria-live` solo anuncia cambios iniciados por el usuario. Con autoplay queda en `off`.
- [x] A12. Cada carrusel: `role="region"`, `aria-roledescription="carrusel"` y `aria-label` propio. Cada slide: `role="group"` y etiqueta «3 de 8». Los clones `.slick-cloned` con `aria-hidden` y sin tabindex en sus enlaces.
- [x] A13. Miniaturas de `medios-plantas-slider.php`: `<button>` con `aria-label` y `aria-current` en la activa.
- [x] A14. Vídeo de fondo en `theme_inicio.php` e `inc/inicio-hero-banner.php`: `aria-hidden="true"`, texto de respaldo en español traducible, botón de pausa visible. Sin autoplay con `prefers-reduced-motion`.
- [x] A15. Iframes de YouTube del hero (`controls=0&disablekb=1`): `tabindex="-1"`, `aria-hidden="true"` y pausa con la API (`enablejsapi=1` ya está). Archivos: `theme_inicio.php`, `inc/inicio-hero-banner.php`, `inc/landings-hero-slider.php`.

### Contraste, formularios y popups

- [x] A34. Token `--nara-contrast` (`#B64900`, contraste ≈ 5.3:1 sobre blanco) en **todo** texto naranja sobre fondo claro, titulares incluidos (D8). `#fc7416` queda solo como fondo, con texto oscuro, o en elementos decorativos. El H1 del inicio lleva un fondo `#0b1f42` al 82 %. Los botones con texto blanco usan el naranja de marca como fondo. El detalle de contraste que quedaba abierto se cierra en R1.
- [x] A32. En Contact Form 7 (contacto, ofrece tu lote y landings), en local: cada campo tiene `<label>` visible y los obligatorios dicen «(obligatorio)». Hay que repetir el mismo marcado en producción. Los errores de CF7 5 se asocian al campo cuando existe la etiqueta.
- [x] A35. Popup Builder cierra con Escape solo si el popup tiene activa la opción de cerrar con Escape. No atrapa el foco ni lo devuelve al disparador. No se parchea desde el tema.

### Aceptación

- [ ] Lighthouse Accessibility ≥ 95 y 0 errores críticos o graves de axe en: home, `/proyectos/`, una ficha, una landing de cada fase y `/contacto/`.
- [ ] Recorrido solo con teclado (Tab, Shift+Tab, Enter, Esc, flechas) en menú móvil, pestañas, sliders, lightbox, selects y formularios. El foco siempre visible y nunca dentro de un panel cerrado.
- [ ] Con `prefers-reduced-motion: reduce` no hay autoplay ni animaciones de entrada.

## Correcciones tras la revisión del 2026-10-13

Salen de revisar en local lo ya entregado de las fases 1, 2, 2b y 3. R1, R2 y R5 harían fallar la aceptación de axe de la fase 2b: van antes de pasarla.

### Accesibilidad

- [ ] **R1. Contraste del naranja (alta).** `#fc7416` sobre blanco da 2,74:1, por debajo de 4.5:1 (texto normal) y de 3:1 (texto grande, WCAG 1.4.3). Lo que dice A34, que «los titulares grandes conservan la marca», no cumple AA.
  - Buscar todos los `color` con `#fc7416` / `var(--nara)` sobre fondo claro en `style.css`, `css/responsive.css`, `css/home/*.css` y `css/landings/*.css`, y pasarlos a `var(--nara-contrast)`.
  - `#fc7416` se queda solo como fondo (con texto `#0b1f42`) o en elementos decorativos sin texto.
  - Aceptación: axe sin errores `color-contrast` en las páginas de la fase 2b.
- [ ] **R2. Puntos del hero dentro de `aria-hidden` (alta).** `<div class="home-hero-dots" aria-hidden="true">` en `theme_inicio.php:62,136`, `inc/inicio-hero-banner.php:78,210` e `inc/landings-hero-slider.php:164,189,228,740,775,827`. Slick añade ahí los `<button>` de los puntos (`js/main.js:342-381`): con más de un slide quedan controles enfocables dentro de `aria-hidden` (axe: `aria-hidden-focus`, crítico).
  - Quitar `aria-hidden="true"` del contenedor.
  - Dar a cada punto un `aria-label` («Ir a la diapositiva 2 de 4») con `customPaging` de Slick, y `aria-current="true"` al activo.
  - Aceptación: con dos o más slides en el hero, axe sin `aria-hidden-focus` y los puntos se pueden usar con el teclado.
- [ ] **R4. Anuncios repetidos en el footer (media).** `footer.php` tiene tres regiones `aria-live` (`#contacto-anuncio`, `#contacto-telefonos`, `#contacto-direcciones`); un cambio de ciudad se anuncia varias veces.
  - Dejar solo `#contacto-anuncio` y quitar `aria-live` de `#contacto-telefonos` y `#contacto-direcciones`.
  - Revisar que `#mapas-anuncio` (inicio) siga el mismo patrón: una sola región.
  - Aceptación: con VoiceOver, al cambiar de ciudad se oye un solo «Mostrando: <ciudad>».
- [ ] **R5. `alt` genéricos que quedan (media).**
  - `theme_sobrenosotros.php:16` `alt="Banner"` → `alt=""` (el H1 está encima).
  - `theme_sobrenosotros.php:52,210` `alt="Icono"` → `alt=""` si hay texto al lado; si no, la descripción del icono.
  - `theme_inicio.php:239,243`: los logos usan `the_title_attribute()` y en la portada salen con `alt="Inicio"`. Usar el nombre de la marca o del proyecto (campo ACF o `get_bloginfo( 'name' )`).
  - Aceptación: ningún `alt` con «Banner», «Icono», «Inicio» o «Proyecto» en el HTML de inicio y de `/sobre-nosotros/`.
- [ ] **R7. Foco con el menú móvil abierto (baja).** Con el menú abierto, Tab sale del panel hacia la página que queda detrás.
  - En `skemaSetMobileMenuOpen()` (`js/main.js`), al abrir: `inert` en `#primary`, el footer y `.bnts_wa_tel`; al cerrar, quitarlo.
  - Aceptación: con el menú abierto, Tab y Shift+Tab solo recorren el panel.

### SEO y URLs

- [ ] **R3. URL duplicada de la landing (media).** `/?landings=tambora` responde 200 con la landing completa (el canonical apunta al proyecto, pero la URL se puede rastrear).
  - En `inc/cpt-proyectos.php`, en `template_redirect`: si `is_singular( 'landings' )` y `theme_skema_landing_project_id()` devuelve un proyecto publicado, 301 a `get_permalink( $project_id )`. Sin proyecto vinculado: 404.
  - Aceptación: `/?landings=tambora` → 301 → `/proyectos/tambora/`, en un solo salto.
- [ ] **R6. H1 de «Sobre nosotros» (baja).** Hoy el H1 sale del editor: «POR MÁS DE 18 AÑOS hemos desarrollado proyectos innovadores…». Es largo, va en mayúsculas en el HTML y no tiene palabra clave.
  - H1 corto desde `the_title()` o un campo ACF (por ejemplo «Sobre Skema Promotora»). La frase pasa a párrafo de entrada, con las mayúsculas en CSS (`text-transform`).
  - Aceptación: un solo H1 de menos de 70 caracteres en `/sobre-nosotros/`.
- [ ] **R8. Landing publicada sin proyecto (baja).** El plan dice que una landing sin proyecto no se publica, pero el código no lo impide.
  - En `inc/cpt-proyectos.php`, con `wp_insert_post_data` (o `acf/validate_value` del campo `skema_landing_proyecto`): si el estado es `publish` y no hay proyecto, guardar como `draft` y mostrar un aviso en el admin.
  - Aceptación: al publicar una landing sin proyecto, queda en borrador y aparece el aviso.

### Pendientes menores

- [ ] El popup 1136 todavía enlaza a `/landings/`. Funciona por el 301, pero hay que cambiarlo por `/proyectos/tambora/`.
- [ ] `/contacto/` responde 404 aunque está en la arquitectura objetivo. Decidir si se crea la página con `theme_contacto.php` o se quita de la arquitectura y de los menús.
- [ ] Probar el `noindex` de la prelanding con una landing de prueba vinculada a un proyecto en fase `prelanding`, y después volver a dejarla en borrador.
- [ ] Commitear por fases antes de desplegar: hoy los cambios de las fases 1–3 están sin commit, mezclados con cambios anteriores de la rama.

## Fase 3. Migración de permalinks

Hallazgos: P4, más D1 y D2. Riesgo alto. El cambio de slug y los 301 salen en el mismo despliegue. Hecho en local el 2026-10-13. Producción sigue bloqueada hasta el backup, el crawl y Search Console.

- [x] Crear `inc/cpt-proyectos.php` y requerirlo desde `functions.php`, con las mismas keys `proyectos`, `tipo_proyecto`, `ciudad_proyecto` y `estado_proyecto`. Rewrite del CPT: `slug` `proyectos`, `with_front` false, `has_archive` false. Las tres taxonomías siguen privadas hasta la fase 4.
- [x] En el mismo despliegue, CPT UI deja de registrar esas keys (`cptui_disable_*`). La opción en la base se conserva por si hay que volver atrás. No pueden convivir: `register_post_type` con una key ya registrada falla.
- [x] En `inc/cpt-landings.php`, `rewrite` false. Campo ACF `skema_landing_proyecto`. Filtro `post_type_link` hacia la URL del proyecto.
- [x] En `single-proyectos.php`, si el proyecto tiene landing publicada, renderizar el layout comercial (variante y fase actuales). Si la fase es `prelanding`, emitir `noindex`. Si no hay landing, mantener la ficha actual.
- [x] Redirecciones de un solo salto, versionadas en el tema:
  - `^/proyecto/(.+)$` → `/proyectos/$1` solo si ese proyecto está publicado
  - `^/landings/(.+)$` → `/proyectos/<slug-del-proyecto-vinculado>/`
  - `/inicio/` → `/`
- [x] Las reglas se regeneran una vez (`theme_skema_rewrite_version` = `proyectos-slug-1`). En producción hay que guardar Enlaces permanentes en el mismo despliegue.
- [x] Search-replace local, después de `--dry-run`: `http://skema-02.local/proyecto/` → `/proyectos/` y solo la URL de Tamborá en `/landings/tambora`. El menú apunta al proyecto 1152. Repetir en producción.
- [x] Caché de WP Rocket purgada en local. Falta reenviar el sitemap en Search Console cuando esto salga a producción.

### Aceptación

- [x] En local: `/proyecto/aureal/` y `/landings/tambora/` responden 301 y la URL nueva responde 200. `/proyecto/no-existe/` y `/landings/1149/` responden 404.
- [x] Un solo salto. Comprobado en local.
- [x] El canonical de Aureal, Tamborá y el hub apunta a `/proyectos/<slug>/`.
- [ ] No quedan enlaces internos a `/proyecto/` ni a `/landings/`. En local ya no hay `/proyecto/` en el contenido. Queda `/landings/` en el popup 1136.
- [x] Tamborá (`landing`) muestra el layout comercial, con canonical indexable, en `/proyectos/tambora/`. La pestaña Plantas se activa. Aureal, sin landing, conserva la ficha. No hay una prelanding publicada para ver el `noindex` en una URL real.
- [x] El sitemap de proyectos lista `/proyectos/<slug>/` (18 URLs, incluida Tamborá) y ninguna `/proyecto/`. Caché purgada en local.

## Fase 4. Ciudad y tipo

Hallazgo: P5. Solo después de que la fase 3 esté estable. Requiere texto de descripción por término.

- [ ] Hacer públicas solo `ciudad_proyecto` y `tipo_proyecto`, con rewrite `/proyectos/ciudad/<term>/` y `/proyectos/tipo/<term>/`. Esas reglas se registran antes que `proyectos/([^/]+)/`.
- [ ] `estado_proyecto` sigue privado.
- [ ] Crear `taxonomy-ciudad_proyecto.php` y `taxonomy-tipo_proyecto.php` reutilizando la grilla de `theme_proyectos.php`, con H1 propio y descripción del término.
- [ ] `noindex` en términos con menos de 2 proyectos.
- [ ] Limpiar el término duplicado `cali-puerto-tejada`.
- [ ] Las tarjetas enlazan al permalink del proyecto.
- [ ] Incluir esas taxonomías en el sitemap de SEOPress.

### Aceptación

- [ ] `/proyectos/ciudad/cali/` responde 200 con H1 propio y enlaces a fichas.
- [ ] Un término con un solo proyecto lleva `noindex`.
- [ ] Ningún proyecto usa el slug `ciudad` o `tipo`.

## Fase 5. Migas y schema

Hallazgos: P6, P7.

- [ ] Imprimir `seopress_display_breadcrumbs()` (con `function_exists`) en la ficha de proyecto, en las plantillas de taxonomía y en las páginas internas. Jerarquía: Inicio › Proyectos › ciudad › proyecto. En una ficha con landing, las migas usan los términos del proyecto.
- [ ] Comprobar que SEOPress Local Business no duplica un `Organization` / `RealEstateAgent`. El tema ya no emite el suyo si el plugin está activo; no reescribir `inc/seo.php` salvo el arreglo de la fase 1.
- [ ] Validar con Rich Results Test una ficha, una taxonomía y la portada.

## Fase 6. Seguimiento

Durante 4 a 6 semanas en Search Console, sin cambios de código:

- [ ] Cobertura e indexación de `/proyectos/<slug>/`.
- [ ] Caída de las URLs antiguas `/proyecto/` y `/landings/`.
- [ ] 404 nuevos después de la migración.
