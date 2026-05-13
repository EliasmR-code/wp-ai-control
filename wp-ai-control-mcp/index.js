#!/usr/bin/env node
import 'dotenv/config';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { createWpaicMcpServer } from './src/create-wpaic-server.js';
import { startHttpMcpServer } from './src/http-mcp.js';

function useHttpMode() {
  if (process.env.MCP_TRANSPORT === 'http') {
    return true;
  }
  if (process.env.MCP_TRANSPORT === 'stdio') {
    return false;
  }
  return Boolean(process.env.RAILWAY_ENVIRONMENT);
}

if (useHttpMode()) {
  if (process.env.WP_URL && process.env.WP_API_KEY) {
    console.error(
      '[wp-ai-control-mcp] HTTP: optional WP_URL/WP_API_KEY set (fallback when a tool omits site_url/api_key). ' +
        'For shared hosting, leave both unset so every user passes credentials per call.'
    );
  } else {
    console.error(
      '[wp-ai-control-mcp] HTTP: multi-tenant mode (recommended). Each tools/call must include site_url and api_key.'
    );
  }
  await startHttpMcpServer();
} else {
  const server = createWpaicMcpServer();
  const transport = new StdioServerTransport();
  await server.connect(transport);
}
