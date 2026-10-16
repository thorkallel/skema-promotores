# Estrategia antispam para formularios Contact Form 7 — theme_skema

Fecha: 2026-10-15

## 1. Diagnóstico del estado actual

| Elemento | Estado detectado | Observación |
|---|---|---|
| Contact Form 7 | 6.1.6 | Trae de serie **Cloudflare Turnstile**, **reCAPTCHA v3**, **Akismet** y la **lista de palabras no permitidas** de WordPress. |
| `wpcf7-recaptcha` | 1.5.0 | Plugin de terceros para reCAPTCHA **v2** (casilla «No soy un robot»). Es el captcha más fácil de saltar con granjas de resolución (2Captcha, CapSolver, etc.). |
| `contact-form-7-honeypot` | 3.6.0 instalado | Verificar que esté **activo** y que el tag `[honeypot …]` esté realmente dentro de **cada** formulario. |
| Flamingo | instalado | Útil para analizar el spam que llega (guarda envíos y los marcados como spam). |
| Wordfence + All In One WP Security | instalados | Dos firewalls a la vez: se puede aprovechar uno solo para el rate limiting del endpoint de CF7. |
| WP Rocket + LiteSpeed Cache | instalados | **Importante**: cualquier técnica que dependa de un valor generado en PHP al renderizar (timestamp, nonce) se rompe con la caché de página. Las soluciones propuestas aquí son compatibles con caché. |

Formularios en el tema:

- `theme_contacto.php` y `template-parts/content-fcontacto.php` → `[contact-form-7 id="d3f6ebb" title="Formulario Contacto"]`
- `theme_ofrecetulote.php` → `[contact-form-7 id="c091d98" title="Formulario Ofrece tu lote"]`
- Landings → shortcodes dinámicos desde ACF (`inc/acf-landings-slider.php`, `inc/landings-hero-slider.php`). **Cada formulario de landing también debe recibir las protecciones** (honeypot, etc.), no solo los dos principales.

### Por qué el reCAPTCHA no es suficiente

1. **CF7 envía por REST** (`/wp-json/contact-form-7/v1/contact-forms/{id}/feedback`). Muchos bots no cargan la página: hacen POST directo al endpoint. Si alguna protección es solo de frontend, se la saltan.
2. reCAPTCHA v2 se resuelve por ~1–3 USD cada 1000 captchas en servicios de resolución, y gran parte del spam actual es **humano o semihumano** (granjas que llenan formularios a mano para SEO, cripto, servicios de marketing).
3. Una sola barrera = un solo punto de falla. La estrategia correcta es **por capas**: cada capa filtra un tipo de spam distinto y ninguna depende de la otra.

---

## 2. Estrategia por capas (de menor a mayor esfuerzo)

### Capa 0 — Medir antes de tocar (30 min)

Antes de cambiar nada, entender **qué** spam llega:

- Revisar en Flamingo (Contacto → Mensajes entrantes) 20–30 mensajes de spam y anotar:
  - ¿Contienen enlaces? ¿Cuántos?
  - ¿Idioma? (inglés, ruso, caracteres cirílicos/chinos)
  - ¿Campo «nombre» con URLs o texto basura?
  - ¿Teléfonos con formato inválido?
  - ¿Dominios de correo repetidos?
  - ¿Llegan a un formulario concreto (contacto, lote, alguna landing)?
  - ¿Horarios / ráfagas? (muchos en pocos minutos = bot; goteo diario = humano/granja)
- Con esto se ajustan las reglas de la Capa 4 y la lista de palabras de la Capa 3.

### Capa 1 — Honeypot (inmediato, sin costo)

El plugin ya está instalado. En **cada** formulario CF7 (incluidos los de landings) agregar:

```
[honeypot sitio-web-hp move:true validautocomplete:true timecheck_enabled:true timecheck_threshold:4]
```

- `move:true`: mueve el campo fuera del flujo visual (evita detección por `display:none`).
- `timecheck_*`: rechaza envíos hechos en menos de N segundos desde que cargó el formulario (las opciones disponibles dependen de la versión; revisar en la configuración del plugin: *Contacto → Honeypot*).
- Usar un nombre de campo creíble (`sitio-web`, `empresa-url`), no `honeypot`.

Filtra: bots simples que rellenan todos los campos.

### Capa 2 — Reemplazar reCAPTCHA v2 por Cloudflare Turnstile (recomendado)

CF7 6.x integra **Turnstile** de forma nativa (Contacto → Integración → Cloudflare Turnstile):

