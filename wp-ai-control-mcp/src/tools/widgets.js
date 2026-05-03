// Widget Management Tools (27 tools)

export default [
  // =================== WIDGETS - INDIVIDUAL (12) ===================

  {
    name: "list-widgets",
    description: "List all active widgets across all sidebars.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/widgets",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-widget",
    description: "Get a specific widget's details and settings.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID (e.g., text-2)" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/widgets/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-widget",
    description: "Create a new widget instance and add it to a sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        id_base: { type: "string", description: "Widget base ID (e.g., text, search)" },
        sidebar: { type: "string", description: "Sidebar ID to add widget to" },
        settings: { type: "object", description: "Widget settings object" },
      },
      required: ["id_base", "sidebar"],
    },
    _method: "POST",
    _path: "/widgets",
    _pathParams: [],
    _bodyParams: ["id_base", "sidebar", "settings"],
  },
  {
    name: "update-widget",
    description: "Update an existing widget's settings.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID" },
        settings: { type: "object", description: "New widget settings" },
      },
      required: ["id", "settings"],
    },
    _method: "PUT",
    _path: "/widgets/{id}",
    _pathParams: ["id"],
    _bodyParams: ["settings"],
  },
  {
    name: "delete-widget",
    description: "Delete a widget from all sidebars.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID to delete" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/widgets/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "duplicate-widget",
    description: "Duplicate a widget within the same sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID to duplicate" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/widgets/{id}/duplicate",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "list-available-widgets",
    description: "List all available widget types that can be created.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/widgets/available",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-widget-settings",
    description: "Get a widget's current settings.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/widgets/{id}/settings",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-widget-settings",
    description: "Update a widget's settings directly.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID" },
        settings: { type: "object", description: "Settings object" },
      },
      required: ["id", "settings"],
    },
    _method: "PUT",
    _path: "/widgets/{id}/settings",
    _pathParams: ["id"],
    _bodyParams: ["settings"],
  },
  {
    name: "preview-widget",
    description: "Preview a widget's output as HTML.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/widgets/{id}/preview",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "bulk-update-widgets",
    description: "Update multiple widgets with the same settings.",
    inputSchema: {
      type: "object",
      properties: {
        widget_ids: { type: "array", items: { type: "string" }, description: "Array of widget IDs" },
        settings: { type: "object", description: "Settings to apply to all widgets" },
      },
      required: ["widget_ids", "settings"],
    },
    _method: "POST",
    _path: "/widgets/bulk-update",
    _pathParams: [],
    _bodyParams: ["widget_ids", "settings"],
  },
  {
    name: "bulk-delete-widgets",
    description: "Delete multiple widgets at once.",
    inputSchema: {
      type: "object",
      properties: {
        widget_ids: { type: "array", items: { type: "string" }, description: "Array of widget IDs to delete" },
      },
      required: ["widget_ids"],
    },
    _method: "POST",
    _path: "/widgets/bulk-delete",
    _pathParams: [],
    _bodyParams: ["widget_ids"],
  },

  // =================== SIDEBARS (6) ===================

  {
    name: "list-sidebars",
    description: "List all registered widget areas (sidebars).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/sidebars",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-sidebar",
    description: "Get a specific sidebar's details and its widgets.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Sidebar ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/sidebars/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "register-sidebar",
    description: "Register a new sidebar (widget area).",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Sidebar ID" },
        name: { type: "string", description: "Sidebar display name" },
        description: { type: "string", description: "Sidebar description" },
      },
      required: ["id", "name"],
    },
    _method: "POST",
    _path: "/sidebars",
    _pathParams: [],
    _bodyParams: ["id", "name", "description"],
  },
  {
    name: "unregister-sidebar",
    description: "Unregister a sidebar (widget area).",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Sidebar ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/sidebars/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "get-sidebar-widgets",
    description: "Get all widgets assigned to a specific sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Sidebar ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/sidebars/{id}/widgets",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "clear-sidebar",
    description: "Remove all widgets from a sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Sidebar ID" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/sidebars/{id}/clear",
    _pathParams: ["id"],
    _bodyParams: [],
  },

  // =================== WIDGET POSITIONING (5) ===================

  {
    name: "move-widget",
    description: "Move a widget from one sidebar to another.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "string", description: "Widget ID" },
        from_sidebar: { type: "string", description: "Source sidebar ID" },
        to_sidebar: { type: "string", description: "Target sidebar ID" },
        position: { type: "number", description: "Position in target sidebar (optional)" },
      },
      required: ["id", "from_sidebar", "to_sidebar"],
    },
    _method: "POST",
    _path: "/widgets/{id}/move",
    _pathParams: ["id"],
    _bodyParams: ["from_sidebar", "to_sidebar", "position"],
  },
  {
    name: "reorder-widget",
    description: "Reorder widgets within a sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        sidebar: { type: "string", description: "Sidebar ID" },
        order: { type: "array", items: { type: "string" }, description: "New widget order (array of widget IDs)" },
      },
      required: ["sidebar", "order"],
    },
    _method: "POST",
    _path: "/widgets/{id}/reorder",
    _pathParams: ["id"],
    _bodyParams: ["sidebar", "order"],
  },
  {
    name: "add-widget-to-sidebar",
    description: "Add an existing widget to a sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        sidebar_id: { type: "string", description: "Sidebar ID" },
        widget_id: { type: "string", description: "Widget ID to add" },
      },
      required: ["sidebar_id", "widget_id"],
    },
    _method: "POST",
    _path: "/sidebars/{sidebar_id}/add-widget",
    _pathParams: ["sidebar_id"],
    _bodyParams: ["widget_id"],
  },
  {
    name: "remove-widget-from-sidebar",
    description: "Remove a widget from a specific sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        sidebar_id: { type: "string", description: "Sidebar ID" },
        widget_id: { type: "string", description: "Widget ID to remove" },
      },
      required: ["sidebar_id", "widget_id"],
    },
    _method: "DELETE",
    _path: "/sidebars/{sidebar_id}/remove-widget/{widget_id}",
    _pathParams: ["sidebar_id", "widget_id"],
    _bodyParams: [],
  },
  {
    name: "swap-widgets",
    description: "Swap positions of two widgets within the same sidebar.",
    inputSchema: {
      type: "object",
      properties: {
        widget_id_1: { type: "string", description: "First widget ID" },
        widget_id_2: { type: "string", description: "Second widget ID" },
      },
      required: ["widget_id_1", "widget_id_2"],
    },
    _method: "POST",
    _path: "/widgets/swap",
    _pathParams: [],
    _bodyParams: ["widget_id_1", "widget_id_2"],
  },

  // =================== WIDGET ANALYSIS (4) ===================

  {
    name: "get-widget-usage-stats",
    description: "Get statistics about widget usage across sidebars.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/widgets/usage-stats",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "find-orphaned-widgets",
    description: "Find widgets that are registered but not assigned to any sidebar.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/widgets/orphaned",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "find-duplicate-widgets",
    description: "Find widgets with identical settings.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/widgets/duplicates",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "analyze-sidebar-usage",
    description: "Analyze sidebar usage and widget distribution.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/sidebars/usage",
    _pathParams: [],
    _bodyParams: [],
  },
];
