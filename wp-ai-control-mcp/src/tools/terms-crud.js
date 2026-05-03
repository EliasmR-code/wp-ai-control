export default [
  {
    name: "create-term",
    description: "Create a new term in a taxonomy.",
    inputSchema: {
      type: "object",
      properties: {
        name: { type: "string", description: "Term name" },
        taxonomy: { type: "string", description: "Taxonomy (category, post_tag, etc.)" },
        description: { type: "string", description: "Term description" },
        parent: { type: "number", description: "Parent term ID" },
      },
      required: ["name", "taxonomy"],
    },
    _method: "POST",
    _path: "/terms",
    _pathParams: [],
    _bodyParams: ["name", "taxonomy", "description", "parent"],
  },
  {
    name: "get-term",
    description: "Get a term by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Term ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/terms/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-term",
    description: "Update a term's name, description, or parent.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Term ID" },
        taxonomy: { type: "string", description: "Taxonomy name" },
        name: { type: "string", description: "New name" },
        description: { type: "string", description: "New description" },
        parent: { type: "number", description: "New parent ID" },
      },
      required: ["id", "taxonomy"],
    },
    _method: "PUT",
    _path: "/terms/{id}",
    _pathParams: ["id"],
    _bodyParams: ["taxonomy", "name", "description", "parent"],
  },
  {
    name: "delete-term",
    description: "Delete a term from a taxonomy.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Term ID" },
        taxonomy: { type: "string", description: "Taxonomy name" },
      },
      required: ["id", "taxonomy"],
    },
    _method: "DELETE",
    _path: "/terms/{id}",
    _pathParams: ["id"],
    _bodyParams: ["taxonomy"],
  },
];
