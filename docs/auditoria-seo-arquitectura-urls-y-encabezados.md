# Análisis SEO: permalinks, arquitectura de URLs, jerarquía de encabezados y accesibilidad

**Sitio:** Skema Promotora (tema `theme_skema`)
**Fecha del análisis:** 2026-10-13
**Fuente de los datos:** código del tema + base de datos local (`skema-02.local`). **Antes de ejecutar nada, verifica estos datos en producción**, porque pueden ser distintos.

> Este documento sirve de insumo para generar un plan de trabajo ejecutable. Cada hallazgo indica la evidencia (archivo:línea u opción de BD), la acción propuesta y el criterio de aceptación.

---

## 0. Contexto técnico relevante

| Elemento | Estado actual |
|---|---|
| Estructura de permalinks | `/%postname%/` (correcta, se mantiene) |
| Plugin SEO activo | **SEOPress** (titles, sitemap, social, schema/local business, breadcrumbs, robots: todo activado) |
| SEO propio del tema | `inc/seo.php`: meta, OG, canonical y JSON-LD **se desactivan solos** si SEOPress está activo (`theme_skema_seo_plugin_active()`). El filtro `robots_txt` **sigue ejecutándose siempre**. |
| CPT `proyectos` | Registrado con **CPT UI** (en BD, no en código). `rewrite_slug = proyecto`, `has_archive = false`, sin taxonomías asociadas en el registro (sí asociadas desde la taxonomía). 17 publicados. |
| CPT `landings` | Registrado en código: `inc/cpt-landings.php:39` → `rewrite slug = landings`, `has_archive = false`. 2 publicados. |
| Taxonomías (CPT UI) | `tipo_proyecto` (Residenciales 11, Institucionales 3, Comerciales 2, Inversión 1), `ciudad_proyecto` (Cali 10, Bogotá 3, …), `estado_proyecto` (Terminado 13, Ejecución 3, Comercialización 1). **Las tres tienen `publicly_queryable = false`**, así que no tienen URL pública. |
| Página hub de proyectos | Página `proyectos` (ID 213, plantilla `theme_proyectos.php`) en `/proyectos/` |
| Portada | Página ID 2, slug `sample-page`, título "Inicio", plantilla `theme_inicio.php` |
| Cache | WP Rocket + LiteSpeed (hay que purgar después de cambiar URLs) |

### Arquitectura de URLs actual (resultado)
```
/                         → Inicio (page 2, slug "sample-page")
/inicio/                  → DUPLICADO de la portada (page 485, misma plantilla)
/proyectos/               → Página hub "Nuestros Proyectos"
/proyecto/<slug>/         → Ficha de proyecto (CPT)      ← singular ≠ plural del hub
/landings/<slug>/         → Landing comercial (CPT)      ← sin relación semántica con /proyectos/
/landings/1149/           → Landing SIN TÍTULO publicada
/sobre-nosotros/  /ofrece-tu-lote/  /politica-privacidad/
/hello-world/             → Post de ejemplo publicado
/category/uncategorized/  → Categoría vacía de valor, incluida en el sitemap
```

---

## 1. Hallazgos: indexación y permalinks

### P1. CRÍTICO: el sitemap de SEOPress no incluye `proyectos` ni `landings`
- **Evidencia:** opción `seopress_xml_sitemap_option_name` → `post_types_list` solo tiene `post` y `page`; `taxonomies_list` solo tiene `category`.
- **Impacto:** las páginas con más valor comercial (17 proyectos y las landings) no están en el sitemap, mientras que `hello-world` y `uncategorized` sí lo están.
- **Acción:** en SEOPress → Sitemaps, incluir `proyectos` y `landings`; excluir `post` (si no habrá blog) y `category`. Si se publican taxonomías (P5), incluirlas.
- **Aceptación:** `/sitemaps.xml` lista todos los proyectos y landings publicados y ninguna URL de ejemplo.

### P2. CRÍTICO: `robots.txt` apunta a un sitemap que no es el que se usa
- **Evidencia:** `inc/seo.php:310-321` añade `Sitemap: /wp-sitemap.xml` sin comprobar si hay un plugin SEO activo. SEOPress desactiva el sitemap del core y publica `/sitemaps.xml`. También `theme_skema_head_sitemap_link()` (línea 326) apunta a `wp-sitemap.xml`, aunque esa sí se desactiva con el plugin.
- **Acción:** en `theme_skema_robots_txt_sitemap()` hacer `return $output` si `theme_skema_seo_plugin_active()`, o apuntar a `/sitemaps.xml`.
- **Aceptación:** `robots.txt` contiene una sola línea `Sitemap:` y esa URL responde 200 con XML válido.

