export default [
  {
    name: "list-posts",
    description: "List WordPress posts with optional search, status filter, and pagination.",
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
    _path: "/posts",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "read-post",
    description: "Get full post content including title, status, meta, categories, and tags.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/posts/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-post",
    description: "Update a post's title, content, or status.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post ID" },
        title: { type: "string", description: "New title" },
        content: { type: "string", description: "New content" },
        status: { type: "string", description: "New status (publish, draft, private)" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/posts/{id}",
    _pathParams: ["id"],
    _bodyParams: ["title", "content", "status"],
  },
  {
    name: "delete-post",
    description: "Move a post to the trash.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/posts/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
];
