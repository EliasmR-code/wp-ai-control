export default [
  {
    name: "get-usage",
    description: "Get current usage statistics: count, limit, and reset date (local data, no external calls).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/usage",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-plan-info",
    description: "Get plugin information: version, features, and capabilities (local data).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/plan-info",
    _pathParams: [],
    _bodyParams: [],
  },
];