### P3. ALTO: contenido duplicado y URLs basura
| URL | Problema | Acción |
|---|---|---|
| `/inicio/` (page 485) | Misma plantilla que la portada, contenido duplicado | Pasar a borrador/eliminar + 301 a `/` |
| `/landings/1149/` | Landing sin título, slug numérico | Completar o pasar a borrador; si estaba indexada, 301 |
| `/hello-world/` + `/category/uncategorized/` | Contenido de ejemplo | Eliminar el post; noindex o redirección de la categoría |
| Slug de la portada `sample-page` | Slug poco profesional (WP lo redirige a `/`, pero queda en el admin y en algunas exportaciones) | Renombrar el slug a `inicio` **después** de eliminar la page 485 |
| `pruebas.php` en la raíz del tema | Archivo de pruebas con encabezados (h2/h3/h4) | Eliminarlo del tema |

### P4. ALTO: jerarquía de URLs incoherente entre hub, proyectos y landings
- **Problema:** el hub está en `/proyectos/`, pero las fichas cuelgan de `/proyecto/…` (singular) y las landings de `/landings/…`. Google no ve una relación padre-hijo, las migas de pan no tienen sentido y la palabra "landings" es jerga interna sin valor de búsqueda.
- **Propuesta de arquitectura objetivo:**
```
/                                   Inicio
/proyectos/                         Hub (página 213, se mantiene como página)
/proyectos/<slug-proyecto>/         Ficha de proyecto (CPT proyectos)
/proyectos/<ciudad>/  (opcional, P5) Listado por ciudad
/<prefijo-landing>/<slug>/          Landings → ver decisión D1
/sobre-nosotros/  /ofrece-tu-lote/  /contacto/  /politica-privacidad/
```
- **Cambios:**
  1. CPT UI → `proyectos` → `rewrite_slug`: `proyecto` → `proyectos`; `with_front`: false. Mantener `has_archive = false` para que `/proyectos/` siga sirviendo la página 213 (no hay conflicto: la regla del CPT solo captura `/proyectos/<algo>/`).
  2. **Recomendado:** migrar el registro del CPT `proyectos` y de las 3 taxonomías de CPT UI a código (`inc/cpt-proyectos.php`, como ya se hace con `inc/cpt-landings.php`) para versionar la configuración de URLs. Mantener exactamente los mismos `name`/keys para no perder contenido. Después, desactivar CPT UI.
  3. Landings: cambiar `rewrite.slug` en `inc/cpt-landings.php:39` según D1 y añadir `'with_front' => false`.
  4. Vaciar los rewrites (Ajustes → Enlaces permanentes → Guardar) en cada entorno.
- **Redirecciones 301 obligatorias** (SEOPress Pro → Redirecciones, o regla en el servidor):
  - `^/proyecto/(.*)$` → `/proyectos/$1`
  - `^/landings/(.*)$` → `/<nuevo-prefijo>/$1` (si cambia)
  - `/inicio/` → `/`
- **Aceptación:** todas las URLs antiguas responden 301 en un solo salto hacia la nueva URL, que responde 200; los canonical apuntan a la nueva URL; no quedan enlaces internos a URLs antiguas (revisar menús, campos ACF con URLs absolutas, plugin `internal-links` y el contenido del editor). Hacer un search-replace en la BD de `/proyecto/` → `/proyectos/` (con WP-CLI `search-replace --dry-run` primero).

### P5. MEDIO (oportunidad): páginas por ciudad y por tipo
- **Contexto:** `ciudad_proyecto` (Cali tiene 10 proyectos) y `tipo_proyecto` (Residenciales 11) apuntan a búsquedas reales ("proyectos de vivienda en Cali", "constructora Cali"), pero hoy son `publicly_queryable = false`.
- **Propuesta:** hacer públicas **solo** `ciudad_proyecto` y `tipo_proyecto`, con rewrite `/proyectos/ciudad/<term>/` y `/proyectos/tipo/<term>/`. Crear `taxonomy-ciudad_proyecto.php` y `taxonomy-tipo_proyecto.php` reutilizando la grilla de `theme_proyectos.php`, con un H1 propio y una descripción del término (SEOPress ya permite editarla: `tax_desc_editor` = 1).
- **Reglas:** poner `noindex` a los términos con menos de 2 proyectos (Yumbo tiene 0; Medellín, Pereira, Puerto Tejada y "Cali / Puerto Tejada" tienen 1). Limpiar el término duplicado `cali-puerto-tejada`. `estado_proyecto` se queda privado (contenido pobre).
- **Orden de rewrites:** las reglas de taxonomía deben registrarse antes que `proyectos/([^/]+)/` para que `ciudad` y `tipo` no se lean como slug de proyecto. Reservar esos slugs (que ningún proyecto se llame `ciudad` ni `tipo`).

