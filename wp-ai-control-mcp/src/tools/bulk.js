export default [
  {
    name: "bulk-update-posts",
    description: "Update multiple posts/pages at once with the same changes.",
    inputSchema: {
      type: "object",
      properties: {
        post_ids: { type: "array", items: { type: "number" }, description: "Array of post IDs" },
        updates: {
          type: "object",
          properties: {
            title: { type: "string" },
            content: { type: "string" },
            status: { type: "string" },
          },
          description: "Fields to update (title, content, status)"
        },
      },
      required: ["post_ids", "updates"],
    },
    _method: "POST",
    _path: "/bulk-update-posts",
    _pathParams: [],
    _bodyParams: ["post_ids", "updates"],
  },
  {
    name: "bulk-delete-posts",
    description: "Delete multiple posts/pages at once.",
    inputSchema: {
      type: "object",
      properties: {
        post_ids: { type: "array", items: { type: "number" }, description: "Array of post IDs to delete" },
      },
      required: ["post_ids"],
    },
    _method: "POST",
    _path: "/bulk-delete-posts",
    _pathParams: [],
    _bodyParams: ["post_ids"],
  },
];
