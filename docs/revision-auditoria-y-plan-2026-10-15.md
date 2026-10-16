# Revisión de la auditoría SEO/encabezados y del plan de ejecución

**Sitio:** Skema Promotora (tema `theme_skema`)
**Fecha de la revisión:** 2026-10-15
**Insumos:** [auditoria-seo-arquitectura-urls-y-encabezados.md](auditoria-seo-arquitectura-urls-y-encabezados.md) y [plan-de-ejecucion.md](plan-de-ejecucion.md)

**Qué se comprobó:** `skema-02.local` respondía. Se revisaron con `curl` 36 URLs (código de estado y destino de cada redirección) y se extrajeron el `<head>` y los encabezados de 11 de ellas. También se leyó el código del tema.

**Qué no se pudo comprobar:** el servidor de Playwright no conectó, así que no se ejecutó JavaScript ni se hicieron capturas. Lo que se dice del DOM ya renderizado (clones de Slick, `aria-hidden` que se aplica al rotar, visibilidad por punto de corte) sale de las clases del HTML servido y de la configuración de `js/main.js`, no de una prueba en navegador. Tampoco se ejecutaron Lighthouse ni axe, y no hubo acceso a producción.

---

## 1. Veredicto

La auditoría sirve como línea base para la estructura de encabezados y el contexto técnico, con tres límites:

- No revisó `title` ni `meta description`.
- No revisó búsqueda interna, parámetros de URL ni el DOM renderizado.
- Algunos de sus criterios ya no valen después de D1, por ejemplo el de P1.

El plan está bien ordenado, pero **todavía no se puede llevar a producción**:

- Varias casillas `[x]` solo se comprobaron en el HTML del servidor, no en el DOM ni en móvil. En móvil Tamborá no tiene ningún H1 visible.
- Todos los pendientes R1–R8 siguen abiertos.
- La fase 3 tiene dos riesgos sin cubrir: la redirección pierde `gclid`/`utm`, y no hay ningún paso para crear y vincular el proyecto Tamborá en producción.
- Los cambios de las fases 1–3 siguen sin commit, y `inc/cpt-proyectos.php` ni siquiera está bajo control de versiones.

## 2. Huecos y correcciones de la auditoría