### P6. MEDIO: breadcrumbs
- SEOPress tiene breadcrumbs activados, pero el tema **no los imprime en ninguna plantilla** (no hay ninguna aparición de `breadcrumb` en el tema).
- **Acción:** imprimir `seopress_display_breadcrumbs()` (con `function_exists`) en `single-proyectos.php`, `single-landings.php`, en las plantillas de taxonomía (P5) y en las páginas internas. Así se emite `BreadcrumbList` en JSON-LD alineado con la nueva jerarquía: `Inicio › Proyectos › Cali › <Proyecto>`.

### P7. BAJO: limpieza de `inc/seo.php`
- Con SEOPress activo, casi todo el archivo es código muerto. Mantenerlo solo como respaldo; corregir P2. Comprobar que SEOPress Local Business sustituye correctamente al `RealEstateAgent` del tema y que no se emiten dos esquemas `Organization`.

---

## 2. Hallazgos: jerarquía de encabezados (H1–H6)

**Reglas a aplicar en todo el tema:**
1. Exactamente **un `<h1>` visible** por URL, que describa la página con la palabra clave (no "Inicio").
2. No saltar niveles: H1 → H2 → H3. Un H3 siempre va dentro de una sección con H2.
3. Los encabezados son para estructurar secciones, **no para dar estilo**. Etiquetas, datos, valores, años, ciudades o textos de iconos van en `<p>`/`<span>` con clase. El tamaño se controla con CSS (las clases `.h5`, `.h6` de Bootstrap ya existen).
4. Nada de MAYÚSCULAS escritas en el HTML ("PROYECTOS SIMILARES"): usar `text-transform` en CSS.
5. Los bloques globales (formulario de contacto y footer) no deben meter encabezados que rompan la jerarquía de la página.

### Tabla de cambios por plantilla

