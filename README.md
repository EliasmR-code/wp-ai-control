# WP AI Control - Project Overview

## Resumen

Infraestructura AI para WordPress con plugin, servidor MCP, CLI y 43 skills de automatización.

## Estructura

```
wp-ai-control/           → Plugin WordPress (166 tools)
wp-ai-control-mcp/       → Servidor MCP (Node.js)
wp-ai-control-cli/       → CLI Tool (27+ comandos)
wp-ai-control-skills/    → Skills Library (43 skills)
```

## Componentes

### 1. Plugin WordPress (wp-ai-control)
- **Ubicación**: Directorio `wp-content/plugins/`
- **API REST**: `/wp-json/wp-ai-control/v1/*`
- **Herramientas**: 166 herramientas en PHP
- **Categorías**:
  - Posts, Pages, Media, Comments (CRUD)
  - Users, Roles, Capabilities
  - WooCommerce (21 tools)
  - ACF (54 custom fields)
  - Widgets (27)
  - Theme/Plugin management
  - 12 Page Builders (Gutenberg, Elementor, Divi, WPBakery, Bricks, Oxygen, Beaver, Brizy, Thrive, Breakdance, Flatsome, Kadence, Kadence Blocks)

### 2. MCP Server (wp-ai-control-mcp)
- **Tipo**: Node.js + MCP SDK (stdio local o **Streamable HTTP** en `/mcp` en Railway)
- **Multi-tenant**: cada tool call lleva su propio `site_url` + `api_key`; el servidor es un puente sin estado.
- **Producción recomendada**: desplegar en **Railway** (`Root Directory` = `wp-ai-control-mcp`); Cursor usa `url` → `https://…/mcp`
- **Desarrollo local**: `MCP_TRANSPORT=stdio` (por defecto fuera de Railway) o `node index.js` vía stdio

### 3. CLI Tool (wp-ai-control-cli)
- **Comandos**: `wpai posts`, `wpai pages`, `wpai run`, WooCommerce, ACF, etc. (`wpai --help`)
- **Instalación**: `npm install -g` desde la carpeta del paquete, o `npm link`

### 4. Skills Library (wp-ai-control-skills)
- **Skills**: 43 automatizaciónes
- **Categorías**: AI Content, SEO, WooCommerce, Security, Monitoring, Dev, Migration

## Quick Start

### 1. Instalar Plugin
```bash
# Copiar a wp-content/plugins/
cp -r wp-ai-control /path/to/wp/wp-content/plugins/
# Activar en WordPress Admin
```

### 2. Configurar API Key
- WordPress Admin → WP AI Control → Settings
- Generar API key
- Guardar para uso posterior

### 3. MCP en Railway + Cursor

1. Sigue **`wp-ai-control-mcp/DEPLOY-RAILWAY.md`**: servicio con raíz `wp-ai-control-mcp`, variables `WP_URL` y `WP_API_KEY`, dominio público.
2. En tu PC, define la variable de entorno **`WPAIC_MCP_URL`** con la URL HTTPS del MCP **incluyendo `/mcp`** (ej. `https://tu-servicio.up.railway.app/mcp`).
3. El repo incluye **`.cursor/mcp.json`** con `"url": "${env:WPAIC_MCP_URL}"` para que Cursor apunte al despliegue.

Comprueba el servicio: `GET …/health` debe devolver `{"ok":true,"service":"wp-ai-control-mcp"}`.

### 4. MCP local (solo desarrollo)

```bash
npm run bootstrap:mcp
# Editar wp-ai-control-mcp/.env (WP_URL, WP_API_KEY)
```

Ejecutar con stdio (por defecto si no estás en Railway):

```bash
cd wp-ai-control-mcp
npm install
set MCP_TRANSPORT=stdio
node index.js
```

En Cursor puedes usar en su lugar `"command": "node"` y `"args": ["/ruta/al/repo/wp-ai-control-mcp/index.js"]` con `env` o `envFile`. No uses `npx -y wp-ai-control-mcp` en el registry público.

### 5. CLI
npm install
wpai --help
```

## Uso con IAs

### Cursor (recomendado: MCP en Railway)

Define **`WPAIC_MCP_URL`** en el entorno de tu sistema (Windows: variables de entorno de usuario) con la URL pública del MCP, por ejemplo `https://tu-servicio.up.railway.app/mcp`. El archivo **`.cursor/mcp.json`** del repo usa `"url": "${env:WPAIC_MCP_URL}"`.