| Hallazgo o corrección | Severidad | Evidencia | ¿Lo cubre el plan? |
|---|---|---|---|
| **El H1 de Tamborá no se ve en móvil.** El H1 está dentro de `.slider-banner.d-none.d-sm-block` y el hero móvil (`.d-sm-none`) solo usa `h2`. Por debajo de 576 px no hay H1 visible, y Google indexa la versión móvil. H13 solo pedía revisar las combinaciones variante × fase, no los puntos de corte. | Alta | `/proyectos/tambora/`, línea 212 del HTML; `inc/landings-hero-slider.php:735` (h1 en escritorio) y `:770` (h2 en móvil) | No. El plan marca esto como `[x]` |
| **El H1 de Tamborá está dentro de un slide con autoplay.** Al rotar, `skemaLabelCarouselSlides` (`js/main.js:141-158`) pone `aria-hidden="true"` en el slide que lleva el H1. Además, Slick usa por defecto `infinite:true` y clona el primer slide, así que en el DOM renderizado habría dos `<h1>`. *Deducido del código; no comprobado en navegador.* | Alta | `js/main.js:340-357`, sin `infinite:false` ni `fade` | No |
| **Las redirecciones 301 pierden la query string.** `/landings/tambora/?gclid=abc&utm_source=g` redirige a `/proyectos/tambora/` sin los parámetros, porque la función usa `$GLOBALS['wp']->request`. Las landings reciben tráfico de pauta. | Alta | `inc/cpt-proyectos.php:306-366` y la respuesta de `curl` | No |
| **La auditoría no revisó `title` ni `meta description`.** Las descripciones se generan solas y salen mal: inicio «Líderes en construcción», hub «DESCUBRE EL PROYECTO IDEAL PARA TI», `/ofrece-tu-lote/` «REGRISTRA TUS DATOS», `/politica-privacidad/` «JUR-POL-001POLTICADETRATAMIENTO…». El title de inicio no incluye la palabra clave del H1. | Media | `<head>` de cada URL | No |
| **La búsqueda interna es indexable y sale duplicada.** `/?s=tambora` y `/search/tambora/` responden 200 con `index, follow`, un H1 en inglés («Search Results for:») y Tamborá dos veces (la landing y el proyecto). `landings` no tiene `exclude_from_search`. | Media | `inc/cpt-landings.php:30-44`; HTML de `/?s=tambora` | No |
| **H8 está mal planteado.** El problema no es solo que `txt_ubic` venga vacío: la ubicación es un dato y no debería ser un H2 (regla 3). Lo mismo vale para `tt_proyect`, que es un eslogan («Aureal Proyecto Real»). | Media | `single-proyectos.php:70,88` | No |
| **Los textos en mayúsculas no se limitan a `single-proyectos` (H7).** También vienen de ACF o del editor en el h2 de inicio, el h2 del hub y el h1/h2/h3 de `/sobre-nosotros/`. | Media | Outline de esas URLs | Solo el H1 (R6) |
| **A26/A27 se quedaron a medias.** `alt="Logo — %s"` sigue en `single-proyectos.php:60` («Logo — Aureal» junto al H1 «Aureal») y en `inc/landings-respaldo-slider.php:65`, donde produce «Logo — OTRO PROYECTO DE», «Logo — SUBSIDIO». Además, «Banner del proyecto Aureal». | Media | HTML de Aureal y de Tamborá | No |
| **Contenido de prueba publicado.** El hero móvil de Tamborá muestra «El mejor proyecto VIS 3» / «Descubre Tamborá 3». | Media | HTML de `/proyectos/tambora/` | No |
| **Tamborá no aparece en la grilla del hub**, porque el proyecto 1152 no tiene ciudad ni tipo. La única página comercial indexable solo recibe enlace desde el menú. | Media | Outline del hub: 17 `h3`, ninguno es Tamborá | Se menciona, pero no hay tarea |
| **H16 / ficha del apartamento:** «Torres», «Pisos Torre», «Aptos por piso» son etiquetas de datos en `h3`. Deberían ir en `dl`, igual que en A33. | Baja | `template-parts/landings/landing-content-box.php:237` | No |
| **Contradicción P1 ↔ D1:** está resuelta en el código, salvo una vía. `landings` está fuera del sitemap y su canonical apunta al proyecto, pero `/?landings=tambora` sigue respondiendo 200, con `og:url` = `http://skema-02.local/`. | Media | R3 | Sí (R3, abierto) |
| **Contradicción A34 ↔ R1:** la auditoría estaba bien. Su A34 pide `#B64900` para *todo* texto, titulares incluidos (auditoría, línea 205). La contradicción la introdujo la casilla A34 del plan («los titulares grandes conservan la marca»). Sigue viva: hay 12 reglas `color: var(--nara)`, incluido `.project-item .card .card-body h3` (títulos del hub) y `.divMapa p`. | Alta | `style.css:1175,1597`; `css/home/skema-home-proyectos.css:46,204,229,279` | R1, abierto |
| **Contradicción en el H1 de Sobre nosotros:** R6 describe mal el H1. No contiene la frase completa: es solo «POR MÁS DE 18 AÑOS». Depende de que el editor tenga un `<h1>` (`theme_sobrenosotros.php:24`), algo frágil. La meta description repite la misma frase en mayúsculas. | Media | Outline de `/sobre-nosotros/` | R6, con un diagnóstico que hay que corregir |
| **P1 tiene un criterio de aceptación obsoleto** («lista landings»). Con D1 debe decir lo contrario. | Baja | Auditoría, línea 46 | Implícitamente sí |

## 3. Huecos del plan

### Casillas `[x]` que no se sostienen

