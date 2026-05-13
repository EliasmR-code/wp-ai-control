// Rank Math SEO (10 tools) — requiere Rank Math activo; redirecciones vía módulo Rank Math.

const RANK_MATH_BODY_FIELDS = [
  "rank_math_title",
  "rank_math_description",
  "rank_math_focus_keyword",
  "rank_math_canonical_url",
  "rank_math_primary_category",
  "rank_math_primary_product_cat",
  "rank_math_facebook_title",
  "rank_math_facebook_description",
  "rank_math_facebook_image",
  "rank_math_twitter_title",
  "rank_math_twitter_description",
  "rank_math_twitter_image",
  "rank_math_twitter_use_facebook",
  "rank_math_rich_snippet",
  "rank_math_snippet_name",
  "rank_math_robots",
];

const REDIRECTION_WRITE_FIELDS = [
  "sources",
  "source_pattern",
  "comparison",
  "url_to",
  "header_code",
  "status",
];

export default [
  {
    name: "rank-math-status",
    description:
      "Comprueba Rank Math (versión, si las redirecciones por API están disponibles).",
    inputSchema: { type: "object", properties: {} },
    _method: "GET",
    _path: "/rank-math/status",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-rank-math-settings",
    description:
      "Lee opciones globales de Rank Math. group: general | sitemap | titles, o all para las tres opciones (rank-math-options-*).",
    inputSchema: {
      type: "object",
      properties: {
        group: {
          type: "string",
          description: "general, sitemap, titles o all",
        },
      },
    },
    _method: "GET",
    _path: "/rank-math/settings",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "update-rank-math-settings",
    description:
      'Actualiza opciones con whitelist: body { "group": "general"|"sitemap"|"titles", "patch": { "clave": "valor" } }. Valores on/off, listas slug, robots como array de strings, etc. Ver documentación Rank Math para claves.',
    inputSchema: {
      type: "object",
      properties: {
        group: {
          type: "string",
          enum: ["general", "sitemap", "titles"],
          description: "Grupo de opciones",
        },
        patch: {
          type: "object",
          description: "Solo claves permitidas para ese grupo",
          additionalProperties: true,
        },
      },
      required: ["group", "patch"],
    },
    _method: "PUT",
    _path: "/rank-math/settings",
    _pathParams: [],
    _bodyParams: ["group", "patch"],
  },
  {
    name: "get-rank-math-post-seo",
    description:
      "Meta SEO Rank Math de un post/página (título SEO, descripción, keyword, canonical, robots, OG, Twitter…).",
    inputSchema: {
      type: "object",
      properties: { id: { type: "number", description: "ID del post" } },
      required: ["id"],
    },
    _method: "GET",
    _path: "/rank-math/posts/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-rank-math-post-seo",
    description:
      'PUT meta del post: enviar solo rank_math_* a cambiar; rank_math_robots como objeto. Mínimo un campo además de id.',
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number" },
        rank_math_title: { type: "string" },
        rank_math_description: { type: "string" },
        rank_math_focus_keyword: { type: "string" },
        rank_math_canonical_url: { type: "string" },
        rank_math_primary_category: { type: "number" },
        rank_math_primary_product_cat: { type: "number" },
        rank_math_facebook_title: { type: "string" },
        rank_math_facebook_description: { type: "string" },
        rank_math_facebook_image: { type: "string" },
        rank_math_twitter_title: { type: "string" },
        rank_math_twitter_description: { type: "string" },
        rank_math_twitter_image: { type: "string" },
        rank_math_twitter_use_facebook: { type: "boolean" },
        rank_math_rich_snippet: { type: "string" },
        rank_math_snippet_name: { type: "string" },
        rank_math_robots: { type: "object", additionalProperties: { type: "string" } },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/rank-math/posts/{id}",
    _pathParams: ["id"],
    _bodyParams: RANK_MATH_BODY_FIELDS,
  },
  {
    name: "list-rank-math-redirections",
    description:
      "Lista redirecciones Rank Math (tabla interna). Paginación, búsqueda, status any|active|inactive|trashed.",
    inputSchema: {
      type: "object",
      properties: {
        page: { type: "number", description: "Página (default 1)" },
        per_page: { type: "number", description: "Máx 100, default 20" },
        search: { type: "string" },
        status: { type: "string" },
        orderby: {
          type: "string",
          description: "id, url_to, header_code, hits, created, last_accessed",
        },
        order: { type: "string", description: "ASC o DESC" },
      },
    },
    _method: "GET",
    _path: "/rank-math/redirections",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-rank-math-redirection",
    description: "Obtiene una redirección por ID (sources deserializados).",
    inputSchema: {
      type: "object",
      properties: { id: { type: "number" } },
      required: ["id"],
    },
    _method: "GET",
    _path: "/rank-math/redirections/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-rank-math-redirection",
    description:
      'Crea redirección: usar sources [{pattern, comparison}] o atajo source_pattern + comparison (exact|contains|start|end|regex). header_code 301|302|307|410|451; 410/451 sin url_to.',
    inputSchema: {
      type: "object",
      properties: {
        sources: {
          type: "array",
          description: "Lista de { pattern, comparison?, ignore? }",
        },
        source_pattern: { type: "string" },
        comparison: { type: "string" },
        url_to: { type: "string" },
        header_code: { type: "number" },
        status: { type: "string", description: "active o inactive" },
      },
    },
    _method: "POST",
    _path: "/rank-math/redirections",
    _pathParams: [],
    _bodyParams: REDIRECTION_WRITE_FIELDS,
  },
  {
    name: "update-rank-math-redirection",
    description:
      "Actualiza redirección por ID. Mismos campos que create; omitir lo que no cambie.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number" },
        sources: { type: "array" },
        source_pattern: { type: "string" },
        comparison: { type: "string" },
        url_to: { type: "string" },
        header_code: { type: "number" },
        status: { type: "string" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/rank-math/redirections/{id}",
    _pathParams: ["id"],
    _bodyParams: REDIRECTION_WRITE_FIELDS,
  },
  {
    name: "delete-rank-math-redirection",
    description: "Elimina una redirección por ID.",
    inputSchema: {
      type: "object",
      properties: { id: { type: "number" } },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/rank-math/redirections/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
];
