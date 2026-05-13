import { randomUUID } from 'node:crypto';
import { StreamableHTTPServerTransport } from '@modelcontextprotocol/sdk/server/streamableHttp.js';
import { isInitializeRequest } from '@modelcontextprotocol/sdk/types.js';
import { createMcpExpressApp } from '@modelcontextprotocol/sdk/server/express.js';
import { createWpaicMcpServer } from './create-wpaic-server.js';

const MCP_PATH = process.env.MCP_PATH || '/mcp';

/**
 * JSON batch responses avoid per-request SSE streams that trigger GET /mcp + session stickiness.
 * Default ON on Railway unless MCP_JSON_RESPONSE=0. Opt-in elsewhere with MCP_JSON_RESPONSE=1.
 */
function useJsonMcpResponses() {
  if (process.env.MCP_JSON_RESPONSE === '1') return true;
  if (process.env.MCP_JSON_RESPONSE === '0') return false;
  return Boolean(process.env.RAILWAY_ENVIRONMENT);
}

/**
 * Express + Streamable HTTP MCP (Railway, Fly, etc.).
 */
export function createHttpMcpApp() {
  const allowedHosts = process.env.MCP_ALLOWED_HOSTS?.split(',').map((s) => s.trim()).filter(Boolean);
  const app =
    allowedHosts?.length > 0
      ? createMcpExpressApp({ host: '0.0.0.0', allowedHosts })
      : createMcpExpressApp({ host: '0.0.0.0' });
  // Railway / reverse proxies: correct Host, IP, and optional MCP_ALLOWED_HOSTS checks
  if (process.env.TRUST_PROXY !== '0') {
    app.set('trust proxy', 1);
  }
  const transports = {};
  const enableJsonResponse = useJsonMcpResponses();

  app.get('/health', (_req, res) => {
    res.status(200).json({ ok: true, service: 'wp-ai-control-mcp' });
  });

  app.get('/', (_req, res) => {
    res.status(200).json({
      service: 'wp-ai-control-mcp',
      mcp: MCP_PATH,
      health: '/health',
    });
  });

  /** Cursor / clients sometimes hit the wrong path; return a clear hint (not a silent 404). */
  app.post('/', (_req, res) => {
    res.status(404).json({
      error: 'wrong_endpoint',
      message: `POST must target MCP path ${MCP_PATH} (example: https://your-host.up.railway.app${MCP_PATH})`,
    });
  });

  const mcpCorsHeaders = {
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, DELETE, OPTIONS',
    'Access-Control-Allow-Headers':
      'Content-Type, Accept, mcp-session-id, mcp-protocol-version, Authorization, Last-Event-ID',
    'Access-Control-Expose-Headers': 'mcp-session-id, mcp-protocol-version',
  };

  app.options(MCP_PATH, (_req, res) => {
    Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
    res.sendStatus(204);
  });

  const mcpPostHandler = async (req, res) => {
    const sessionId = req.headers['mcp-session-id'];
    try {
      let transport;
      if (sessionId && transports[sessionId]) {
        transport = transports[sessionId];
      } else if (!sessionId && isInitializeRequest(req.body)) {
        transport = new StreamableHTTPServerTransport({
          sessionIdGenerator: () => randomUUID(),
          enableJsonResponse,
          onsessioninitialized: (sid) => {
            transports[sid] = transport;
          },
        });
        transport.onclose = () => {
          const sid = transport.sessionId;
          if (sid && transports[sid]) {
            delete transports[sid];
          }
        };
        const server = createWpaicMcpServer();
        await server.connect(transport);
        Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
        await transport.handleRequest(req, res, req.body);
        return;
      } else {
        Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
        res.status(400).json({
          jsonrpc: '2.0',
          error: {
            code: -32000,
            message: 'Bad Request: No valid session ID provided',
          },
          id: null,
        });
        return;
      }
      Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
      await transport.handleRequest(req, res, req.body);
    } catch (error) {
      console.error('MCP POST error:', error);
      if (!res.headersSent) {
        res.status(500).json({
          jsonrpc: '2.0',
          error: {
            code: -32603,
            message: 'Internal server error',
          },
          id: null,
        });
      }
    }
  };

  const mcpGetHandler = async (req, res) => {
    const sid = req.headers['mcp-session-id'];
    Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
    if (!sid || !transports[sid]) {
      res.status(400).send('Invalid or missing session ID');
      return;
    }
    await transports[sid].handleRequest(req, res);
  };

  const mcpDeleteHandler = async (req, res) => {
    const sid = req.headers['mcp-session-id'];
    Object.entries(mcpCorsHeaders).forEach(([k, v]) => res.setHeader(k, v));
    if (!sid || !transports[sid]) {
      res.status(400).send('Invalid or missing session ID');
      return;
    }
    try {
      await transports[sid].handleRequest(req, res);
    } catch (error) {
      console.error('MCP DELETE error:', error);
      if (!res.headersSent) {
        res.status(500).send('Error processing session termination');
      }
    }
  };

  app.post(MCP_PATH, mcpPostHandler);
  app.get(MCP_PATH, mcpGetHandler);
  app.delete(MCP_PATH, mcpDeleteHandler);

  console.error(
    `[wp-ai-control-mcp] MCP JSON responses: ${enableJsonResponse} (set MCP_JSON_RESPONSE=0 to force SSE mode)`
  );

  return app;
}

export function startHttpMcpServer() {
  const port = parseInt(process.env.PORT || '3000', 10);
  const app = createHttpMcpApp();

  return new Promise((resolve, reject) => {
    const server = app.listen(port, '0.0.0.0', () => {
      console.error(`wp-ai-control-mcp Streamable HTTP on 0.0.0.0:${port}${MCP_PATH}`);
      resolve(server);
    });
    server.on('error', reject);
  });
}