| # | Archivo:línea | Actual | Problema | Cambio propuesto |
|---|---|---|---|---|
| H1 | `theme_sobrenosotros.php` | Sin `<h1>` | **La página no tiene H1** | Añadir un H1 con `the_title()` o un campo ACF (por ejemplo "Sobre Skema Promotora") |
| H2 | `theme_inicio.php:13` | `<h1 class="screen-reader-text">` con `get_the_title()` = "Inicio" | H1 oculto y sin palabra clave | H1 visible en el hero (campo ACF nuevo `home_h1`, por ejemplo "Constructora y promotora inmobiliaria en Cali"), con respaldo en `blogname – blogdescription` |
| H3 | `theme_inicio.php:290,312,334` | `<h5>` textos de iconos | Salto H3→H5; no son títulos | `<p class="h5 …">` |
| H4 | `inc/inicio-helpers.php:145-148` | `<h2>` tipo, `<h3>` título, `<h4>` ciudad en cada tarjeta (dentro de un loop) | Inflación de encabezados: un H2 por tarjeta con un dato | Tipo y ciudad → `<p>`/`<span>`; título del proyecto → `<h3>` (dentro de la sección H2 de la home) |
| H5 | `inc/inicio-helpers.php:355` | `<h4>` nombre en ficha de servicio | Revisar el nivel según la sección contenedora | `<h3>` si la sección tiene H2 |
| H6 | `theme_proyectos.php:409` | H1 → `<h3>` en tarjetas | Falta el H2 | Añadir un H2 de sección (por ejemplo "Proyectos residenciales, comerciales e institucionales") o subir las tarjetas a `<h2>` |
| H7 | `single-proyectos.php:246,291` | `<h2>PROYECTOS SIMILARES</h2>`, `<h2>AVANCE DE OBRA</h2>` | Mayúsculas en el HTML | Texto en formato de oración + CSS `text-transform` |
| H8 | `single-proyectos.php:80` | `<h2>` con icono + campo `txt_ubic` | Comprobar que el texto no esté vacío y que describa la sección | Si está vacío, no imprimir el H2 |
| H9 | `theme_sobrenosotros.php:118` | `<h4>` año de la trayectoria | Un dato, no un título | `<span class="h4 …">` o `<time>` |
| H10 | `theme_ofrecetulote.php:33`, `theme_contacto.php:62` | `<h3>Contacto</h3>` justo después del H1 | Salto H1→H3 | `<h2>` |
| H11 | `template-parts/content-fcontacto.php:6` | `<h3>Contacto</h3>` (bloque global) | Queda colgado de cualquier H2 anterior | `<h2>` (es una sección propia) |
| H12 | `footer.php:53,82` | `<h6>Contacto en:</h6>`, `<h6>Dirección:</h6>` | Encabezados en el footer de todas las páginas | `<p class="h6 …">` |
| H13 | `inc/landings-hero-slider.php:311/325/703` | H1 en ramas distintas (prelanding/promo/screen-reader) | Riesgo de 0 o 2 H1 según variante y fase | Comprobar las 4 combinaciones `variant × phase`: exactamente un H1 visible. Quitar el H1 `screen-reader-text` si ya hay uno visible |
| H14 | `inc/landings-hero-slider.php:347` | `<h5>` encabezado del bloque de lead | Salto H1→H5 | `<h2>` (con clase visual actual) |
| H15 | `template-parts/landings/landing-content-box.php:168-280, 523, 561` | Secciones en `<h3>` | Faltan los H2: son secciones principales de la landing | `<h2>` |
| H16 | `landing-content-box.php:237` | `<h4>` categoría de la ficha del apartamento | Pasa a ser hijo de un H2 | `<h3>` |
| H17 | `landing-content-box.php:583` | `<h5>Solicitar información</h5>` | Salto de nivel | `<h2>` |
| H18 | `inc/landings-project-characteristics.php:167` | `<h6>` con **valores** de características | Datos, no títulos | `<span>`/`<dd>` (idealmente una lista `<dl>`) |
| H19 | `template-parts/landings/partials/medios-gallery-lightbox.php:83` | `<h5 class="modal-title">` | Modal: aceptable, pero mejor coherente | `<h2 class="modal-title h5">` |
| H20 | `header.php:38,53` | Logo con `aria-current="page"` en todas las páginas | Accesibilidad, ver A1 | Movido a la sección 3 (A1) |

**Importante para CSS:** al cambiar etiquetas (h5→h2, h6→p…) hay que conservar el aspecto visual. Revisar los selectores por etiqueta en `style.css`, `css/responsive.css` y `css/landings/*.css` (por ejemplo `.project-info-box h5`, `footer h6`, `.prelanding-copy h1`) y pasarlos a selectores por clase **antes** de cambiar el marcado.

---

## 3. Hallazgos: accesibilidad (atributos ARIA y WCAG 2.2 AA)

**Lo que ya está bien (no tocar):** `language_attributes()` en `<html>`, *skip link* a `#primary` (`header.php:27`) y un `<main id="primary">` en todas las plantillas. Los botones de abrir/cerrar menú tienen `aria-controls` y `aria-label`. Los iconos de redes sociales, WhatsApp y teléfono tienen `aria-label`. Los `<select>` de mapas y de contacto tienen `<label>` oculto. Los iframes de YouTube y del mapa tienen `title`. El lightbox de la galería maneja teclado (`skema-landing-medios-gallery.js:158`). Las flechas de los sliders son `<button>` con `aria-label`.

**Reglas a aplicar en todo el tema:**
1. **ARIA solo cuando el HTML nativo no alcanza.** Un `<button>` o `<nav>` no necesita `role`. Un ARIA mal usado es peor que no tenerlo.
2. Todo estado visual que cambie con JavaScript (abierto/cerrado, pestaña activa, slide actual) debe reflejarse en ARIA (`aria-expanded`, `aria-selected`, `aria-hidden`/`inert`).
3. Lo que está oculto visualmente también debe quedar oculto para el teclado y los lectores de pantalla.
4. Todo lo que se mueve solo más de 5 segundos debe poder pausarse (WCAG 2.2.2) y respetar `prefers-reduced-motion`.
5. Imágenes: `alt` descriptivo si aportan información; `alt=""` si son decorativas o si el texto ya está al lado. Nunca `alt` genérico como "Proyecto".

