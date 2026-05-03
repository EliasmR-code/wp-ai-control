export default [
  {
    name: "list-themes",
    description: "List all installed WordPress themes.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/themes",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "activate-theme",
    description: "Activate a theme by slug.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Theme slug" },
      },
      required: ["slug"],
    },
    _method: "POST",
    _path: "/themes/{slug}/activate",
    _pathParams: ["slug"],
    _bodyParams: [],
  },
  {
    name: "update-theme",
    description: "Update the active theme to the latest version.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Theme slug to update" },
      },
      required: ["slug"],
    },
    _method: "POST",
    _path: "/themes/{slug}/update",
    _pathParams: ["slug"],
    _bodyParams: [],
  },
];
