export default [
  {
    name: "list-users",
    description: "List all WordPress users with their roles.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/users",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-user",
    description: "Get user details by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "User ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/users/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-user",
    description: "Create a new WordPress user.",
    inputSchema: {
      type: "object",
      properties: {
        username: { type: "string", description: "Username" },
        email: { type: "string", description: "Email address" },
        name: { type: "string", description: "Display name" },
        role: { type: "string", description: "Role (subscriber, editor, admin, etc.)" },
      },
      required: ["username", "email"],
    },
    _method: "POST",
    _path: "/users",
    _pathParams: [],
    _bodyParams: ["username", "email", "name", "role"],
  },
  {
    name: "update-user",
    description: "Update an existing user's details.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "User ID" },
        email: { type: "string", description: "New email" },
        name: { type: "string", description: "New display name" },
        role: { type: "string", description: "New role" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/users/{id}",
    _pathParams: ["id"],
    _bodyParams: ["email", "name", "role"],
  },
  {
    name: "delete-user",
    description: "Delete a user from WordPress.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "User ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/users/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
];