- **Fase 2, «Un solo H1 en el HTML»:** se comprobó sobre el HTML del servidor. No cubre el H1 invisible en móvil, el H1 clonado por Slick ni el `aria-hidden` del slide. Además cita `/landings/tambora/`, que ahora es un 301.
- **Fase 2, Landings:** que el H1 solo esté en el primer slide de escritorio es justo el defecto de diseño. El H1 debe ir **fuera del carrusel**, una sola vez y visible en todos los tamaños; los títulos de los slides deben ir en `p`.
- **Fase 2, `theme_sobrenosotros.php`:** el H1 sigue dependiendo del contenido del editor (R6).
- **A34:** el texto de la casilla contradice D8. La casilla debería desmarcarse o fusionarse con R1.
- **A12:** se marcó como hecho, pero esa implementación es la que oculta el H1 al rotar el carrusel.
- **Fase 1, P3:** `/landings/1149/` pasó a borrador, pero el popup 1136 que sale en inicio sigue enlazándola. **El plan dice que ese enlace «funciona por el 301». Es falso: apunta a `/landings/1149/`, que da 404.**

### Pendientes R1–R8 y menores (todos siguen abiertos)

| Pendiente | Estado | ¿Es correcta la solución propuesta? |
|---|---|---|
| R1 | Abierto | Sí |
| R2 | Abierto: 10 `home-hero-dots" aria-hidden="true"` | Sí |
| R3 | Abierto | Sí, pero hay que añadir `! is_preview()` para no romper la vista previa de las landings |
| R4 | Abierto: siguen 3 regiones `aria-live` en `footer.php:68,71,90` | Sí |
| R5 | Abierto | Hay que ampliarlo a `theme_ofrecetulote.php:16,46,60` |
| R6 | Abierto | Sí, con el diagnóstico corregido |
| R7 | Abierto: solo se aplica `menu.inert` | Sí |
| R8 | Abierto | Con `wp_insert_post_data`, el valor ACF del formulario todavía no está guardado. Usar `acf/validate_value` o `acf/save_post` con prioridad > 10 |
| `/contacto/` | 404. Ninguna de las páginas revisadas la enlaza | Se recomienda quitarla de la arquitectura objetivo |
| Prueba real de la prelanding | Sin hacer | — |
| Commits | Todo mezclado y sin commit | — |

### Criterios de aceptación que faltan

- Un solo H1 **en el DOM renderizado** en 375 px y en 1280 px, con el slider ya inicializado.
- La redirección conserva la query string.
- `/?s=` lleva `noindex`.
- Ninguna `meta description` está en mayúsculas o es basura.
- Ningún enlace interno da 404 (contando los popups).
- La regla de **no saltar niveles** tiene una casilla propia de aceptación.

### Qué hay que resolver antes de la fase 3 en producción

1. **Crear el proyecto Tamborá en producción, vincular `skema_landing_proyecto` y actualizar el menú en el mismo despliegue.** Con `rewrite => false`, si no hay proyecto vinculado, `/landings/tambora/` acaba en 404 (`inc/cpt-proyectos.php:353-356`). El plan solo habla de repetir el inventario y el search-replace.
2. Conservar la query string en el 301 (`add_query_arg( $_GET, $target )` o equivalente).
3. Versionar `inc/cpt-proyectos.php`: `functions.php:321` lo carga con `require`, así que si falta en el despliegue el sitio da error fatal. Separar los commits por fase.
4. Comparar la configuración de CPT UI en producción (supports, capabilities, `show_in_rest`, args de las taxonomías) con el registro en código. Por ejemplo, el código no declara `revisions` para `proyectos`.
5. Replicar en producción los ajustes de SEOPress que viven en la base (sitemap, noindex de archivos). Añadir una casilla de verificación en producción.
6. **Fase 4 (aviso anticipado):** en el código, el CPT se registra antes que las taxonomías. El core genera la regla de adjuntos `proyectos/[^/]+/([^/]+)/?$`, que capturaría `/proyectos/ciudad/cali/`. Hay que registrar las taxonomías antes o usar `add_rewrite_rule(..., 'top')`. *Deducido del core; no comprobado con `wp rewrite list`.*

