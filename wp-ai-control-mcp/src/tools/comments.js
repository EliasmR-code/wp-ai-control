export default [
  {
    name: "list-comments",
    description: "List comments with optional status and post filter.",
    inputSchema: {
      type: "object",
      properties: {
        status: { type: "string", description: "Status: hold, approve, spam, trash" },
        post_id: { type: "number", description: "Filter by post ID" },
        per_page: { type: "number", description: "Results per page (default 20)" },
        page: { type: "number", description: "Page number" },
      },
    },
    _method: "GET",
    _path: "/comments",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-comment",
    description: "Get a single comment by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Comment ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/comments/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "approve-comment",
    description: "Approve a comment for display.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Comment ID" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/comments/{id}/approve",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "spam-comment",
    description: "Mark a comment as spam.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Comment ID" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/comments/{id}/spam",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "delete-comment",
    description: "Delete a comment permanently.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Comment ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/comments/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
];
