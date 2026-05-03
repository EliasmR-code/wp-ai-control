export default [
  {
    name: "list-post-meta",
    description: "List all custom field meta for a post or page.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post/Page ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/posts/{id}/meta",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-post-meta",
    description: "Update multiple meta fields for a post or page.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post/Page ID" },
        meta: { type: "object", description: "Object with meta key-value pairs" },
      },
      required: ["id", "meta"],
    },
    _method: "POST",
    _path: "/posts/{id}/meta",
    _pathParams: ["id"],
    _bodyParams: ["meta"],
  },
  {
    name: "delete-post-meta",
    description: "Delete a specific meta field from a post or page.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Post/Page ID" },
        meta_key: { type: "string", description: "Meta key to delete" },
      },
      required: ["id", "meta_key"],
    },
    _method: "DELETE",
    _path: "/posts/{id}/meta/{meta_key}",
    _pathParams: ["id", "meta_key"],
    _bodyParams: [],
  },
];