El orden de fases 0 → 1 → 2/2b → 3 → 4 → 5 → 6 es correcto. Pero como todo está en un mismo árbol sin commits, en la práctica las fases 1–3 saldrían en un solo despliegue de alto riesgo.

## 4. Outline por URL

### `/` (inicio)

1. h1 «Constructora y promotora inmobiliaria en Cali» — visible
2. h2 «CONSTRUIMOS Y DESARROLLAMOS PROYECTOS DESDE CALI PARA EL MUNDO» — visible
3. h2 «CONOCE NUESTROS PROYECTOS en construcción y ventas» — visible
4. h3 ×5 «Mama Shelter», «Tamborá Apartamentos», «Aureal», «Tribeca», «Astoria» — visibles
5. h2 «Contacto» — visible

Violaciones: los h2 están en mayúsculas escritas en el HTML, y el segundo usa `</br>`, que no es válido.

### `/proyectos/`

1. h1 «Nuestros Proyectos»
2. h2 «DESCUBRE EL PROYECTO IDEAL PARA TI»
3. h2 «Proyectos residenciales, comerciales e institucionales»
4. h3 ×17 tarjetas, dentro de `.tab-pane` (visibles si su pestaña está activa)
5. h2 «Contacto»

Violaciones: el primer h2 es un eslogan en mayúsculas, no una sección; el H1 no tiene palabra clave; falta Tamborá; los h3 tienen color `--nara` (R1).

### `/proyectos/tambora/` (landing vinculada, fase `landing`)

`/?landings=tambora` sirve un outline idéntico.

1. h1 «Tamborá Apartamentos» — visible solo en ≥ 576 px
2. h2 «¡La inversión inteligente de tu vida!» y h2 «Parqueaderos comunales…» — escritorio, en slides que rotan
3. h2 «El mejor proyecto VIS 3» y h2 «El mejor proyecto VIS» — solo móvil
4. h2 «Información General del Proyecto», h2 «Descripción del apartamento», h2 «Zonas Sociales», h2 «Ficha del apartamento»
5. h3 ×12 («Habitaciones» … «Parqueadero comunal motos»)
6. h2 «Información General», h2 «Medios del proyecto»
7. h2 ×2 «Galería — …» — modales, ocultos
8. h2 «Con el respaldo de:», h2 «Ubicación», h2 «Solicitar información»

Violaciones:

- No hay H1 visible en móvil.
- El H1 está en un slide con autoplay: queda con `aria-hidden` al rotar y probablemente se clona.
- Los títulos de los slides en h2 no abren ninguna sección.
- Hay contenido de prueba publicado.
- «Información General» e «Información General del Proyecto» son dos h2 casi iguales.
- Hay datos marcados como h3.
- Los modales tienen h2: es aceptable, no compiten con el H1.

### `/proyectos/aureal/` (ficha sin landing)

1. h1 «Aureal»
2. h2 «Aureal Proyecto Real»
3. h2 «A 5 minutos del Sur de Cali - Ciudad del Sur»
4. h2 «Proyectos similares» con 10 h3
5. h2 «Contacto»

Violaciones: la ubicación y el eslogan están marcados como h2 (regla 3); el H1 no tiene palabra clave.

### `/sobre-nosotros/`

1. h1 «POR MÁS DE 18 AÑOS»
2. h2 «LOS VALORES QUE NOS REPRESENTAN:» con 4 h3 en mayúsculas
3. h2 «TRAYECTORIA» con h3 «Contamos con el respaldo de SAINC…»
4. h2 «¿QUÉ OFRECEMOS?» con 4 h3

Violaciones: H1 sin palabra clave y en mayúsculas en el HTML; todo el outline en mayúsculas; además `alt="Banner"` y `alt="Icono"` ×5.

### `/ofrece-tu-lote/`

h1 «Ofrece tu lote» y h2 «Contacto». El outline es correcto; el H1 no tiene palabra clave.