1. Crear un widget gratis en el panel de Cloudflare (no hace falta que el dominio esté en Cloudflare).
2. Pegar *Site Key* y *Secret Key* en la integración de CF7.
3. **Desactivar** el plugin `wpcf7-recaptcha` y quitar los tags `[recaptcha]` de los formularios.
4. Agregar `[turnstile]` en cada formulario (si no se coloca, CF7 lo inserta automáticamente antes del botón).

Ventajas frente a reCAPTCHA v2: invisible para la mayoría de usuarios, mejor detección de bots, sin cookies de Google (mejor para privacidad/GDPR) y **validación del token en el servidor** (protege también contra POST directos al endpoint REST).

Alternativa: reCAPTCHA **v3** nativo de CF7 con umbral más estricto:

```php
// Por defecto CF7 usa 0.50. Subir a 0.7 si sigue entrando spam (cuidado con falsos positivos).
add_filter( 'wpcf7_recaptcha_threshold', function () { return 0.7; } );
```

> No usar reCAPTCHA v2 y v3/Turnstile a la vez: elegir uno.

### Capa 3 — Lista de palabras no permitidas + Akismet

**Lista de palabras (gratis, inmediato):** CF7 usa la lista de *Ajustes → Comentarios → Claves de comentarios no permitidas*. Si un envío contiene alguna, se marca como spam. Ejemplo inicial (ajustar con lo visto en la Capa 0):

```
seo services
backlinks
crypto
bitcoin
casino
viagra
porn
loan
http://
bit.ly
t.me/
telegram
whatsapp me at
guest post
ranking on google
```

> Ojo: no incluir palabras que un cliente real pueda usar (p. ej. «crédito», «préstamo» si aplican a la compra de vivienda).

**Akismet (opcional, de pago para uso comercial):** CF7 lo integra marcando campos en el formulario:

```
[text* nombre akismet:author]
[email* correo akismet:author_email]
[textarea mensaje]
```

### Capa 4 — Reglas propias del tema (filtro `wpcf7_spam`)

Es la capa más potente porque se ejecuta **en el servidor** y se adapta al negocio. Propuesta: crear `inc/cf7-antispam.php` y cargarlo desde `functions.php`:

```php
require get_template_directory() . '/inc/cf7-antispam.php';
```

#### 4.1 Trampa de tiempo compatible con caché

El timestamp no puede generarse en PHP (WP Rocket/LiteSpeed lo congelarían). Se genera en JS:

1. PHP agrega un campo oculto **vacío** a todos los formularios (`wpcf7_form_hidden_fields`).
2. JS lo rellena con `Date.now()` cuando el usuario interactúa (primer `focus`/`input`).
3. El servidor rechaza si: está vacío (bot que no ejecuta JS o POST directo), si el envío fue en menos de X segundos o si es demasiado antiguo.

#### 4.2 Heurísticas de contenido

- Más de N enlaces en el mensaje (p. ej. > 1).
- URLs o `@` en el campo nombre.
- Caracteres cirílicos, chinos, árabes, etc. (si el público es hispanohablante).
- Mensaje 100 % en inglés con palabras típicas de spam.
- Teléfono con formato inválido (definir el patrón del país objetivo).
- Correos de dominios desechables (`mailinator.com`, `yopmail.com`, `guerrillamail.com`, `tempmail…`).

#### 4.3 Límite de envíos por IP

Máximo, por ejemplo, 3 envíos por IP cada 10 minutos usando *transients*.

#### Código de referencia (`inc/cf7-antispam.php`)