### Cursor / Claude (stdio local, desarrollo)

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/path/to/wp-ai-control-mcp/index.js"],
      "env": {
        "WP_URL": "https://tu-wordpress.com",
        "WP_API_KEY": "tu-key"
      }
    }
  }
}
```

También puedes usar `"envFile": "/path/to/wp-ai-control-mcp/.env"` en lugar de `env`.

### Línea de comandos (CLI `wpai`)
```bash
cd wp-ai-control-cli && npm install
wpai setup
wpai posts -s "noticias"
wpai post-create -t "Título" -c "Contenido..."
wpai pages
wpai page-create -t "Nueva página" -c "Contenido..."
wpai media
wpai media-upload --url "https://ejemplo.com/imagen.png" --filename "hero.png" --alt "Descripción"
wpai run "/wc/orders" -m GET
```
*(Rutas como `backup/create` en documentación antigua no forman parte del plugin; el MCP `upload-media` usa `POST /media/upload`.)*

## Variables de Entorno

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| WP_URL | Raíz del sitio **o** URL hasta `/wp-json/wp-ai-control/v1` (MCP acepta ambas; CLI recomienda solo la raíz) | `https://example.com` |
| WP_API_KEY | API key del plugin (header `Authorization: Bearer`) | `wpaic_xxxxx` |

## Herramientas Principales (Plugin)

### Posts
- `wpai_posts_create` - Crear post
- `wpai_posts_list` - Listar posts
- `wpai_posts_update` - Actualizar post
- `wpai_posts_delete` - Eliminar post
- `wpai_posts_search` - Buscar posts

### Media
- `wpai_media_upload` - Subir archivo
- `wpai_media_list` - Listar medios
- `wpai_media_delete` - Eliminar medio

### Users
- `wpai_users_create` - Crear usuario
- `wpai_users_list` - Listar usuarios
- `wpai_users_update` - Actualizar usuario

### WooCommerce
- `wpai_woocommerce_products_create` - Crear producto
- `wpai_woocommerce_orders_list` - Listar pedidos
- `wpai_woocommerce_products_update_stock` - Actualizar stock

### ACF
- `wpai_acf_field_create` - Crear campo ACF
- `wpai_acf_group_create` - Crear grupo de campos
- `wpai_acf_field_update` - Actualizar campo

## API Endpoints (ejemplos reales del plugin)

```
GET    /wp-json/wp-ai-control/v1/posts
POST   /wp-json/wp-ai-control/v1/posts
GET    /wp-json/wp-ai-control/v1/pages
POST   /wp-json/wp-ai-control/v1/pages
POST   /wp-json/wp-ai-control/v1/media/upload
GET    /wp-json/wp-ai-control/v1/media
GET    /wp-json/wp-ai-control/v1/users
POST   /wp-json/wp-ai-control/v1/bulk-update-posts
GET    /wp-json/wp-ai-control/v1/wc/products
GET    /wp-json/wp-ai-control/v1/wc/orders
GET    /wp-json/wp-ai-control/v1/menus
GET    /wp-json/wp-ai-control/v1/plan-info
GET    /wp-json/wp-ai-control/v1/site-info
```

## Seguridad

- Autenticación via API Key (Bearer token)
- Rate limiting configurado
- Validación de permisos de usuario WP
- Nonces de WordPress

## Page Builders Soportados

1. Gutenberg (core)
2. Elementor
3. Divi
4. WPBakery
5. Bricks
6. Oxygen
7. Beaver Builder
8. Brizy
9. Thrive Architect
10. Breakdance
11. Flatsome
12. Kadence Theme
13. Kadence Blocks

## Deployment

### Railway (MCP Server)
1. Push a GitHub
2. New Project → Deploy from GitHub
3. Variables: WP_URL, WP_API_KEY
4. Costo: $5/mes

### WordPress
- Hosting estándar con PHP 8.0+
- MySQL 5.7+

## Dependencias

**Plugin (PHP):**
- WordPress 6.0+
- WooCommerce (opcional)
- ACF (opcional)

**MCP Server (Node.js):**
- Node.js 18+
- @modelcontextprotocol/sdk

**CLI (Node.js):**
- Node.js 18+
- commander, node-fetch, dotenv, ora

## Licencia

MIT

## Soporte

- WordPress Admin: Settings → WP AI Control
- Debug: Enable en settings
- Logs: wp-content/debug.log