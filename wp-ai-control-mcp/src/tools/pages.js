export default [
  {
    name: "list-pages",
    description: "List WordPress pages with optional search, status filter, and pagination.",
    inputSchema: {
      type: "object",
      properties: {
        search: { type: "string", description: "Text to search" },
        status: { type: "string", description: "publish | draft | private | any" },
        per_page: { type: "number", description: "Results per page (default 10)" },
        page: { type: "number", description: "Page number (default 1)" },
      },
    },
    _method: "GET",
    _path: "/pages",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "read-page",
    description: "Get full page content including title, status, meta, and builder data.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Page ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/pages/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-page",
    description: "Update a page's title, content, or status.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Page ID" },
        title: { type: "string", description: "New title" },
        content: { type: "string", description: "New content" },
        status: { type: "string", description: "New status (publish, draft, private)" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/pages/{id}",
    _pathParams: ["id"],
    _bodyParams: ["title", "content", "status"],
  },
  {
    name: "delete-page",
    description: "Move a page to the trash.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Page ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/pages/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-page-duplicate",
    description: "Create an exact duplicate of an existing page including all content and builder data.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Page ID to duplicate" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/pages/{id}/duplicate",
    _pathParams: ["id"],
    _bodyParams: [],
  },
];