```php
<?php
/**
 * Reglas antispam adicionales para Contact Form 7.
 *
 * Complementa honeypot + Turnstile/reCAPTCHA. Todo se valida en servidor,
 * por lo que también cubre POST directos al endpoint REST de CF7.
 *
 * @package theme_skema
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPCF7_Submission' ) ) {
	return;
}

const SKEMA_CF7_TS_FIELD    = '_skema_ts';
const SKEMA_CF7_MIN_SECONDS = 4;          // Envío más rápido permitido.
const SKEMA_CF7_MAX_AGE     = 2 * DAY_IN_SECONDS;
const SKEMA_CF7_MAX_LINKS   = 1;
const SKEMA_CF7_RATE_LIMIT  = 3;          // Envíos por IP…
const SKEMA_CF7_RATE_WINDOW = 10 * MINUTE_IN_SECONDS; // …en esta ventana.

/**
 * Campo oculto vacío (cache-safe): lo rellena JS en el navegador.
 */
add_filter(
	'wpcf7_form_hidden_fields',
	function ( $fields ) {
		$fields[ SKEMA_CF7_TS_FIELD ] = '';
		return $fields;
	}
);

/**
 * JS mínimo: marca el momento de la primera interacción con el formulario.
 */
add_action(
	'wp_footer',
	function () {
		if ( ! wp_script_is( 'contact-form-7', 'enqueued' ) ) {
			return;
		}
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.querySelectorAll('form.wpcf7-form').forEach(function (form) {
				var field = form.querySelector('input[name="<?php echo esc_js( SKEMA_CF7_TS_FIELD ); ?>"]');
				if (!field) { return; }
				var mark = function () {
					if (!field.value) { field.value = String(Date.now()); }
				};
				form.addEventListener('focusin', mark, { once: true });
				form.addEventListener('input', mark, { once: true });
			});
		});
		</script>
		<?php
	},
	100
);

/**
 * Marca como spam y deja registro del motivo (visible en Flamingo).
 */
function skema_cf7_flag( $submission, $reason ) {
	$submission->add_spam_log(
		array(
			'agent'  => 'theme_skema',
			'reason' => $reason,
		)
	);
	return true;
}

add_filter(
	'wpcf7_spam',
	function ( $spam, $submission ) {
		if ( $spam ) {
			return $spam;
		}

		// 1. Trampa de tiempo.
		$ts = isset( $_POST[ SKEMA_CF7_TS_FIELD ] ) ? (int) ( (int) $_POST[ SKEMA_CF7_TS_FIELD ] / 1000 ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! $ts ) {
			return skema_cf7_flag( $submission, 'Sin marca de tiempo (JS no ejecutado o POST directo).' );
		}
		$elapsed = time() - $ts;
		if ( $elapsed < SKEMA_CF7_MIN_SECONDS ) {
			return skema_cf7_flag( $submission, sprintf( 'Enviado demasiado rápido (%d s).', $elapsed ) );
		}
		if ( $elapsed > SKEMA_CF7_MAX_AGE ) {
			return skema_cf7_flag( $submission, 'Marca de tiempo caducada o falsa.' );
		}

		// 2. Límite por IP.
		$ip  = $submission->get_meta( 'remote_ip' );
		$key = 'skema_cf7_rl_' . md5( (string) $ip );
		$hit = (int) get_transient( $key );
		if ( $hit >= SKEMA_CF7_RATE_LIMIT ) {
			return skema_cf7_flag( $submission, 'Límite de envíos por IP superado.' );
		}
		set_transient( $key, $hit + 1, SKEMA_CF7_RATE_WINDOW );

		// 3. Heurísticas de contenido.
		$data = $submission->get_posted_data();
		$all  = implode( ' ', array_map( 'strval', array_filter( $data, 'is_scalar' ) ) );

		if ( preg_match_all( '#https?://|www\.#i', $all ) > SKEMA_CF7_MAX_LINKS ) {
			return skema_cf7_flag( $submission, 'Demasiados enlaces.' );
		}

		if ( preg_match( '/[\p{Cyrillic}\p{Han}\p{Hangul}\p{Arabic}\p{Thai}]/u', $all ) ) {
			return skema_cf7_flag( $submission, 'Alfabeto no esperado.' );
		}

		// Ajustar los nombres de campo a los reales de cada formulario.
		foreach ( array( 'your-name', 'nombre' ) as $name_field ) {
			if ( ! empty( $data[ $name_field ] ) && preg_match( '#https?://|www\.|@|\.com\b#i', $data[ $name_field ] ) ) {
				return skema_cf7_flag( $submission, 'URL o correo en el campo nombre.' );
			}
		}

		foreach ( array( 'your-email', 'correo', 'email' ) as $email_field ) {
			if ( empty( $data[ $email_field ] ) ) {
				continue;
			}
			$domain     = strtolower( substr( strrchr( $data[ $email_field ], '@' ), 1 ) );
			$disposable = array( 'mailinator.com', 'yopmail.com', 'guerrillamail.com', 'sharklasers.com', 'tempmail.com', 'temp-mail.org', '10minutemail.com', 'trashmail.com' );
			if ( in_array( $domain, $disposable, true ) ) {
				return skema_cf7_flag( $submission, 'Correo desechable.' );
			}
		}

		return $spam;
	},
	20,
	2
);
```

Notas:

