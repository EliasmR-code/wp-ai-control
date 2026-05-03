# WP AI Control MCP Server

MCP (Model Context Protocol) server that connects AI assistants (Claude, Cursor, Windsurf, Cline) to your WordPress site via the WP AI Control plugin.

## Architecture

```
AI Assistant (Claude/Cursor/etc)  <--MCP-->  wp-ai-control-mcp  <--REST API-->  WP AI Control Plugin  <--->  WordPress
```

## Installation

```bash
cd wp-ai-control-mcp
npm install
```

## Configuration

Copy the example env file and configure it:

```bash
cp .env.example .env
```

Edit `.env` with your WordPress site URL and API key:

```
WP_URL=https://tu-sitio.com
WP_API_KEY=wpaic_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

You can generate an API key from your WordPress admin at **Settings > WP AI Control**.

## Usage with Claude Desktop

Add to your `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/ruta/completa/wp-ai-control-mcp/index.js"],
      "env": {
        "WP_URL": "https://tu-sitio.com",
        "WP_API_KEY": "wpaic_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
      }
    }
  }
}
```

## Usage with Cursor

Add to your `.cursor/mcp.json`:

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/ruta/completa/wp-ai-control-mcp/index.js"]
    }
  }
}
```

## Usage with Cline (VS Code)

Add to your VS Code settings or `.vscode/mcp.json`:

```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/ruta/completa/wp-ai-control-mcp/index.js"]
    }
  }
}
```

## Test Connection

You can test the MCP server with a one-liner via stdin:

```bash
echo '{"jsonrpc":"2.0","id":1,"method":"tools/list"}' | node index.js
```

## Available Tools (166 total)

### Context (3 - read-only)
- **get-site-context** - Get site info (WP version, theme, plugins, etc.)
- **get-builder-info** - Detect active page builder (12 supported: Gutenberg, Elementor, Divi, WPBakery, Bricks, Oxygen, Beaver Builder, Brizy, Thrive Architect, Breakdance, Flatsome, Kadence, Kadence Blocks)
- **get-theme-docs** - Get active theme documentation

### Pages (5)
- **list-pages** - List pages with search, status filter, pagination
- **read-page** - Get full page content and metadata
- **update-page** - Update page title, content, or status
- **delete-page** - Move a page to trash
- **create-page-duplicate** - Create exact duplicate of a page

### Posts (4)
- **list-posts** - List posts with search, status filter, pagination
- **read-post** - Get full post content and metadata
- **update-post** - Update post title, content, or status
- **delete-post** - Move a post to trash

### Builder (2)
- **extract-builder-content** - Extract page builder content as JSON
- **inject-builder-content** - Replace page builder content from JSON

### Media (4 - original: 1, additional: 3)
- **upload-media** - Upload file to WordPress media library
- **list-media** - List media files with MIME type filter
- **delete-media** - Delete a media file
- **update-media-meta** - Update media meta fields

### Analysis (4 - read-only)
- **analyze-seo** - SEO analysis (title, headings, keywords, recommendations)
- **analyze-performance** - Performance analysis (content size, caching, recommendations)
- **analyze-aeo** - AI Engine Optimization analysis (structured data, snippets)
- **analyze-accessibility** - Accessibility analysis (WCAG, alt text, ARIA)

### Plugins (4)
- **list-plugins** - List all installed plugins with status
- **install-plugin** - Install plugin from WordPress.org
- **activate-plugin** - Activate an installed plugin
- **deactivate-plugin** - Deactivate an active plugin

### Menus (3 - read-only)
- **list-menus** - List all navigation menus
- **get-menu** - Get menu structure with all items
- **list-menu-locations** - List theme menu locations

### Taxonomies (2 - read-only)
- **list-taxonomies** - List all registered taxonomies
- **list-terms** - List terms in a taxonomy

### Taxonomy CRUD (4 - NEW)
- **create-term** - Create a new term in a taxonomy
- **get-term** - Get a term by ID
- **update-term** - Update a term's name, description, or parent
- **delete-term** - Delete a term from a taxonomy

