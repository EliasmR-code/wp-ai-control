# WP AI Control CLI

Interfaz de línea de comandos para el plugin **WP AI Control**: llama a la REST API (`/wp-json/wp-ai-control/v1`) con la API key en `Authorization: Bearer`.

## Requisitos

- Node.js 18+
- Plugin WP AI Control activo y API key válida (licencia según plan del sitio)

## Instalación

Desde esta carpeta:

```bash
npm install
npm link
```

O ejecutar sin instalar globalmente:

```bash
node bin/wpai.js --help
```

## Configuración

**`WP_URL`**: raíz del sitio (recomendado), por ejemplo `https://yoursite.com`. También puedes usar la URL completa hasta `.../wp-json/wp-ai-control/v1`; el cliente la normaliza.

**`WP_API_KEY`**: la clave del plugin (prefijo típico `wpaic_`).

Opciones:

1. Archivo **`.wpai`** en el directorio de trabajo (lo crea `wpai setup`):

```
WP_URL=https://yoursite.com
WP_API_KEY=wpaic_xxxxxxxx
```

2. Variables de entorno:

```bash
export WP_URL=https://yoursite.com
export WP_API_KEY=wpaic_xxxxxxxx
```

## Uso rápido

```bash
wpai setup
wpai site-info
wpai posts -s "news"
wpai post-create -t "Hello" -c "<p>Body</p>" --status draft
wpai pages
wpai page-create -t "About" -c "..." --status publish
wpai page-read 42
wpai run "/wc/orders" -m GET
wpai bulk-update-posts -i "10,11" -t "New title"
wpai media-upload --url "https://www.w3.org/Icons/w3c_home.png" --filename "w3c.png" --alt "W3C"
```

## Comandos (resumen)

| Área | Comandos |
|------|----------|
| Config | `setup` |
| Sitio | `site-info`, `builder-info` |
| Posts | `posts`, `post-read`, `post-create`, `post-update`, `post-delete`, `bulk-update-posts`, `bulk-delete-posts` |
| Páginas | `pages`, `page-read`, `page-create`, `page-update`, `page-delete`, `page-duplicate` |
| Usuarios | `users`, `user-create` (`-p` / `--password`, `-r` / `--role`) |
| Medios / comentarios | `media`, `media-upload` (URL remota), `comments`, `comment-approve` |
| Plugins / temas | `plugins`, `plugin-install`, `plugin-activate`, `plugin-deactivate`, `themes`, `theme-activate` |
| Menús | `menus`, `menu-read`, `menu-locations` |
| WooCommerce | `products`, `product-create` |
| Widgets | `widgets`, `sidebars` |
| ACF | `acf-field-groups`, `acf-fields` |
| Análisis | `analyze-seo`, `analyze-performance` |
| Genérico | `run <path>` con `-m` / `--method` y `-b` / `--body` (JSON) |

Los endpoints de análisis, WooCommerce, ACF o widgets solo existen si el plan del plugin los incluye y la licencia está activa.

## `run` (API arbitraria)

```bash
wpai run "/posts" -m GET
wpai run "/bulk-update-posts" -m POST -b "{\"post_ids\":[1],\"updates\":{\"status\":\"draft\"}}"
```

## Notas

- **Clave API en el admin** (`GET /auth/key`, `POST /auth/key`): exigen sesión de administrador en WordPress (`check_admin`), no bastan el Bearer y la API key del plugin. Para rotar la clave usa el panel de WP AI Control.
- **Subida de medios**: `POST /media/upload` importa desde una **URL http(s)** (`wpai media-upload --url ...`). No sustituye multipart desde el disco; para archivos locales puedes usar la REST de WordPress o subirlos a una URL accesible.

## Licencia

MIT