### 3.1 Navegación, landmarks y foco

| # | Archivo:línea | Problema | Cambio propuesto | WCAG |
|---|---|---|---|---|
| A1 | `header.php:38,53` | `aria-current="page"` en el logo de **todas** las páginas: el lector anuncia "página actual" en cualquier URL | Imprimirlo solo si `is_front_page()` | 4.1.2 |
| A2 | `header.php:30` | `<nav role="navigation">` redundante y sin nombre accesible | Quitar `role` y añadir `aria-label="Menú principal"`. Darle a la `<nav>` del footer, si existe, otro `aria-label` | 1.3.1 |
| A3 | `header.php:44` + `js/main.js:592-600` | El menú móvil se oculta con `right:-100%`: los enlaces fuera de pantalla **siguen siendo enfocables** con Tab. No hay cierre con `Esc`, el foco no entra en el menú al abrirlo ni vuelve al botón al cerrarlo | Con el menú cerrado en móvil: `inert` (o `visibility:hidden`) en `#menuPrincipal`. Al abrir: mover el foco al botón cerrar; `Esc` cierra; al cerrar, devolver el foco a `.btn-abrir`. Mantener sincronizado `aria-expanded` | 2.4.3, 2.1.1 |
| A4 | `header.php:45-47` | El botón cerrar arranca con `aria-expanded="true"` aunque el menú esté cerrado | Quitar `aria-expanded` del botón cerrar (solo lo lleva el botón que abre/controla) | 4.1.2 |
| A5 | `header.php:94,100` | Iconos `bi-whatsapp` / `bi-phone` sin `aria-hidden="true"` (el enlace ya tiene `aria-label`) | Añadir `aria-hidden="true"` al `<i>` | 1.1.1 |
| A6 | `header.php:94` | El enlace de WhatsApp abre en una pestaña nueva sin avisar | `aria-label="Contactar por WhatsApp (abre en una nueva pestaña)"` | 3.2.5 (AAA, buena práctica) |
| A7 | `css/landings/phase-prelanding.css:478,547`, `phase-landing.css:1571`, `css/slick*.css` | `outline:none` en campos y en los botones de Slick. En los formularios se compensa con `box-shadow` al 20 % de opacidad: **contraste insuficiente** del indicador de foco | Usar `:focus-visible` con un indicador de contraste ≥ 3:1 (por ejemplo `outline: 2px solid #0b1f42; outline-offset: 2px`). Revisar los botones de Slick | 2.4.7, 2.4.11 |
| A8 | Header fijo (`.fixed-top`) | El header fijo puede tapar el elemento enfocado y el destino del *skip link* | `scroll-padding-top` en `html` igual a la altura del header | 2.4.11 |
| A9 | Global | Ningún CSS ni JS propio respeta `prefers-reduced-motion` (solo Bootstrap) | Media query que desactive `scrollreveal`, `custom-animations.js`, las animaciones `slide-in-top`/`reveal` y el autoplay de los sliders | 2.3.3, 2.2.2 |

### 3.2 Sliders, carruseles y vídeo

