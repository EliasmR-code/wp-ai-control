import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
} from '@modelcontextprotocol/sdk/types.js';
import { wpFetch } from './wp-client.js';
import { tools } from './tools/index.js';

const ALWAYS_ALLOWED_FEATURES = new Set(['context', 'usage']);

function buildCapabilities(planInfo) {
  const features = new Set(Array.isArray(planInfo?.features) ? planInfo.features : []);
  const builders = new Set(Array.isArray(planInfo?.builders) ? planInfo.builders : []);
  return { features, builders, plan: planInfo?.plan || 'studio' };
}

function isToolAllowedByPlan(tool, capabilities) {
  const feature = tool._feature || 'content';
  if (ALWAYS_ALLOWED_FEATURES.has(feature)) {
    return true;
  }
  return capabilities.features.has(feature);
}

/**
 * Plan/capabilities for the site that will execute the tool (per-request or env fallback).
 * Used only when calling tools — not for ListTools, so a shared Railway MCP never filters
 * the catalog based on one tenant's plan.
 */
async function getCapabilities(site_url, api_key) {
  const url = site_url || process.env.WP_URL;
  const key = api_key || process.env.WP_API_KEY;
  if (!url || !key) {
    return null;
  }
  try {
    const planInfo = await wpFetch('/plan-info', {
      method: 'GET',
      params: {},
      site_url: url,
      api_key: key,
    });
    return buildCapabilities(planInfo);
  } catch {
    return null;
  }
}

function sanitizeTool(tool) {
  const { _feature, ...cleanTool } = tool;
  return cleanTool;
}

/**
 * New instance per MCP HTTP session (recommended by MCP SDK examples).
 */
export function createWpaicMcpServer() {
  const server = new Server(
    { name: 'wp-ai-control-mcp', version: '1.1.1' },
    { capabilities: { tools: {} } }
  );

  server.setRequestHandler(ListToolsRequestSchema, async () => {
    // Multi-tenant: always advertise the full tool catalog. Plan gating runs on each tools/call
    // using that caller's site_url + api_key (or optional WP_* env fallback).
    return { tools: tools.map(sanitizeTool) };
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

    const site_url = pathArgs.site_url;
    const api_key = pathArgs.api_key;
    delete pathArgs.site_url;
    delete pathArgs.api_key;

    const capabilities = await getCapabilities(site_url, api_key);
    if (capabilities && !isToolAllowedByPlan(tool, capabilities)) {
      return {
        content: [{ type: 'text', text: `Tool ${name} is not available in plan ${capabilities.plan}.` }],
        isError: true,
      };
    }

    if (
      capabilities &&
      tool._feature === 'builders' &&
      pathArgs.builder &&
      !capabilities.builders.has(String(pathArgs.builder))
    ) {
      return {
        content: [{ type: 'text', text: `Builder ${pathArgs.builder} is not available in plan ${capabilities.plan}.` }],
        isError: true,
      };
    }

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
      const result = await wpFetch(path, { method, body: bodyArgs, params: queryArgs, site_url, api_key });
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

  return server;
}
