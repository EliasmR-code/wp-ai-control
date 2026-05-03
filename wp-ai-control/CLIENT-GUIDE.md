# WP AI Control - Guía del Cliente

## Instalación

1. Ve a **Plugins → Añadir nuevo → Subir plugin**
2. Sube el archivo `wp-ai-control.zip`
3. Activa el plugin

## Configuración

1. Ve a **Ajustes → WP AI Control**
2. Verifica la conexión con el botón "Test API Connection"
3. Tu API endpoint será: `https://tusitio.com/wp-json/wp-ai-control/v1/`

## Generar API Key

En la misma página de ajustes:
1. La API key se genera automáticamente en el primer uso
2. Copia tu API key

## Uso con IA (Cursor, Claude, etc.)

### Opción 1: MCP Server Local

Instala el MCP en tu servidor o PC:

```bash
# Configura las variables
export WP_URL="https://tusitio.com/wp-json/wp-ai-control/v1"
export WP_API_KEY="tu-api-key-aqui"

# Ejecuta el MCP
npm start
```

### Opción 2: Railway (Cloud)

1. Deploy del MCP a Railway
2. Configura las variables de entorno con tu URL y API key
3. Usa la URL de Railway en tu IA

## Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/site-info` | Info del sitio |
| GET | `/posts` | Listar posts |
| POST | `/posts` | Crear post |
| GET | `/pages` | Listar páginas |
| GET | `/media` | Listar medios |
| POST | `/media` | Subir medio |
| GET | `/users` | Listar usuarios |
| GET | `/categories` | Listar categorías |
| GET | `/tags` | Listar etiquetas |
| GET | `/comments` | Listar comentarios |
| GET | `/themes` | Listar temas |
| GET | `/plugins` | Listar plugins |
| GET | `/menus` | Listar menús |

## Autenticación

Usa el header:
```
Authorization: Bearer TU_API_KEY
```

## Soporte

¿Problemas? Revisa:
- WordPress debug.log
- Consola del navegador (F12)
- Error logs del servidor