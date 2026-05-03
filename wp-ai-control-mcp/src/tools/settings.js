export default [
  {
    name: "get-site-settings",
    description: "Get WordPress site settings (site name, description, timezone, etc.).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/settings",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "update-site-settings",
    description: "Update WordPress site settings.",
    inputSchema: {
      type: "object",
      properties: {
        blogname: { type: "string", description: "Site title" },
        blogdescription: { type: "string", description: "Tagline" },
        users_can_register: { type: "boolean", description: "Allow user registration" },
        timezone_string: { type: "string", description: "Timezone (e.g., America/New_York)" },
        date_format: { type: "string", description: "Date format" },
        time_format: { type: "string", description: "Time format" },
        start_of_week: { type: "number", description: "Start of week (0=Sunday, 1=Monday, etc.)" },
      },
    },
    _method: "PUT",
    _path: "/settings",
    _pathParams: [],
    _bodyParams: ["blogname", "blogdescription", "users_can_register", "timezone_string", "date_format", "time_format", "start_of_week"],
  },
];
