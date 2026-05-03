// WooCommerce Tools (21 tools)

export default [
  // =================== PRODUCTS (9) ===================

  {
    name: "list-products",
    description: "List WooCommerce products with optional search, category filter, and pagination.",
    inputSchema: {
      type: "object",
      properties: {
        search: { type: "string", description: "Search term" },
        category: { type: "string", description: "Category slug" },
        per_page: { type: "number", description: "Results per page (default 20)" },
        page: { type: "number", description: "Page number (default 1)" },
      },
    },
    _method: "GET",
    _path: "/wc/products",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-product",
    description: "Get a single WooCommerce product by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/wc/products/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-product",
    description: "Create a new WooCommerce product.",
    inputSchema: {
      type: "object",
      properties: {
        name: { type: "string", description: "Product name" },
        description: { type: "string", description: "Product description" },
        short_description: { type: "string", description: "Short description" },
        price: { type: "string", description: "Product price" },
        regular_price: { type: "string", description: "Regular price" },
        stock_quantity: { type: "number", description: "Stock quantity" },
      },
      required: ["name"],
    },
    _method: "POST",
    _path: "/wc/products",
    _pathParams: [],
    _bodyParams: ["name", "description", "short_description", "price", "regular_price", "stock_quantity"],
  },
  {
    name: "update-product",
    description: "Update a WooCommerce product.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
        name: { type: "string", description: "New product name" },
        price: { type: "string", description: "New price" },
        regular_price: { type: "string", description: "New regular price" },
        stock_quantity: { type: "number", description: "New stock quantity" },
      },
      required: ["id"],
    },
    _method: "PUT",
    _path: "/wc/products/{id}",
    _pathParams: ["id"],
    _bodyParams: ["name", "price", "regular_price", "stock_quantity"],
  },
  {
    name: "delete-product",
    description: "Delete a WooCommerce product permanently.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/wc/products/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-product-stock",
    description: "Update a product's stock quantity.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
        stock_quantity: { type: "number", description: "New stock quantity" },
      },
      required: ["id", "stock_quantity"],
    },
    _method: "PUT",
    _path: "/wc/products/{id}/stock",
    _pathParams: ["id"],
    _bodyParams: ["stock_quantity"],
  },
  {
    name: "list-product-categories",
    description: "List all WooCommerce product categories.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/wc/product-categories",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-product-reviews",
    description: "Get reviews for a specific product.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/wc/products/{id}/reviews",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "update-product-review",
    description: "Update a product review (content or rating).",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Product ID" },
        content: { type: "string", description: "New review content" },
        rating: { type: "number", description: "New rating (1-5)" },
      },
      required: ["id"],
    },
    _method: "POST",
    _path: "/wc/products/{id}/reviews",
    _pathParams: ["id"],
    _bodyParams: ["content", "rating"],
  },

  // =================== ORDERS (8) ===================

  {
    name: "list-orders",
    description: "List WooCommerce orders with optional status filter.",
    inputSchema: {
      type: "object",
      properties: {
        status: { type: "string", description: "Order status (pending, processing, completed, etc.)" },
        per_page: { type: "number", description: "Results per page (default 20)" },
        page: { type: "number", description: "Page number (default 1)" },
      },
    },
    _method: "GET",
    _path: "/wc/orders",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "get-order",
    description: "Get a single WooCommerce order by ID.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/wc/orders/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "create-order",
    description: "Create a new WooCommerce order.",
    inputSchema: {
      type: "object",
      properties: {
        billing: { type: "object", description: "Billing address object" },
        shipping: { type: "object", description: "Shipping address object" },
      },
    },
    _method: "POST",
    _path: "/wc/orders",
    _pathParams: [],
    _bodyParams: ["billing", "shipping"],
  },
  {
    name: "update-order-status",
    description: "Update an order's status.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
        status: { type: "string", description: "New status (pending, processing, completed, etc.)" },
      },
      required: ["id", "status"],
    },
    _method: "PUT",
    _path: "/wc/orders/{id}/status",
    _pathParams: ["id"],
    _bodyParams: ["status"],
  },
  {
    name: "delete-order",
    description: "Delete a WooCommerce order permanently.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
      },
      required: ["id"],
    },
    _method: "DELETE",
    _path: "/wc/orders/{id}",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "get-order-notes",
    description: "Get all notes for a specific order.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
      },
      required: ["id"],
    },
    _method: "GET",
    _path: "/wc/orders/{id}/notes",
    _pathParams: ["id"],
    _bodyParams: [],
  },
  {
    name: "add-order-note",
    description: "Add a note to an order.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
        note: { type: "string", description: "Note content" },
        is_customer_note: { type: "boolean", description: "Whether to show to customer" },
      },
      required: ["id", "note"],
    },
    _method: "POST",
    _path: "/wc/orders/{id}/notes",
    _pathParams: ["id"],
    _bodyParams: ["note", "is_customer_note"],
  },
  {
    name: "delete-order-note",
    description: "Delete a note from an order.",
    inputSchema: {
      type: "object",
      properties: {
        id: { type: "number", description: "Order ID" },
        note_id: { type: "number", description: "Note ID" },
      },
      required: ["id", "note_id"],
    },
    _method: "DELETE",
    _path: "/wc/orders/{id}/notes/{note_id}",
    _pathParams: ["id", "note_id"],
    _bodyParams: [],
  },

  // =================== INVENTORY (2) ===================

  {
    name: "get-inventory-report",
    description: "Get inventory report with stock status counts.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/wc/inventory",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "list-stock-alerts",
    description: "List products with low or out-of-stock alerts.",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/wc/stock-alerts",
    _pathParams: [],
    _bodyParams: [],
  },

  // =================== STORE SETTINGS (2) ===================

  {
    name: "get-store-settings",
    description: "Get WooCommerce store settings (address, currency, etc.).",
    inputSchema: {
      type: "object",
      properties: {},
    },
    _method: "GET",
    _path: "/wc/settings",
    _pathParams: [],
    _bodyParams: [],
  },
  {
    name: "update-store-settings",
    description: "Update WooCommerce store settings.",
    inputSchema: {
      type: "object",
      properties: {
        woocommerce_store_address: { type: "string" },
        woocommerce_store_city: { type: "string" },
        woocommerce_currency: { type: "string" },
        woocommerce_currency_pos: { type: "string" },
      },
    },
    _method: "PUT",
    _path: "/wc/settings",
    _pathParams: [],
    _bodyParams: ["woocommerce_store_address", "woocommerce_store_city", "woocommerce_currency", "woocommerce_currency_pos"],
  },
];