### Snapshots (2)
- **list-snapshots** - List available content snapshots
- **restore-snapshot** - Roll back to a previous snapshot

### Usage (2 - read-only, local data)
- **get-usage** - Get usage statistics from local wp_options
- **get-plan-info** - Get plugin version and feature information

### Users (5 - NEW)
- **list-users** - List all WordPress users
- **get-user** - Get user details by ID
- **create-user** - Create a new WordPress user
- **update-user** - Update an existing user's details
- **delete-user** - Delete a user from WordPress

### Comments (5 - NEW)
- **list-comments** - List comments with optional status and post filter
- **get-comment** - Get a single comment by ID
- **approve-comment** - Approve a comment for display
- **spam-comment** - Mark a comment as spam
- **delete-comment** - Delete a comment permanently

### Site Settings (2 - NEW)
- **get-site-settings** - Get WordPress site settings
- **update-site-settings** - Update WordPress site settings

### Themes (3 - NEW)
- **list-themes** - List all installed WordPress themes
- **activate-theme** - Activate a theme by slug
- **update-theme** - Update the active theme to latest version

### Custom Fields / Meta (3 - NEW)
- **list-post-meta** - List all custom field meta for a post or page
- **update-post-meta** - Update multiple meta fields for a post or page
- **delete-post-meta** - Delete a specific meta field from a post or page

### Search (1 - NEW)
- **search-content** - Search across posts and pages by keyword

### Widgets (3 - NEW)
- **list-widgets** - List all widgets in all sidebars
- **update-widget** - Update a widget's settings
- **list-sidebars** - List all registered widget areas

### Bulk Operations (2 - NEW)
- **bulk-update-posts** - Update multiple posts/pages at once
- **bulk-delete-posts** - Delete multiple posts/pages at once

### WooCommerce (21 - NEW)
#### Products (9)
- **list-products** - List WooCommerce products with search, category filter, pagination
- **get-product** - Get a single product by ID
- **create-product** - Create a new WooCommerce product
- **update-product** - Update a WooCommerce product
- **delete-product** - Delete a WooCommerce product permanently
- **update-product-stock** - Update product stock quantity
- **list-product-categories** - List all product categories
- **get-product-reviews** - Get reviews for a specific product
- **update-product-review** - Update a product review

#### Orders (8)
- **list-orders** - List WooCommerce orders with status filter
- **get-order** - Get a single order by ID
- **create-order** - Create a new WooCommerce order
- **update-order-status** - Update an order's status
- **delete-order** - Delete a WooCommerce order permanently
- **get-order-notes** - Get all notes for a specific order
- **add-order-note** - Add a note to an order
- **delete-order-note** - Delete a note from an order

#### Inventory (2)
- **get-inventory-report** - Get inventory report with stock status counts
- **list-stock-alerts** - List products with low or out-of-stock alerts

#### Store Settings (2)
- **get-store-settings** - Get WooCommerce store settings
- **update-store-settings** - Update WooCommerce store settings

### Advanced Custom Fields (54 - NEW)
#### Field Groups (8)
- **list-field-groups** - List all ACF field groups
- **get-field-group** - Get a field group by ID
- **create-field-group** - Create a new field group
- **update-field-group** - Update a field group
- **delete-field-group** - Delete a field group
- **list-fields-in-group** - List all fields in a group
- **duplicate-field-group** - Duplicate an entire field group
- **assign-field-group** - Assign field group to post types

#### Fields (12)
- **list-fields** - List ACF fields with filters
- **get-field** - Get a field by ID
- **create-field** - Create a new ACF field
- **update-field** - Update an ACF field
- **delete-field** - Delete an ACF field
- **duplicate-field** - Duplicate an ACF field
- **export-fields** - Export ACF fields by IDs
- **import-fields** - Import ACF fields from exported data
- **validate-field** - Validate an ACF field configuration
- **bulk-update-fields** - Update multiple fields at once
- **clone-field** - Clone a field to a different parent

