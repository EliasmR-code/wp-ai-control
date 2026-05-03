export default [
  {
    name: "search-content",
    description: "Search across posts and pages by keyword.",
    inputSchema: {
      type: "object",
      properties: {
        query: { type: "string", description: "Search query" },
      },
      required: ["query"],
    },
    _method: "GET",
    _path: "/search",
    _pathParams: [],
    _bodyParams: [],
  },
];
