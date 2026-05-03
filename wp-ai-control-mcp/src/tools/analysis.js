export default [
  {
    name: "analyze-seo",
    description: "Run an SEO analysis on a page: meta tags, headings, keyword density, and recommendations.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID to analyze" },
      },
      required: ["page_id"],
    },
    _method: "GET",
    _path: "/analyze/seo/{page_id}",
    _pathParams: ["page_id"],
    _bodyParams: [],
  },
  {
    name: "analyze-performance",
    description: "Run a performance analysis on a page: asset sizes, render-blocking resources, and optimization tips.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID to analyze" },
      },
      required: ["page_id"],
    },
    _method: "GET",
    _path: "/analyze/performance/{page_id}",
    _pathParams: ["page_id"],
    _bodyParams: [],
  },
  {
    name: "analyze-aeo",
    description: "Run an AI Engine Optimization analysis: structured data, answer-readiness, and entity coverage.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID to analyze" },
      },
      required: ["page_id"],
    },
    _method: "GET",
    _path: "/analyze/aeo/{page_id}",
    _pathParams: ["page_id"],
    _bodyParams: [],
  },
  {
    name: "analyze-accessibility",
    description: "Run an accessibility analysis on a page: WCAG compliance, alt text, contrast, and ARIA issues.",
    inputSchema: {
      type: "object",
      properties: {
        page_id: { type: "number", description: "Page ID to analyze" },
      },
      required: ["page_id"],
    },
    _method: "GET",
    _path: "/analyze/accessibility/{page_id}",
    _pathParams: ["page_id"],
    _bodyParams: [],
  },
];
