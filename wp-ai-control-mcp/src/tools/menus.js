export default [
  {
    name: "list-menus",
    description: "List all WordPress navigation menus with their item counts and assigned locations.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/menus",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-menu",
    description: "Get a navigation menu's full structure including all menu items and their hierarchy.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Menu ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/menus/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "list-menu-locations",
    description: "List all registered theme menu locations and which menus are assigned to them.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/menus/locations",
    _pathParams: [],
    _bodyParams: [],
  },
];
