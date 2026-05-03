export default [
  {
    name: "list-taxonomies",
    description: "List all registered WordPress taxonomies (categories, tags, custom taxonomies).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/taxonomies",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "list-terms",
    description: "List terms in a taxonomy with names, slugs, and post counts.",
    inputSchema: {
      type: "object",
      properties: {
        taxonomy: { type: "string", description: "Taxonomy name (e.g., 'category', 'post_tag')" },
        per_page: { type: "number", description: "Results per page (default 100)" },
        page: { type: "number", description: "Page number (default 1)" },
        search: { type: "string", description: "Search term" },
      },
      required: ["taxonomy"],
    },
    _method: "GET",
    _path: "/taxonomies/{taxonomy}/terms",
    _pathParams: ["taxonomy"],
    _bodyParams: [],
  },
];
