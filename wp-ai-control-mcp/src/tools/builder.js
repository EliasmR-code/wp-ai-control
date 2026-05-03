export default [
  {
    name: "extract-builder-content",
    description: "Extract page builder content as structured JSON. Supports 12 builders: gutenberg, elementor, divi, wpbakery, bricks, oxygen, beaver, brizy, thrive, breakdance, flatsome, kadence, kadence_blocks.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID" },
        builder: { type: "string", description: "Builder: gutenberg, elementor, divi, wpbakery, bricks, oxygen, beaver, brizy, thrive, breakdance, flatsome, kadence, kadence_blocks" },
      },
      required: ["page_id", "builder"],
    },
    _method: "GET",
    _path: "/builder/{builder}/extract/{page_id}",
    _pathParams: ["builder", "page_id"],
    _bodyParams: [],
  },
  {
    name: "inject-builder-content",
    description: "Replace page builder content from structured JSON. Supports 12 builders with deep intelligence for each.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID" },
        builder: { type: "string", description: "Builder: gutenberg, elementor, divi, wpbakery, bricks, oxygen, beaver, brizy, thrive, breakdance, flatsome, kadence, kadence_blocks" },
        content: { type: "object", description: "Builder content structure (format varies by builder)" },
      },
      required: ["page_id", "builder", "content"],
    },
    _method: "POST",
    _path: "/builder/{builder}/inject/{page_id}",
    _pathParams: ["builder", "page_id"],
    _bodyParams: ["content"],
  },
];
