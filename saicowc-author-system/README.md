# SaicoWC Author Follow & Badges

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)
![WooCommerce](https://img.shields.io/badge/WooCommerce-5.0%2B-purple.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPL%20v2-green.svg)

Sistema profesional de seguir autores y gamificación con badges para WooCommerce. Diseñado específicamente para integrarse perfectamente con el theme SaicoWC.

**Autor:** Wilber Saico
**Web:** [wsaico.com](https://wsaico.com)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Uso](#-uso)
- [Shortcodes](#-shortcodes)
- [Widgets](#-widgets)
- [Hooks](#-hooks-para-desarrolladores)
- [FAQ](#-preguntas-frecuentes)
- [Changelog](#-changelog)

---

## ✨ Características

### Sistema de Seguir Autores
- **Seguir/Dejar de seguir** autores tipo Facebook/Instagram
- **Botones inteligentes** con estados: Seguir, Siguiendo, Dejar de seguir
- **Contador de seguidores** en tiempo real
- **Notificaciones por email** cuando autores seguidos publican productos
- **Sin tablas personalizadas** - Todo en `user_meta` para máxima compatibilidad

### Sistema de Gamificación
- **5 Niveles de Badges:**
  - 🥉 **Bronce** (0-50 pts): Autor Novato
  - 🥈 **Plata** (51-200 pts): Autor Establecido
  - 🥇 **Oro** (201-500 pts): Autor Destacado
  - 💎 **Platino** (501-1000 pts): Autor Elite
  - 💠 **Diamante** (1001+ pts): Autor Leyenda

- **Puntos por Acciones** (100% configurable):
  - Publicar producto: +10 puntos
  - Producto vendido: +5 puntos
  - Nuevo seguidor: +2 puntos
  - Producto destacado: +15 puntos

- **Badges SVG elegantes** con animaciones sutiles
- **Barra de progreso** al siguiente nivel
- **Notificación por email** al subir de nivel

### Integración con Theme SaicoWC
- **Single Product:** Badge y botón en `stats-author-info`
- **Página de Autor:** Panel completo con stats y progreso
- **Detección automática** de ubicaciones del theme
- **Fallback inteligente** si hooks no existen
- **Respeta estilos** del theme padre

### Panel de Administración Profesional
- **Dashboard completo** con estadísticas generales
- **Top Autores** por seguidores y puntos
- **Configuración visual** con tabs organizados
- **Perfil de usuario** muestra stats del autor
- **SMTP opcional** para emails (compatible con Brevo, Gmail, etc.)

---

## 🎯 Requisitos

- ✅ WordPress 5.8 o superior
- ✅ PHP 7.4 o superior
- ✅ WooCommerce 5.0 o superior
- ✅ Theme SaicoWC (recomendado)

---

## 📦 Instalación

### Método 1: Instalación Manual

1. **Clonar o copiar** el plugin en `/wp-content/plugins/`:
   ```bash
   cd /wp-content/plugins/
   cp -r saicowc-author-system ./
   ```

2. **Activar el plugin** desde WordPress:
   - Ir a **Plugins** → **Plugins Instalados**
   - Buscar **SaicoWC Author Follow & Badges**
   - Click en **Activar**

3. **Verificar requisitos:**
   - El plugin verificará automáticamente WordPress, PHP y WooCommerce
   - Si falta algún requisito, mostrará un aviso en el admin

### Método 2: Instalación vía ZIP

1. **Comprimir** el directorio del plugin:
   ```bash
   zip -r saicowc-author-system.zip saicowc-author-system/
   ```

2. **Subir desde WordPress:**
   - **Plugins** → **Añadir nuevo** → **Subir plugin**
   - Seleccionar `saicowc-author-system.zip`
   - Click en **Instalar ahora**
   - Click en **Activar**

---

## ⚙️ Configuración

### Configuración Inicial

1. **Ir al menú del plugin:**
   - **Author System** → **Configuración**

2. **Tab: Puntos**
   - Configurar puntos por cada acción
   - Por defecto: Publicar (10), Vender (5), Seguidor (2), Destacado (15)

3. **Tab: Notificaciones**
   - ✓ Habilitar sistema de notificaciones
   - ✓ Email cuando autor seguido publica producto
   - ✓ Email cuando autor sube de nivel

4. **Tab: SMTP (Opcional)**
   - Configurar servidor SMTP para envío profesional
   - Compatible con Brevo, Gmail, SendGrid, etc.
   - Ejemplo Brevo:
     ```
     Host: smtp-relay.brevo.com
     Puerto: 587
     Usuario: tu-email@dominio.com
     Contraseña: tu-api-key
     ```

5. **Tab: Badges**
   - Vista informativa de los 5 niveles
   - Iconos SVG renderizados inline
   - No requiere configuración (personalizable vía código)

### Configuración de Colores (Opcional)

Añadir en el `functions.php` del child theme o Customizer:

```php
// Cambiar colores del plugin
add_filter('saicowc_author_colors', function($colors) {
    $colors['primary'] = '#your-color';
    return $colors;
});
```

---

## 🚀 Uso

### Para Usuarios

#### Seguir a un Autor

1. **En Single Product:**
   - Ver el botón "Seguir" junto al nombre del autor
   - Click para seguir
   - El botón cambia a "Siguiendo"

2. **En Página de Autor:**
   - Ver panel completo con badge, stats y progreso
   - Click en "Seguir" para recibir notificaciones

3. **Dejar de Seguir:**
   - Hover sobre "Siguiendo" → cambia a "Dejar de seguir"
   - Click para confirmar

#### Ver Autores Seguidos

- **Menú de Usuario** → **Mi Cuenta**
- **Sidebar** → Widget "Mis Autores Seguidos"
- **Shortcode:** `[saicowc_following_list]`

### Para Autores

#### Ganar Puntos y Subir de Nivel

1. **Publicar Productos:** +10 puntos por producto
2. **Vender Productos:** +5 puntos por venta
3. **Conseguir Seguidores:** +2 puntos por seguidor
4. **Producto Destacado:** +15 puntos (opcional)

#### Ver Mis Stats

- **Panel de Usuario** → Editar mi perfil
- Sección **Author System - Estadísticas**
- Ver badge actual, puntos, seguidores, progreso

---

## 📝 Shortcodes

### `[saicowc_follow_button]`

Muestra el botón de seguir para un autor específico.

**Parámetros:**
- `author_id` (requerido): ID del autor

**Ejemplo:**
```php
[saicowc_follow_button author_id="5"]
```

---

### `[saicowc_author_badge]`

Muestra el badge de un autor.

**Parámetros:**
- `author_id` (requerido): ID del autor
- `size` (opcional): Tamaño en px (default: 32)

**Ejemplo:**
```php
[saicowc_author_badge author_id="5" size="48"]
```

---

### `[saicowc_author_stats]`

Muestra las estadísticas completas de un autor.

**Parámetros:**
- `author_id` (opcional): ID del autor (default: usuario actual)

**Ejemplo:**
```php
[saicowc_author_stats author_id="5"]
```

**Output:**
- Seguidores
- Puntos
- Productos
- Badge y progreso

---

### `[saicowc_following_list]`

Muestra la lista de autores que sigue el usuario actual.

**Ejemplo:**
```php
[saicowc_following_list]
```

**Nota:** Requiere que el usuario esté logueado.

---

### `[saicowc_top_authors]`

Muestra el ranking de top autores.

**Parámetros:**
- `limit` (opcional): Número de autores (default: 10, max: 50)

**Ejemplo:**
```php
[saicowc_top_authors limit="20"]
```

---

## 🧩 Widgets

### Widget: Top Autores

Muestra los autores con más seguidores.

**Configuración:**
- Título personalizable
- Número de autores (1-20)

**Ubicaciones sugeridas:**
- Sidebar principal
- Footer

---

### Widget: Mis Autores Seguidos

Muestra los autores que sigue el usuario actual.

**Configuración:**
- Título personalizable

**Nota:** Solo visible para usuarios logueados.

---

### Widget: Productos de Autores Seguidos

Muestra productos recientes de autores que sigue el usuario.

**Configuración:**
- Título personalizable
- Número de productos (1-10)

---

## 🔧 Hooks para Desarrolladores

### Actions

#### `saicowc_author_followed`
Se ejecuta cuando un usuario sigue a un autor.

```php
do_action('saicowc_author_followed', $user_id, $author_id);
```

**Parámetros:**
- `$user_id` (int): ID del usuario
- `$author_id` (int): ID del autor

**Ejemplo:**
```php
add_action('saicowc_author_followed', function($user_id, $author_id) {
    // Tu código aquí
}, 10, 2);
```

---

#### `saicowc_author_unfollowed`
Se ejecuta cuando un usuario deja de seguir a un autor.

```php
do_action('saicowc_author_unfollowed', $user_id, $author_id);
```

---

#### `saicowc_author_points_added`
Se ejecuta cuando se añaden puntos a un autor.

```php
do_action('saicowc_author_points_added', $author_id, $points, $new_total, $reason);
```

**Parámetros:**
- `$author_id` (int): ID del autor
- `$points` (int): Puntos añadidos
- `$new_total` (int): Puntos totales
- `$reason` (string): Razón (publish_product, product_sold, new_follower)

---

#### `saicowc_author_level_up`
Se ejecuta cuando un autor sube de nivel.

```php
do_action('saicowc_author_level_up', $author_id, $new_level, $old_level, $points);
```

**Parámetros:**
- `$author_id` (int): ID del autor
- `$new_level` (string): Nuevo nivel (bronze, silver, gold, platinum, diamond)
- `$old_level` (string): Nivel anterior
- `$points` (int): Puntos totales

---

### Filters

#### `saicowc_author_template_args`
Modifica los argumentos pasados a los templates.

```php
apply_filters('saicowc_author_template_args', $args, $template_name);
```

**Ejemplo:**
```php
add_filter('saicowc_author_template_args', function($args, $template_name) {
    if ($template_name === 'follow-button') {
        // Modificar args
    }
    return $args;
}, 10, 2);
```

---

## 📊 Estructura de Datos

### User Meta Keys

- `_saicowc_following_authors`: Array de IDs de autores que sigue
- `_saicowc_author_followers`: Array de IDs de seguidores
- `_saicowc_author_points`: Puntos totales del autor
- `_saicowc_author_badge_level`: Nivel de badge actual
- `_saicowc_notify_preferences`: Preferencias de notificación

### Options

- `saicowc_author_settings`: Configuración del plugin
- `saicowc_author_version`: Versión instalada
- `saicowc_author_notification_queue`: Cola de notificaciones

### Transients (Caché)

- `top_authors_{limit}`: Top autores (1 hora)
- Varios caché en `wp_cache` para mejor performance

---

## ❓ Preguntas Frecuentes

### ¿Funciona sin el theme SaicoWC?

Sí, el plugin funciona con cualquier theme de WooCommerce. La integración específica con SaicoWC es opcional y mejora la experiencia visual.

### ¿Crea tablas en la base de datos?

No. Todo se almacena en `user_meta` de WordPress para máxima compatibilidad y facilidad de mantenimiento.

### ¿Cómo personalizo los badges?

Los badges son SVG inline generados por PHP. Puedes modificarlos en `includes/class-gamification.php` o usar un filtro:

```php
add_filter('saicowc_author_badge_svg', function($svg, $level, $size) {
    // Tu SVG personalizado
    return $custom_svg;
}, 10, 3);
```

### ¿Puedo cambiar los rangos de puntos de los badges?

Sí, edita la propiedad `$badge_levels` en `includes/class-gamification.php` o usa un filtro en activación.

### ¿Las notificaciones funcionan automáticamente?

Sí, las notificaciones se envían via cron de WordPress. Se encolan para evitar timeouts y se envían cada hora.

### ¿Cómo configuro SMTP?

Ve a **Author System** → **Configuración** → **Tab SMTP**. El plugin es compatible con Brevo, Gmail, SendGrid y cualquier servidor SMTP estándar.

---

## 🔒 Seguridad

- ✅ **Nonces** en todas las peticiones AJAX
- ✅ **Sanitización** completa de datos de entrada
- ✅ **Validación** de permisos de usuario
- ✅ **Escapado** correcto en output
- ✅ **Prevención** de SQL injection
- ✅ **Cumple** WordPress Coding Standards

---

## 🎨 Personalización CSS

Variables CSS disponibles:

```css
:root {
    --saicowc-primary: #667eea;
    --saicowc-primary-dark: #5568d3;
    --saicowc-secondary: #764ba2;
    --saicowc-success: #10b981;
    --saicowc-danger: #ef4444;
    --saicowc-text: #1f2937;
    --saicowc-text-light: #6b7280;
    --saicowc-border: #e5e7eb;
    --saicowc-bg: #f9fafb;
    --saicowc-radius: 8px;
}
```

Personaliza añadiendo en tu CSS:

```css
.saicowc-follow-button {
    background: linear-gradient(135deg, #your-color-1, #your-color-2);
}
```

---

## 📈 Performance

- **Caché optimizado** con `wp_cache` y transients
- **Lazy loading** de componentes
- **Queries optimizadas** sin N+1
- **AJAX eficiente** sin reloads
- **Assets minificados** (production)

---

## 🌐 i18n (Traducción)

El plugin está listo para traducción:

**Text Domain:** `saicowc-author`
**Domain Path:** `/languages`

Para traducir:

1. Usar **Poedit** o **Loco Translate**
2. Crear archivo `.po` para tu idioma
3. Compilar a `.mo`
4. Guardar en `/languages/`

---

## 📞 Soporte

**Autor:** Wilber Saico
**Web:** [wsaico.com](https://wsaico.com)
**Email:** contacto@wsaico.com
**GitHub:** [github.com/wsaico](https://github.com/wsaico)

---

## 📄 Licencia

GPL v2 or later

```
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 🎯 Changelog

### Version 1.0.0 (2025-01-17)
- 🎉 Lanzamiento inicial
- ✨ Sistema de seguir autores
- ⭐ Sistema de puntos y 5 niveles de badges
- 📧 Notificaciones por email
- 🎨 Integración con theme SaicoWC
- 🛠️ Panel de administración profesional
- 📱 Responsive y mobile-friendly
- 🌙 Soporte para dark mode
- 🔒 Seguridad completa (nonces, sanitización)
- 📊 Shortcodes y widgets
- 🚀 Performance optimizado

---

## 🙏 Créditos

Desarrollado con ❤️ por **Wilber Saico** para la comunidad WordPress.

**Tecnologías utilizadas:**
- WordPress 5.8+
- WooCommerce 5.0+
- PHP 7.4+
- JavaScript (ES6+)
- CSS3 (Grid, Flexbox, Variables)
- SVG inline para badges

---

**¡Gracias por usar SaicoWC Author Follow & Badges!** ⭐
