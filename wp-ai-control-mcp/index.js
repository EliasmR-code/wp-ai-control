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
  if (!process.env.WP_URL || !process.env.WP_API_KEY) {
    console.error(
      '[wp-ai-control-mcp] Running in HTTP mode without default WP_URL/WP_API_KEY. ' +
        'Each tool call MUST supply site_url and api_key (multi-tenant mode).'
    );
  } else {
    console.error('[wp-ai-control-mcp] HTTP mode with default WP credentials from environment (single-tenant fallback).');
  }
  await startHttpMcpServer();
} else {
  const server = createWpaicMcpServer();
  const transport = new StdioServerTransport();
  await server.connect(transport);
}
