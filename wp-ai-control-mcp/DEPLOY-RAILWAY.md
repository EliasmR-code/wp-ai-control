# Despliegue en Railway (MCP por HTTP)

El servidor MCP expone **Streamable HTTP** (MCP spec) en **`/mcp`**. Es **multi-tenant**: cada llamada a una tool incluye `site_url` y `api_key`, por lo que **no necesita** credenciales globales en Railway. Opcionalmente se pueden definir `WP_URL`/`WP_API_KEY` como fallback single-tenant.

## 1. Repositorio

Sube el monorepo a GitHub (o usa el repo existente). En Railway no hace falta un repo solo con la carpeta del MCP.

## 2. Nuevo servicio en Railway

1. [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo**
2. Selecciona el repositorio.
3. En el servicio → **Settings** → **Root Directory**: `wp-ai-control-mcp`
4. **Build** / **Start**: Railway ejecutará `npm install` y `npm start` (ver `package.json`).

## 3. Variables de entorno

**Ninguna es obligatoria en modo multi-tenant.** El servidor arranca igual y cada cliente pasa sus credenciales por tool call.

Opcionales:

| Variable | Descripción |
|----------|-------------|
| `WP_URL` | Fallback single-tenant: raíz del sitio (ej. `https://tu-sitio.com`) o URL hasta `/wp-json/wp-ai-control/v1`. Si se define, se usa cuando una tool no manda `site_url`. |
| `WP_API_KEY` | Fallback single-tenant: API key del plugin (WP Admin → WP AI Control). Pareja con `WP_URL`. |
| `MCP_TRANSPORT` | `http` fuerza HTTP; `stdio` fuerza stdio (útil en Railway solo en casos raros). Si no está definido y existe `RAILWAY_ENVIRONMENT`, se usa HTTP. |
| `PORT` | Lo asigna Railway automáticamente. |
| `MCP_PATH` | Por defecto `/mcp`. |
| `MCP_ALLOWED_HOSTS` | Lista separada por comas de `Host` permitidos (protección DNS rebinding). Ej.: `tu-app.up.railway.app` |

Tras el deploy, anota la URL pública HTTPS, por ejemplo: `https://wp-ai-control-mcp-production-xxxx.up.railway.app`.

## 4. Comprobar salud

```text
GET https://TU-HOST/health
```

Respuesta esperada: `{"ok":true,"service":"wp-ai-control-mcp"}`.

## 5. Cursor (cliente)

En `.cursor/mcp.json` del proyecto (o en `~/.cursor/mcp.json`):

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "url": "https://TU-HOST.up.railway.app/mcp"
    }
  }
}
```

O con variable de entorno en tu máquina:

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "url": "${env:WPAIC_MCP_URL}"
    }
  }
}
```

Define `WPAIC_MCP_URL` con la URL completa **incluyendo** `/mcp`.

## 6. Claude Desktop

En `claude_desktop_config.json`, misma clave `url` apuntando a `https://.../mcp`.

## Coste y notas

- **Hobby** / **Pro**: según plan Railway.
- Modo multi-tenant: cada cliente envía sus credenciales WordPress por tool call (`site_url`, `api_key`). No expongas tokens globales si no es necesario.
- Modo single-tenant (opcional): define `WP_URL` y `WP_API_KEY` en el servicio y el servidor las usará como fallback cuando una tool no especifique las suyas.
- Para desarrollo local sin Railway: `MCP_TRANSPORT=stdio` (por defecto fuera de Railway) y `node index.js` con stdio.
