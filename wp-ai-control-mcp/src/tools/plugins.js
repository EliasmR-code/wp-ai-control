export default [
  {
    name: "list-plugins",
    description: "List all installed WordPress plugins with status, version, and update availability.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/plugins",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "install-plugin",
    description: "Install a plugin from the WordPress.org repository by slug.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Plugin slug (e.g., 'contact-form-7')" },
      },
      required: ["slug"],
    },
    _method: "POST",
    _path: "/plugins/install",
    _pathParams: [],
    _bodyParams: ["slug"],
  },
  {
    name: "activate-plugin",
    description: "Activate an installed WordPress plugin by its slug.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Plugin slug" },
      },
      required: ["slug"],
    },
    _method: "POST",
    _path: "/plugins/{slug}/activate",
    _pathParams: ["slug"],
    _bodyParams: [],
  },
  {
    name: "deactivate-plugin",
    description: "Deactivate an active WordPress plugin by its slug.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Plugin slug" },
      },
      required: ["slug"],
    },
    _method: "POST",
    _path: "/plugins/{slug}/deactivate",
    _pathParams: ["slug"],
    _bodyParams: [],
  },
];
