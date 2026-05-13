# Despliegue en Railway (MCP por HTTP)

El servidor MCP expone **Streamable HTTP** (MCP spec) en **`/mcp`**. Es **multi-tenant**: cada llamada a una tool incluye `site_url` y `api_key`, por lo que **no necesita** credenciales globales en Railway. Opcionalmente se pueden definir `WP_URL`/`WP_API_KEY` como fallback single-tenant.

## 1. Repositorio

Sube el monorepo a GitHub (o usa el repo existente). En Railway no hace falta un repo solo con la carpeta del MCP.

## 2. Nuevo servicio en Railway

1. [railway.app](https://railway.app) → **New Project** → **Deploy from GitHub repo**
2. Selecciona el repositorio.
3. En el servicio → **Settings** → **Root Directory**: `wp-ai-control-mcp`
4. Opcional: el repo incluye `railway.toml` con healthcheck en `/health` y arranque `npm start`.
5. **Build** / **Start**: Railway ejecutará `npm install` y `npm start` (ver `package.json`).

## 3. Variables de entorno

**MCP compartido (recomendado para “cualquier usuario”):** deja **sin definir** `WP_URL` y `WP_API_KEY`. Cada cliente (Cursor, etc.) pasa `site_url` y `api_key` en cada llamada a una tool. El servidor ya **no filtra** el listado de herramientas por el plan de un solo sitio.

**Servidor dedicado a un solo WordPress:** puedes definir `WP_URL` y `WP_API_KEY` como respaldo cuando una tool no envíe credenciales.

| Variable | Descripción |
|----------|-------------|
| `WP_URL` | Opcional. Fallback single-tenant. |
| `WP_API_KEY` | Opcional. API key del plugin (pareja con `WP_URL`). |
| `MCP_TRANSPORT` | `http` fuerza HTTP; `stdio` fuerza stdio. Si no está definido y existe `RAILWAY_ENVIRONMENT`, se usa HTTP. |
| `PORT` | Lo asigna Railway automáticamente. |
| `MCP_PATH` | Por defecto `/mcp`. |
| `MCP_ALLOWED_HOSTS` | **Recomendado en producción:** lista separada por comas del `Host` público (p. ej. `tu-app.up.railway.app`) para mitigar DNS rebinding. |
| `TRUST_PROXY` | Por defecto el servidor confía en el proxy (`1`). En `0` desactiva `trust proxy` de Express. |
| `WP_FETCH_TIMEOUT_MS` | Timeout hacia WordPress en ms (por defecto 60000, máximo 300000). |
| `WP_FETCH_USER_AGENT` | Cabecera `User-Agent` hacia WordPress (opcional). |

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
- Modo multi-tenant: cada cliente envía sus credenciales WordPress por tool call (`site_url`, `api_key`). En un MCP público **no** pongas `WP_URL`/`WP_API_KEY` en Railway salvo que el servicio sea solo para un sitio.
- Modo single-tenant (opcional): define `WP_URL` y `WP_API_KEY` en el servicio y el servidor las usará como fallback cuando una tool no especifique las suyas.
- Para desarrollo local sin Railway: `MCP_TRANSPORT=stdio` (por defecto fuera de Railway) y `node index.js` con stdio.