| # | Archivo:línea | Problema | Cambio propuesto | WCAG |
|---|---|---|---|---|
| A10 | `js/main.js:154,523,534`, `js/skema-landing-respaldo-slider.js:22` | Sliders con `autoplay` (2–4,2 s) **sin botón de pausa** | Añadir un botón Pausar/Reproducir (`aria-pressed` o cambio de `aria-label`). Activar `pauseOnFocus`/`pauseOnHover` de Slick. Sin autoplay si `prefers-reduced-motion` | 2.2.2 |
| A11 | `js/main.js:15-30` | La región `aria-live="polite"` anuncia cada slide; con autoplay, **el lector anuncia sin parar** | Solo anunciar en cambios iniciados por el usuario (flechas, puntos, teclado); con autoplay, poner la región en `aria-live="off"` | 4.1.3 |
| A12 | Contenedores de Slick (todos) | Sin semántica de carrusel | Contenedor: `role="region"` + `aria-roledescription="carrusel"` + `aria-label` ("Planos del proyecto", "Aliados"…). Cada slide: `role="group"` + `aria-roledescription="diapositiva"` + `aria-label="3 de 8"`. Comprobar que Slick (`accessibility:true`) no deje slides clonados enfocables (`.slick-cloned` → `aria-hidden` + sin tabindex en los enlaces) | 1.3.1, 2.4.3 |
| A13 | `medios-plantas-slider.php:55-60` | Miniaturas de navegación con `alt=""` + `role="presentation"`, pero son **controles** (al hacer clic cambian de plano) | Si son clicables: `<button>` con `aria-label="Ver plano: <alt del slide>"` y `aria-current` en la activa. Si solo son decorativas: `aria-hidden="true"` en todo `.plantas-slider-nav` | 4.1.2, 1.1.1 |
| A14 | `theme_inicio.php:21-28,162`, `inc/inicio-hero-banner.php:52-58` | `<video autoplay loop muted>` de fondo sin control de pausa. El texto de respaldo está en inglés ("Your browser does not support…") | Botón visible de pausa (WCAG exige pausar el movimiento de más de 5 s). Si es decorativo: `aria-hidden="true"`. Texto de respaldo en español y traducible. Sin autoplay con `prefers-reduced-motion` | 2.2.2, 3.1.2 |
| A15 | `theme_inicio.php:107-123`, `inc/inicio-hero-banner.php:184-200`, `inc/landings-hero-slider.php:208-220,809-821` | Iframes de YouTube del hero con `controls=0&disablekb=1`: el usuario no puede pausar ni manejar el vídeo con teclado, pero el iframe **recibe foco** | Si es fondo decorativo: `tabindex="-1"` + `aria-hidden="true"` en el iframe y un botón de pausa propio (con la API de YouTube, ya está `enablejsapi=1`). Si es contenido: activar controles y teclado | 2.1.1, 2.2.2 |

### 3.3 Pestañas, selects dinámicos y modales

| # | Archivo:línea | Problema | Cambio propuesto | WCAG |
|---|---|---|---|---|
| A16 | `single-proyectos.php:144-160` | Pestañas con `role="tab"` sobre `<a href="#">` dentro de `<li>` sin `role="presentation"`; dos `id="myTab"` posibles en el sitio; "Caracteristicas" sin tilde | `<li role="presentation">`, usar `<button type="button">`; los `aria-selected` los sincroniza Bootstrap. Corregir el texto a "Características". IDs únicos | 4.1.2, 3.1.1 |
| A17 | `landing-content-box.php:285-310` | Las pestañas de medios están bien encaminadas (`button`, `role="tab"`, `aria-selected`) | Verificar `aria-controls` ↔ `id` del panel, `aria-labelledby` en cada `role="tabpanel"` y que las flechas ←/→ muevan entre pestañas (patrón WAI-ARIA Tabs) | 2.1.1 |
| A18 | `theme_inicio.php:244-266` + `js/main.js:553-570` | El `<select id="selectMapas">` cambia el contenido con `fadeIn`/`fadeOut` sin avisar | Poner `aria-live="polite"` en el contenedor de los mapas, o `aria-controls` en el select y un anuncio corto ("Mostrando: Residenciales") | 4.1.3 |
| A19 | `footer.php:56-90` + `js/main.js:582-590` | Igual con `#selectContact`: cambia el teléfono y la dirección sin anunciarlo. Además, "Teléfono" y "Correo" no son enlaces | Contenedor `aria-live="polite"`. Teléfono → `<a href="tel:…">` y correo → `<a href="mailto:servicioalcliente@skema.co">` (también mejora la conversión) | 4.1.3, 2.5 |
| A20 | `medios-gallery-lightbox.php:83` | Modal de Bootstrap: comprobar `aria-labelledby` ↔ `id` del título, que el botón cerrar tenga `aria-label="Cerrar"` y que el foco vuelva al disparador al cerrar | Verificar y completar | 2.4.3 |

### 3.4 Imágenes y texto alternativo

