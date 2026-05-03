export default [
  {
    name: "list-media",
    description: "List media files with optional MIME type filter.",
    inputSchema: {
      type: "object",
      properties: {
        mime_type: { type: "string", description: "Filter by MIME type (image/jpeg, application/pdf, etc.)" },
        per_page: { type: "number", description: "Results per page (default 20)" },
        page: { type: "number", description: "Page number" },
      },
    },
    _method: "GET",
    _path: "/media",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "delete-media",
    description: "Delete a media file from the library.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Media ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/media/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-media-meta",
    description: "Update multiple meta fields for a media file.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Media ID" },
        meta: { type: "object", description: "Object with meta key-value pairs" },
      },
      required: ["id", "meta"],
    },
    _method: "PUT",
    _path: "/media/{id}/meta",
    _pathParams: ["id"],
    _bodyParams: ["meta"],
  },
];