#### Post Meta with ACF (8)
- **get-post-acf-fields** - Get all ACF fields and values for a post
- **update-post-acf-fields** - Update multiple ACF fields for a post
- **get-post-acf-field** - Get a specific ACF field value
- **update-post-acf-field** - Update a specific ACF field value
- **render-post-acf-fields** - Render ACF form for a post (returns HTML)
- **get-post-layouts** - Get layout fields for a post
- **get-flexible-content** - Get flexible content field layouts
- **get-repeater-field** - Get repeater field rows

#### Options Pages (4)
- **list-options-pages** - List all ACF options pages
- **create-options-page** - Create a new options page
- **get-options-page** - Get an options page by slug
- **get-options-page-fields** - Get fields for an options page

#### Field Types & Validation (6)
- **list-field-types** - List all available ACF field types
- **get-field-type** - Get details about a field type
- **validate-acf-rule** - Validate an ACF location rule
- **list-location-rules** - List all available location rules
- **update-location-rules** - Update location rules for a field group
- **list-cloneable-fields** - List all cloneable ACF fields

#### Bulk Operations (4)
- **bulk-update-acf-meta** - Update ACF fields across multiple posts
- **bulk-clone-fields** - Clone fields from one post to multiple targets
- **export-field-group** - Export complete field group with fields
- **import-field-group** - Import complete field group with fields

#### Field Group Analysis (6)
- **analyze-field-group-usage** - Analyze where a field group is used
- **get-field-dependencies** - Get conditional logic dependencies
- **find-orphaned-fields** - Find fields not assigned to any group
- **check-duplicate-fields** - Check for duplicate field names
- **get-conditional-logic** - Get all conditional logic rules
- **analyze-acf-performance** - Analyze ACF performance issues*

### Widgets (27 - NEW)
#### Widgets - Individual (12)
- **list-widgets** - List all active widgets across sidebars
- **get-widget** - Get a specific widget's details and settings*
- **create-widget** - Create a new widget instance and add to sidebar*
- **update-widget** - Update an existing widget's settings*
- **delete-widget** - Delete a widget from all sidebars*
- **duplicate-widget** - Duplicate a widget within the same sidebar*
- **list-available-widgets** - List all available widget types that can be created*
- **get-widget-settings** - Get a widget's current settings*
- **update-widget-settings** - Update a widget's settings directly*
- **preview-widget** - Preview a widget's output as HTML*
- **bulk-update-widgets** - Update multiple widgets with the same settings*
- **bulk-delete-widgets** - Delete multiple widgets at once*

#### Sidebars (6)
- **list-sidebars** - List all registered widget areas (sidebars)*
- **get-sidebar** - Get a specific sidebar's details and its widgets*
- **register-sidebar** - Register a new sidebar (widget area)*
- **unregister-sidebar** - Unregister a sidebar*
- **get-sidebar-widgets** - Get all widgets assigned to a sidebar*
- **clear-sidebar** - Remove all widgets from a sidebar*

#### Widget Positioning (5)
- **move-widget** - Move a widget from one sidebar to another*
- **reorder-widget** - Reorder widgets within a sidebar*
- **add-widget-to-sidebar** - Add an existing widget to a sidebar*
- **remove-widget-from-sidebar** - Remove a widget from a specific sidebar*
- **swap-widgets** - Swap positions of two widgets*

#### Widget Analysis (4)
- **get-widget-usage-stats** - Get statistics about widget usage across sidebars*
- **find-orphaned-widgets** - Find widgets not assigned to any sidebar*
- **find-duplicate-widgets** - Check for widgets with identical settings*
- **analyze-sidebar-usage** - Analyze sidebar usage and widget distribution*

## Requirements

- Node.js 18+
- WordPress 6.0+
- WP AI Control plugin installed and activated
- PHP 7.4+
