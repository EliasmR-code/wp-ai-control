import { randomUUID } from 'node:crypto';
import { StreamableHTTPServerTransport } from '@modelcontextprotocol/sdk/server/streamableHttp.js';
import { isInitializeRequest } from '@modelcontextprotocol/sdk/types.js';
import { createMcpExpressApp } from '@modelcontextprotocol/sdk/server/express.js';
import { createWpaicMcpServer } from './create-wpaic-server.js';

const MCP_PATH = process.env.MCP_PATH || '/mcp';

/**
 * Express + Streamable HTTP MCP (Railway, Fly, etc.).
 */
export function createHttpMcpApp() {
  const allowedHosts = process.env.MCP_ALLOWED_HOSTS?.split(',').map((s) => s.trim()).filter(Boolean);
  const app =
    allowedHosts?.length > 0
      ? createMcpExpressApp({ host: '0.0.0.0', allowedHosts })
      : createMcpExpressApp({ host: '0.0.0.0' });
  const transports = {};

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

  const mcpPostHandler = async (req, res) => {
    const sessionId = req.headers['mcp-session-id'];
    try {
      let transport;
      if (sessionId && transports[sessionId]) {
        transport = transports[sessionId];
      } else if (!sessionId && isInitializeRequest(req.body)) {
        transport = new StreamableHTTPServerTransport({
          sessionIdGenerator: () => randomUUID(),
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
        await transport.handleRequest(req, res, req.body);
        return;
      } else {
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
    if (!sid || !transports[sid]) {
      res.status(400).send('Invalid or missing session ID');
      return;
    }
    await transports[sid].handleRequest(req, res);
  };

  const mcpDeleteHandler = async (req, res) => {
    const sid = req.headers['mcp-session-id'];
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