| # | Archivo:línea | Actual | Cambio propuesto | WCAG |
|---|---|---|---|---|
| A21 | `theme_sobrenosotros.php:179-180` | `alt="Proyecto"` genérico en todas las imágenes | `alt` del adjunto (`get_post_meta( $id, '_wp_attachment_image_alt', true )`) o el nombre del proyecto | 1.1.1 |
| A22 | `theme_inicio.php:187` (`slider_oportunidad`) | `alt=""` en imágenes de contenido del slider | `alt` del adjunto en ACF (devolver el array de la imagen, no la URL) | 1.1.1 |
| A23 | `theme_inicio.php:284,306,328` | Iconos con `alt` = el mismo texto que el `<h5>` de al lado: el lector lo lee dos veces | `alt=""` (el icono es decorativo, el texto ya está al lado) | 1.1.1 |
| A24 | `inc/inicio-helpers.php:156` | Logo de Skema `alt=""` en cada tarjeta | Correcto si es decorativo; mantener | — |
| A25 | `inc/inicio-helpers.php:375` | Logo del proyecto `alt=""` en la ficha de servicio | `alt="Logo de <proyecto>"`, salvo que el nombre ya esté al lado como texto | 1.1.1 |
| A26 | `theme_contacto.php:16`, `theme_proyectos.php:303` | `alt="Banner de <título>"` en banners | Si el banner es decorativo (el H1 está encima): `alt=""`. "Banner de…" no aporta información | 1.1.1 |
| A27 | `inc/landings-hero-slider.php:306` | `alt="Logo — <H1>"` junto al H1 con el mismo texto | `alt=""` o `alt="Logo"` para no repetir el nombre | 1.1.1 |
| A28 | Global ACF | Muchas imágenes vienen de campos ACF que devuelven URL (`the_sub_field('img')`), así que se pierde el `alt` de la mediateca | Cambiar el formato de retorno de los campos imagen a **Array/ID** y usar `wp_get_attachment_image()` (que además añade `width`/`height`, `srcset` y `loading`, con mejora de CLS/SEO) | 1.1.1 |

### 3.5 Enlaces, formularios y contenido

| # | Archivo:línea | Problema | Cambio propuesto | WCAG |
|---|---|---|---|---|
| A29 | `footer.php:92` | `target="_blank"` sin `rel="noopener"` ni aviso; el texto del enlace viene de ACF y puede estar vacío | `rel="noopener noreferrer"`, aviso de "nueva pestaña" (texto oculto o icono con `aria-label`) y no imprimir el enlace si está vacío | 2.4.4 |
| A30 | `landing-brochure-cta.php:35` | Descarga de PDF en una pestaña nueva sin indicar formato ni tamaño | Añadir en el texto accesible: "(PDF, abre en nueva pestaña)" | 2.4.4 |
| A31 | `inc/inicio-helpers.php:153`, `theme_proyectos.php:403` | Botones "Ver proyecto" repetidos: fuera de contexto todos dicen lo mismo | `theme_proyectos.php` ya usa `aria-label="Ver proyecto %s"` ✔. Replicar en `inicio-helpers.php:153` | 2.4.4 |
| A32 | `theme_contacto.php`, `theme_ofrecetulote.php`, `content-fcontacto.php`, CF7 de landings | Formularios Contact Form 7: verificar que cada campo tenga un `<label>` real (no solo `placeholder`), que los errores se asocien con `aria-describedby` (CF7 5.x lo hace si hay label) y que el reCAPTCHA no bloquee el teclado | Revisar el marcado de cada formulario en el admin de CF7; marcar los obligatorios con texto, no solo con color | 1.3.1, 3.3.1, 3.3.2 |
| A33 | `inc/landings-project-characteristics.php:160-167` | Icono `alt=""` + valor en `<h6>` sin la etiqueta de la característica | Estructura `<dl>`: `<dt>` (nombre de la característica, puede ser oculto visualmente) + `<dd>` (valor). Relacionado con H18 | 1.3.1 |
| A34 | Global (CSS) | Contraste no auditado: textos blancos sobre vídeo o imagen en el hero, naranja `#fc7416` sobre blanco (2,74:1: **no pasa** ni para texto normal, 4.5:1, ni para texto grande, 3:1) | Auditar con axe/Lighthouse. Usar una variante más oscura del naranja (por ejemplo `#B64900`, ≈ 5,3:1) para **todo** texto naranja sobre blanco, también los titulares, y un overlay oscuro detrás del texto del hero | 1.4.3, 1.4.11 |
| A35 | Popup Builder (plugin) | Los popups suelen romper el foco y no cierran con `Esc` | Verificar los popups activos: foco atrapado dentro, `Esc` cierra y el foco vuelve al disparador | 2.1.2, 2.4.3 |

**Criterios de aceptación de la sección 3:**
- Lighthouse Accessibility ≥ 95 y **0 errores críticos o graves en axe DevTools** en: home, `/proyectos/`, una ficha de proyecto, una landing en cada fase y `/contacto/`.
- Navegación completa **solo con teclado** (Tab / Shift+Tab / Enter / Esc / flechas): menú móvil, pestañas, sliders, lightbox, selects y formularios, con el foco siempre visible y nunca en elementos ocultos.
- Prueba con lector de pantalla (VoiceOver en macOS/iOS, NVDA en Windows): la lista de encabezados y de landmarks se entiende sola y los carruseles no anuncian nada sin que el usuario actúe.
- Con `prefers-reduced-motion: reduce` no hay autoplay ni animaciones de entrada.

