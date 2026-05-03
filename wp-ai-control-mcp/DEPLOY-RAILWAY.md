# Deploy a Railway.app

## Pasos

### 1. Subir a GitHub
```bash
cd wp-ai-control-mcp
git init
git add .
git commit -m "MCP Server"
# Crear repo en GitHub y push
```

### 2. Deploy en Railway
1. Ir a [railway.app](https://railway.app)
2. Login con GitHub
3. "New Project" → "Deploy from GitHub repo"
4. Seleccionar el repo
5. Variables de entorno:
   - `WP_URL`: Tu WordPress URL
   - `WP_API_KEY`: Tu API key del plugin
6. "Deploy"

### 3. Obtener URL
Railway te dará una URL como: `https://wp-ai-control-mcp.up.railway.app`

### 4. Configurar cliente MCP

**Para Cursor/Claude Desktop:**
```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "npx",
      "args": ["-y", "wp-ai-control-mcp"],
      "env": {
        "WP_URL": "https://tu-wordpress.com/wp-json/wp-ai-control/v1",
        "WP_API_KEY": "tu-key"
      }
    }
  }
}
```

**O usar como HTTP server (agregar Express):**
```bash
npm install express
```

## Costo

- **Hobby**: $5/mes
- **Pro**: $20/mes

## Notas

- MCP funciona mejor como stdio para desarrollo local
- Railway útil si necesitas HTTP endpoint público
- El plugin WordPress debe estar instalado y configurado