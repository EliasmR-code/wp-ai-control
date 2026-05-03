export default [
  {
    name: "upload-media",
    description: "Upload an image or file to the WordPress media library.",
    inputSchema: {
      type: "object",
      properties: {
        url: { type: "string", description: "URL of the file to upload" },
        filename: { type: "string", description: "Filename for the uploaded file" },
        alt_text: { type: "string", description: "Alt text for images" },
      },
    },
    _method: "POST",
    _path: "/media/upload",
    _pathParams: [],
    _bodyParams: ["url", "filename", "alt_text"],
  },
];