- **Nombres de campos**: revisar en el editor de cada formulario CF7 los `name` reales (`your-name`, `nombre`, `telefono`…) y ajustarlos en el código.
- **Validación de teléfono**: es preferible hacerla como validación normal (que el usuario vea el error) con `wpcf7_validate_tel*` o con el atributo `pattern`/`minlength` del propio tag CF7, y no como spam silencioso.
- Si el sitio está detrás de Cloudflare o un proxy, `remote_ip` puede ser la IP del proxy; en ese caso el rate limit debe hacerse en el firewall (Capa 5).
- Los envíos marcados como spam **no envían correo**, pero Flamingo los guarda en la pestaña *Spam* con el motivo: así se revisan falsos positivos.

### Capa 5 — Firewall / rate limiting del endpoint REST

Como los bots atacan directamente `/wp-json/contact-form-7/v1/contact-forms/*/feedback`:

- **Wordfence → Firewall → Rate Limiting**: limitar peticiones por minuto de «humanos» y «crawlers», y bloquear países que no son mercado objetivo si el spam viene mayoritariamente de ahí (*Wordfence Premium* para bloqueo por país).
- **Si se usa Cloudflare** (recomendado): regla WAF con *Rate limiting* sobre la ruta `/wp-json/contact-form-7/` (p. ej. 5 peticiones / 1 min por IP) y *Managed Challenge* para tráfico de países fuera del mercado objetivo hacia esa ruta.
- Evitar duplicar funciones entre Wordfence y All In One WP Security: dejar el firewall en uno solo para no tener conflictos ni bloqueos difíciles de depurar.

### Capa 6 — Higiene de correo (entregabilidad)

No reduce el spam, pero evita daños colaterales:

- En CF7 → pestaña *Correo*: el **De** (`From`) debe ser del propio dominio (`no-reply@dominio`), nunca `[your-email]`; usar `Reply-To: [your-email]`.
- Desactivar el **correo de respuesta automática al usuario** (Correo 2) o no incluir el mensaje del usuario en él: los spammers lo usan para enviar spam a terceros a través del sitio.
- WP Mail SMTP ya está instalado: mantener SPF, DKIM y DMARC correctos.

---

## 3. Plan de implementación recomendado

| Orden | Acción | Esfuerzo | Impacto |
|---|---|---|---|
| 1 | Capa 0: analizar spam en Flamingo | 30 min | Define reglas |
| 2 | Activar honeypot con time check en **todos** los formularios (incluidas landings) | 30 min | Medio |
| 3 | Lista de palabras no permitidas | 15 min | Medio |
| 4 | Migrar de `wpcf7-recaptcha` (v2) a **Turnstile** nativo de CF7 | 1 h | Alto |
| 5 | `inc/cf7-antispam.php` (tiempo + IP + heurísticas) | 2–3 h con pruebas | Alto |
| 6 | Rate limiting del endpoint REST (Wordfence o Cloudflare) | 30 min | Alto contra bots |
| 7 | Revisar configuración de Correo / Correo 2 | 15 min | Previene abuso |
| 8 | Monitorear Flamingo 1–2 semanas y ajustar reglas | continuo | — |

## 4. Pruebas después de implementar

1. Enviar cada formulario como usuario real (escritorio y móvil, con y sin autocompletado del navegador) → debe llegar el correo.
2. Enviar con un enlace en el mensaje → debe llegar (límite = 1); con 2 enlaces → spam.
3. Enviar inmediatamente tras cargar con autocompletado → comprobar que no se marca como spam por tiempo (ajustar `SKEMA_CF7_MIN_SECONDS` si hace falta).
4. POST directo al endpoint con `curl` sin `_skema_ts` → spam.
5. 4 envíos seguidos desde la misma IP → el 4.º marcado como spam.
6. Purgar caché de WP Rocket y LiteSpeed y repetir la prueba 1 (verificar que nada depende del HTML cacheado).
7. Revisar la pestaña *Spam* de Flamingo durante la primera semana para detectar falsos positivos.

## 5. Riesgos y consideraciones

- **Falsos positivos**: cada regla nueva puede bloquear a un cliente real. Por eso todo se registra con motivo en Flamingo y conviene revisarlo semanalmente al principio.
- **Optimización JS de WP Rocket/LiteSpeed** (*Delay JS* / *Defer*): puede retrasar la ejecución del script de la trampa de tiempo o de Turnstile hasta la interacción. Si aparecen falsos positivos «Sin marca de tiempo», excluir el script inline y los de CF7/Turnstile de *Delay JavaScript execution*.
- **Formularios de landings**: al pegar un shortcode nuevo desde ACF, el formulario CF7 correspondiente debe tener ya el honeypot y Turnstile; documentarlo para quien administre las landings.
- **Spam manual (humano)**: ninguna medida técnica lo elimina al 100 %. La lista de palabras, los límites de enlaces y Akismet son lo que más ayuda contra ese tipo.
