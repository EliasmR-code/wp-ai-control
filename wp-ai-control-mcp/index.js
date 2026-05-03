#!/usr/bin/env node
import 'dotenv/config';
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from '@modelcontextprotocol/sdk/types.js';
import { wpFetch } from './src/wp-client.js';
import { tools } from './src/tools/index.js';

const server = new Server(
  { name: 'wp-ai-control-mcp', version: '1.0.0' },
  { capabilities: { tools: {} } }
);

server.setRequestHandler(ListToolsRequestSchema, async () => {
  return { tools };
});

server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args = {} } = request.params;

  const tool = tools.find((t) => t.name === name);
  if (!tool) {
    return {
      content: [{ type: 'text', text: `Tool ${name} not found.` }],
      isError: true,
    };
  }

  let path = tool._path;
  const method = tool._method;
  const pathParams = tool._pathParams || [];
  const bodyParams = tool._bodyParams || [];

  let pathArgs = { ...args };
  let queryArgs = {};
  let bodyArgs = {};

  for (const param of pathParams) {
    if (pathArgs[param] !== undefined) {
      path = path.replace(`{${param}}`, String(pathArgs[param]));
      delete pathArgs[param];
    }
  }

  for (const param of bodyParams) {
    if (pathArgs[param] !== undefined) {
      bodyArgs[param] = pathArgs[param];
      delete pathArgs[param];
    }
  }

  queryArgs = pathArgs;

  try {
    const result = await wpFetch(path, { method, body: bodyArgs, params: queryArgs });
    return {
      content: [{ type: 'text', text: JSON.stringify(result, null, 2) }],
    };
  } catch (error) {
    return {
      content: [{ type: 'text', text: error.message }],
      isError: true,
    };
  }
});

const transport = new StdioServerTransport();
await server.connect(transport);