---

## 4. Plan por fases sugerido

| Fase | Contenido | Riesgo | Depende de |
|---|---|---|---|
| **0. Línea base** | Crawl completo (Screaming Frog) de producción: lista de URLs indexadas, H1/H2 por URL, enlaces internos. Exportar el rendimiento por URL desde Search Console (Site Kit está activo). Backup con UpdraftPlus. | — | — |
| **1. Quick wins sin cambiar URLs** | P1 (sitemap), P2 (robots), P3 (duplicados/basura), `pruebas.php` | Bajo | 0 |
| **2. Encabezados** | H1–H20 + refactor de selectores CSS | Bajo (visual) | 0 |
| **2b. Accesibilidad** | A1–A35. Orden sugerido: alt/ARIA sencillos (A1, A2, A4–A6, A21–A31) → foco y teclado (A3, A7, A8, A16–A20) → movimiento (A9–A15) → contraste y formularios (A32–A35). Se puede hacer junto con la fase 2 porque tocan las mismas plantillas | Bajo–medio (JS de sliders/menú) | 0 |
| **3. Migración de permalinks** | P4: CPT a código, nuevos slugs, 301, search-replace, purgar WP Rocket/LiteSpeed, reenviar el sitemap en Search Console | **Alto** | 1 |
| **4. Taxonomías públicas** | P5 + plantillas + descripciones + noindex de términos pobres | Medio | 3 |
| **5. Breadcrumbs y schema** | P6, P7; validar con Rich Results Test | Bajo | 3 |
| **6. Monitoreo** | Search Console: cobertura, 404, cambio de URLs indexadas durante 4–6 semanas | — | 3 |

**Checklist de verificación tras la fase 3:**
- [ ] `curl -I` de cada URL antigua → `301` → URL nueva `200`
- [ ] Sin cadenas de redirección (máximo 1 salto)
- [ ] El canonical de cada ficha apunta a `/proyectos/<slug>/`
- [ ] Los menús (`MenuPricipal`), campos ACF y enlaces de `inicio-renvia-proyectos.php` / `inicio-helpers.php` usan `get_permalink()` y no URLs fijas en el código
- [ ] Sitemap regenerado y enviado
- [ ] Cache purgada (WP Rocket + LiteSpeed + CDN si existe)

---

## 5. Decisiones pendientes (las toma el cliente o el responsable)

- **D1. Prefijo de las landings:** ¿qué relación tiene una landing con su proyecto?
  - (a) La landing **es** la página comercial del proyecto en venta → `/proyectos/<slug>/` y la ficha antigua se redirige (requiere unificar o evitar slugs duplicados entre CPTs).
  - (b) Son páginas de campaña → prefijo descriptivo: `/proyectos-en-venta/<slug>/` o `/vivienda-nueva/<slug>/`.
  - (c) Son solo para pauta (Ads) → mantener la URL y poner `noindex` para no canibalizar las fichas.
- **D2.** ¿Las landings en fase `prelanding` se indexan o llevan `noindex` hasta que pasen a la fase final?
- **D3.** ¿Habrá blog? Si no, desactivar los archivos de `post`, `category`, autor y fecha (SEOPress → Titles → Archives: noindex).
- **D4.** ¿Se ejecuta la fase 4 (páginas por ciudad/tipo)? Requiere escribir contenido para cada término.
- **D5.** Palabra clave principal para el H1 de la home.
- **D6.** Nivel de conformidad objetivo: WCAG 2.2 **AA** (recomendado; es el estándar de referencia en Colombia, NTC 5854 / Resolución MinTIC 1519 de 2020 para entidades públicas, y una buena práctica para privados).
- **D7.** ¿Los vídeos del hero son decorativos (solo ambientación) o tienen contenido? Define A14/A15: botón de pausa y `aria-hidden`, o controles completos.
- **D8.** ¿Se puede ajustar la paleta (naranja `#fc7416`)? A 2,74:1 no sirve como color de texto sobre blanco en ningún tamaño; solo puede mantenerse como fondo o en elementos decorativos. (A34)
