export default [
  {
    name: "list-snapshots",
    description: "List available content snapshots for rollback.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/snapshots",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "restore-snapshot",
    description: "Roll back a page to a previous snapshot version.",
    inputSchema: {
      type: "object",
      properties: {
        snapshot_id: { type: "number", description: "Snapshot ID to restore" },
      },
      required: ["snapshot_id"],
    },
    _method: "POST",
    _path: "/snapshots/rollback",
    _pathParams: [],
    _bodyParams: ["snapshot_id"],
  },
];
