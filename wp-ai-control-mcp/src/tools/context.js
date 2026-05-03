export default [
  {
    name: "get-site-context",
    description: "Get comprehensive information about the WordPress site including version, theme, active plugins, and page builder.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/site-info",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-builder-info",
    description: "Detect which page builder is active and its version, available modules, and support level.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/builder-info",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-theme-docs",
    description: "Get documentation and template information for the active WordPress theme.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/theme-docs",
    _pathParams: [],
    _bodyParams: [],
  },
];