### `/politica-privacidad/`

h1 «Política de privacidad». Correcto.

### Footer y popup 1136

Ya no insertan encabezados (H12 sí está hecho).

## 5. Hallazgos nuevos de SEO

| URL o archivo | Impacto | Acción mínima | Fase |
|---|---|---|---|
| Redirección 301 en `inc/cpt-proyectos.php:321` | Se pierde la atribución de Ads (`gclid`/`utm`) | Reenviar la query string en `wp_safe_redirect` | 3 (antes de prod) |
| Popup 1136 → `/landings/1149/` (404) | Enlace roto en inicio | Apuntarlo a `/proyectos/tambora/` o desactivarlo | 1 |
| `/?s=…`, `/search/…` indexables | Contenido fino y duplicado | `noindex` en la búsqueda (SEOPress) y `exclude_from_search => true` en `landings` | 1 |
| Meta descriptions (inicio, hub, ofrece-tu-lote, política, sobre nosotros) | Snippets pobres, con errores o basura | Escribirlas a mano en SEOPress; title de inicio con la palabra clave | 1 (contenido) |
| `?landings=` con `og:url` = portada | Señal social y de canonical incoherente | Queda resuelto con R3 | 3 |
| Landing renderizada con `$post = $landing` (`inc/cpt-proyectos.php:437`) | No está definido qué metadatos de SEOPress mandan, los del proyecto o los de la landing. La description de Tamborá sale de la landing. *No se comprobó el caso contrario.* | Decidir y documentar quién es dueño del title y la description, y probar a editar el campo en el proyecto | 3 |
| `/proyecto/aureal/feed/` → 200 con RSS vacío; `/feed/` → 200 | Rastreo inútil (bajo) | Quitar los feeds, o redirigir el `/proyecto/…/feed/` antiguo | 6 |
| `/proyecto/<slug>/` de un proyecto no publicado → 404 | Pérdida de enlaces antiguos (bajo) | Usar el inventario de la fase 0 para decidir entre 301 al hub o 410 | 3 |
| JSON-LD: inicio solo emite `WebSite` y `Organization`; las fichas no emiten nada | No hay duplicados (P7 resuelto en local), pero tampoco hay datos estructurados en las fichas | Sin cambio; lo cubre la fase 5 | 5 |

## 6. Prioridades

### Antes de producción (fases 1–3)

1. Sacar el H1 de Tamborá del carrusel: un solo H1 visible en todos los tamaños, y los títulos de los slides en `p`. Comprobarlo en el DOM renderizado a 375 px y a 1280 px.
2. Hacer que el 301 conserve la query string.
3. Preparar el paso de producción: crear el proyecto Tamborá, vincular la landing y actualizar el menú en el mismo despliegue que el código.
4. Implementar R3 con `! is_preview()`.
5. Arreglar el enlace del popup 1136.
6. Poner `noindex` en la búsqueda y excluir `landings` de ella.
7. Implementar R1 y reescribir A34; después R2. Ambos son requisito para pasar axe.
8. Commitear por fases, incluido `inc/cpt-proyectos.php`, y comparar CPT UI y SEOPress con producción.
9. Probar la prelanding con un caso real.
10. Quitar el contenido de prueba («… VIS 3»).

### Puede esperar a las fases 4–6

- R6, ya con el diagnóstico corregido, y pasar a CSS las mayúsculas escritas en ACF o en el editor.
- Ubicación y eslogan de `single-proyectos` pasan de h2 a `p`.
- La ficha del apartamento pasa a `dl`.
- R5 ampliado a ofrece-tu-lote, y quitar los alt «Logo — …» que repiten el texto contiguo.
- Meta descriptions manuales.
- Asignar ciudad y tipo a Tamborá para que aparezca en el hub y en los listados de la fase 4.
- Registrar las taxonomías antes que el CPT (fase 4).
- R4, R7, R8, migas y schema (fase 5), y feeds y 404 antiguos (fase 6).
