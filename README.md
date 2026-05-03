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
- **Tipo**: Node.js con MCP SDK
- **Uso**: Conectar IAs (Claude, Cursor, etc.) a WordPress
- **Ejecución**: `npm start` o stdio

### 3. CLI Tool (wp-ai-control-cli)
- **Comandos**: wpai posts, wpai users, wpai backup, etc.
- **Instalación**: `npm install -g`

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

### 3. MCP Server (Local)
```bash
cd wp-ai-control-mcp
npm install
export WP_URL=https://tu-wordpress.com/wp-json/wp-ai-control/v1
export WP_API_KEY=tu-api-key
npm start
```

### 4. CLI
```bash
cd wp-ai-control-cli
npm install
wpai --help
```

## Uso con IAs

### Claude Desktop / Cursor
```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/path/to/wp-ai-control-mcp/index.js"],
      "env": {
        "WP_URL": "https://tu-wordpress.com/wp-json/wp-ai-control/v1",
        "WP_API_KEY": "tu-key"
      }
    }
  }
}
```

### Línea de comandos
```bash
wpai posts list
wpai pages create --title "Nueva Página" --content "Contenido..."
wpai media upload --file imagen.jpg
wpai backup create
```

## Variables de Entorno

| Variable | Descripción | Ejemplo |
|----------|-------------|---------|
| WP_URL | URL API WordPress | `https://example.com/wp-json/wp-ai-control/v1` |
| WP_API_KEY | API key del plugin | `wpaic_xxxxx` |

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

## API Endpoints

```
GET    /wp-json/wp-ai-control/v1/posts
POST   /wp-json/wp-ai-control/v1/posts
GET    /wp-json/wp-ai-control/v1/pages
POST   /wp-json/wp-ai-control/v1/media
GET    /wp-json/wp-ai-control/v1/users
POST   /wp-json/wp-ai-control/v1/woocommerce/products
GET    /wp-json/wp-ai-control/v1/woocommerce/orders
POST   /wp-json/wp-ai-control/v1/backup/create
GET    /wp-json/wp-ai-control/v1/security/scan
POST   /wp-json/wp-ai-control/v1/ai/content/generate
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
- axios

## Licencia

MIT

## Soporte

- WordPress Admin: Settings → WP AI Control
- Debug: Enable en settings
- Logs: wp-content/debug.log