// Advanced Custom Fields (ACF) Tools (54 tools)

export default [
  // =================== FIELD GROUPS (8) ===================

  {
    name: "list-field-groups",
    description: "List all ACF field groups.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/field-groups",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-field-group",
    description: "Get a single ACF field group by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/acf/field-groups/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-field-group",
    description: "Create a new ACF field group.",
    inputSchema: {
      type: "object",
      properties: {
        title: { type: "string", description: "Field group title" },
        key: { type: "string", description: "Field group key (auto-generated if omitted)" },
        position: { type: "string", description: "Position (normal, side, acf_after_title)" },
        style: { type: "string", description: "Style (default, seamless)" },
        location: { type: "array", description: "Location rules array" },
      },
      required: ["title"],
    },
    _method: "POST",
    _path: "/acf/field-groups",
    _pathParams: [],
    _bodyParams: ["title", "key", "position", "style", "location"],
  },
  {
    name: "update-field-group",
    description: "Update an ACF field group.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
        title: { type: "string", description: "New title" },
        position: { type: "string", description: "New position" },
        style: { type: "string", description: "New style" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/acf/field-groups/{id}",
    _pathParams: ["id"],
    _bodyParams: ["title", "position", "style"],
  },
  {
    name: "delete-field-group",
    description: "Delete an ACF field group.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/acf/field-groups/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "list-fields-in-group",
    description: "List all fields in a specific field group.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/acf/field-groups/{id}/fields",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "duplicate-field-group",
    description: "Duplicate an entire field group with all its fields.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID to duplicate" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/acf/field-groups/{id}/duplicate",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "assign-field-group",
    description: "Assign a field group to post types/taxonomies using location rules.",
    inputSchema: {
      type: "object",
      properties: {
        group_id: { type: "number", description: "Field group ID" },
        location: { type: "array", description: "Location rules array" },
      },
      required: ["group_id", "location"],
    },
    _method: "POST",
    _path: "/acf/field-groups/assign",
    _pathParams: [],
    _bodyParams: ["group_id", "location"],
  },

  // =================== FIELDS (12) ===================

  {
    name: "list-fields",
    description: "List ACF fields with optional group or type filter.",
    inputSchema: {
      type: "object",
      properties: {
        group_id: { type: "number", description: "Filter by field group ID" },
        type: { type: "string", description: "Filter by field type" },
      },
    },
    _method: "GET",
    _path: "/acf/fields",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-field",
    description: "Get a single ACF field by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/acf/fields/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-field",
    description: "Create a new ACF field.",
    inputSchema: {
      type: "object",
      properties: {
        label: { type: "string", description: "Field label" },
        name: { type: "string", description: "Field name" },
        type: { type: "string", description: "Field type (text, textarea, number, select, etc.)" },
        parent: { type: "number", description: "Parent field group ID" },
        default_value: { type: "string", description: "Default value" },
        required: { type: "boolean", description: "Whether field is required" },
      },
      required: ["label", "name", "type", "parent"],
    },
    _method: "POST",
    _path: "/acf/fields",
    _pathParams: [],
    _bodyParams: ["label", "name", "type", "parent", "default_value", "required"],
  },
  {
    name: "update-field",
    description: "Update an ACF field.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field ID" },
        label: { type: "string", description: "New label" },
        default_value: { type: "string", description: "New default value" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/acf/fields/{id}",
    _pathParams: ["id"],
    _bodyParams: ["label", "default_value"],
  },
  {
    name: "delete-field",
    description: "Delete an ACF field.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/acf/fields/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "duplicate-field",
    description: "Duplicate an ACF field within the same group.",
    inputSchema: {
      type: "object",
      properties: {
        field_id: { type: "number", description: "Field ID to duplicate" },
      },
      required: ["field_id"],
    },
    _method: "POST",
    _path: "/acf/fields/duplicate",
    _pathParams: [],
    _bodyParams: ["field_id"],
  },
  {
    name: "export-fields",
    description: "Export ACF fields by IDs.",
    inputSchema: {
      type: "object",
      properties: {
        field_ids: { type: "array", items: { type: "number" }, description: "Array of field IDs" },
      },
      required: ["field_ids"],
    },
    _method: "POST",
    _path: "/acf/fields/export",
    _pathParams: [],
    _bodyParams: ["field_ids"],
  },
  {
    name: "import-fields",
    description: "Import ACF fields from exported data.",
    inputSchema: {
      type: "object",
      properties: {
        fields: { type: "array", description: "Array of field objects to import" },
      },
      required: ["fields"],
    },
    _method: "POST",
    _path: "/acf/fields/import",
    _pathParams: [],
    _bodyParams: ["fields"],
  },
  {
    name: "validate-field",
    description: "Validate an ACF field configuration.",
    inputSchema: {
      type: "object",
      properties: {
        field: { type: "object", description: "Field configuration object" },
      },
      required: ["field"],
    },
    _method: "POST",
    _path: "/acf/fields/validate",
    _pathParams: [],
    _bodyParams: ["field"],
  },
  {
    name: "bulk-update-fields",
    description: "Update multiple ACF fields at once.",
    inputSchema: {
      type: "object",
      properties: {
        field_ids: { type: "array", items: { type: "number" }, description: "Array of field IDs" },
        updates: { type: "object", description: "Fields to update" },
      },
      required: ["field_ids", "updates"],
    },
    _method: "POST",
    _path: "/acf/fields/bulk-update",
    _pathParams: [],
    _bodyParams: ["field_ids", "updates"],
  },
  {
    name: "clone-field",
    description: "Clone an ACF field to a different parent group.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field ID" },
        parent: { type: "number", description: "New parent field group ID" },
      },
      required: ["id", "parent"],
    },
    _method: "POST",
    _path: "/acf/fields/{id}/clone",
    _pathParams: ["id"],
    _bodyParams: ["parent"],
  },

  // =================== POST META WITH ACF (8) ===================

  {
    name: "get-post-acf-fields",
    description: "Get all ACF fields and values for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
        group_id: { type: "number", description: "Filter by field group ID" },
      },
      required: ["post_id"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/fields",
    _pathParams: ["post_id"],
    _bodyParams: [],
  },
  {
    name: "update-post-acf-fields",
    description: "Update multiple ACF fields for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
        fields: { type: "object", description: "Object with field key-value pairs" },
      },
      required: ["post_id", "fields"],
    },
    _method: "POST",
    _path: "/acf/post/{post_id}/fields",
    _pathParams: ["post_id"],
    _bodyParams: ["fields"],
  },
  {
    name: "get-post-acf-field",
    description: "Get a specific ACF field value for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
        field_key: { type: "string", description: "ACF field key or name" },
      },
      required: ["post_id", "field_key"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/field/{field_key}",
    _pathParams: ["post_id", "field_key"],
    _bodyParams: [],
  },
  {
    name: "update-post-acf-field",
    description: "Update a specific ACF field value for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
        field_key: { type: "string", description: "ACF field key or name" },
        value: { description: "New field value" },
      },
      required: ["post_id", "field_key", "value"],
    },
    _method: "PUT",
    _path: "/acf/post/{post_id}/field/{field_key}",
    _pathParams: ["post_id", "field_key"],
    _bodyParams: ["value"],
  },
  {
    name: "render-post-acf-fields",
    description: "Render ACF form for a post (returns HTML).",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
      },
      required: ["post_id"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/render",
    _pathParams: ["post_id"],
    _bodyParams: [],
  },
  {
    name: "get-post-layouts",
    description: "Get ACF layout fields for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
      },
      required: ["post_id"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/layouts",
    _pathParams: ["post_id"],
    _bodyParams: [],
  },
  {
    name: "get-flexible-content",
    description: "Get flexible content field layouts for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
      },
      required: ["post_id"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/flexible-content",
    _pathParams: ["post_id"],
    _bodyParams: [],
  },
  {
    name: "get-repeater-field",
    description: "Get repeater field rows for a post.",
    inputSchema: {
      type: "object",
      properties: {
        post_id: { type: "number", description: "Post ID" },
        field_key: { type: "string", description: "Repeater field key or name" },
      },
      required: ["post_id", "field_key"],
    },
    _method: "GET",
    _path: "/acf/post/{post_id}/repeater/{field_key}",
    _pathParams: ["post_id", "field_key"],
    _bodyParams: [],
  },

  // =================== OPTIONS PAGES (4) ===================

  {
    name: "list-options-pages",
    description: "List all ACF options pages.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/options-pages",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "create-options-page",
    description: "Create a new ACF options page.",
    inputSchema: {
      type: "object",
      properties: {
        page_title: { type: "string", description: "Page title" },
        menu_title: { type: "string", description: "Menu title" },
        menu_slug: { type: "string", description: "Menu slug" },
      },
      required: ["page_title", "menu_slug"],
    },
    _method: "POST",
    _path: "/acf/options-pages",
    _pathParams: [],
    _bodyParams: ["page_title", "menu_title", "menu_slug"],
  },
  {
    name: "get-options-page",
    description: "Get a specific options page by slug.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Options page slug" },
      },
      required: ["slug"],
    },
    _method: "GET",
    _path: "/acf/options-pages/{slug}",
    _pathParams: ["slug"],
    _bodyParams: [],
  },
  {
    name: "get-options-page-fields",
    description: "Get all ACF fields and values for an options page.",
    inputSchema: {
      type: "object",
      properties: {
        slug: { type: "string", description: "Options page slug" },
      },
      required: ["slug"],
    },
    _method: "GET",
    _path: "/acf/options-pages/{slug}/fields",
    _pathParams: ["slug"],
    _bodyParams: [],
  },

  // =================== FIELD TYPES & VALIDATION (6) ===================

  {
    name: "list-field-types",
    description: "List all available ACF field types.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/field-types",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-field-type",
    description: "Get details about a specific field type.",
    inputSchema: {
      type: "object",
      properties: {
        type: { type: "string", description: "Field type name" },
      },
      required: ["type"],
    },
    _method: "GET",
    _path: "/acf/field-types/{type}",
    _pathParams: ["type"],
    _bodyParams: [],
  },
  {
    name: "validate-acf-rule",
    description: "Validate an ACF location rule.",
    inputSchema: {
      type: "object",
      properties: {
        rule: { type: "object", description: "Rule object with param, operator, value" },
      },
      required: ["rule"],
    },
    _method: "POST",
    _path: "/acf/validate-rule",
    _pathParams: [],
    _bodyParams: ["rule"],
  },
  {
    name: "list-location-rules",
    description: "List all available ACF location rules.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/location-rules",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "update-location-rules",
    description: "Update location rules for a field group.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
        location: { type: "array", description: "New location rules" },
      },
      required: ["id", "location"],
    },
    _method: "PUT",
    _path: "/acf/field-groups/{id}/rules",
    _pathParams: ["id"],
    _bodyParams: ["location"],
  },
  {
    name: "list-cloneable-fields",
    description: "List all cloneable ACF fields.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/clone-fields",
    _pathParams: [],
    _bodyParams: [],
  },

  // =================== BULK OPERATIONS (4) ===================

  {
    name: "bulk-update-acf-meta",
    description: "Update ACF fields across multiple posts.",
    inputSchema: {
      type: "object",
      properties: {
        post_ids: { type: "array", items: { type: "number" }, description: "Array of post IDs" },
        fields: { type: "object", description: "Object with field key-value pairs" },
      },
      required: ["post_ids", "fields"],
    },
    _method: "POST",
    _path: "/acf/bulk-update-meta",
    _pathParams: [],
    _bodyParams: ["post_ids", "fields"],
  },
  {
    name: "bulk-clone-fields",
    description: "Clone ACF fields from one post to multiple target posts.",
    inputSchema: {
      type: "object",
      properties: {
        source_post_id: { type: "number", description: "Source post ID" },
        target_post_ids: { type: "array", items: { type: "number" }, description: "Target post IDs" },
        field_keys: { type: "array", items: { type: "string" }, description: "Field keys to clone" },
      },
      required: ["source_post_id", "target_post_ids", "field_keys"],
    },
    _method: "POST",
    _path: "/acf/bulk-clone-fields",
    _pathParams: [],
    _bodyParams: ["source_post_id", "target_post_ids", "field_keys"],
  },
  {
    name: "export-field-group",
    description: "Export a complete field group with all its fields.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Field group ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/acf/export-group/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "import-field-group",
    description: "Import a complete field group with all its fields.",
    inputSchema: {
      type: "object",
      properties: {
        data: { type: "object", description: "Exported group data with fields" },
      },
      required: ["data"],
    },
    _method: "POST",
    _path: "/acf/import-group",
    _pathParams: [],
    _bodyParams: ["data"],
  },

  // =================== FIELD GROUP ANALYSIS (6) ===================

  {
    name: "analyze-field-group-usage",
    description: "Analyze where a field group is used across posts.",
    inputSchema: {
      type: "object",
      properties: {
        group_id: { type: "number", description: "Field group ID" },
      },
      required: ["group_id"],
    },
    _method: "GET",
    _path: "/acf/field-groups/usage",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-field-dependencies",
    description: "Get conditional logic dependencies for fields in a group.",
    inputSchema: {
      type: "object",
      properties: {
        field_id: { type: "number", description: "Field ID" },
      },
      required: ["field_id"],
    },
    _method: "GET",
    _path: "/acf/field-groups/dependencies",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "find-orphaned-fields",
    description: "Find ACF fields that don't belong to any group.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/field-groups/orphaned",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "check-duplicate-fields",
    description: "Check for duplicate field names or labels across groups.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/field-groups/duplicate-check",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-conditional-logic",
    description: "Get all conditional logic rules for fields in a group.",
    inputSchema: {
      type: "object",
      properties: {
        group_id: { type: "number", description: "Field group ID" },
      },
      required: ["group_id"],
    },
    _method: "GET",
    _path: "/acf/field-groups/conditional-logic",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "analyze-acf-performance",
    description: "Analyze ACF field groups for performance issues (too many fields, etc.).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/acf/field-groups/performance",
    _pathParams: [],
    _bodyParams: [],
  },
];
