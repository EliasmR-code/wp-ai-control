# 🎉 WP AI Control

**AI infrastructure for WordPress. 166 tools. 12 builders. MCP protocol. 27 CLI commands. WebMCP ready.**

The leading AI infrastructure layer for WordPress. Safe by default, duplicate-first by design, and built for teams that need approvals, traceability, and fast delivery across production WordPress sites.

[![WordPress logo](https://wordpress.org/StyleBook/raw/trunk/assets/images/wordpress-logo.svg)] WordPress
[![WooCommerce logo](https://woocommerce.com/wp-content/themes/woo-2024/assets/images/woo-logo.svg)] WooCommerce
[![Elementor logo](https://elementor.com/wp-content/uploads/2023/03/cropped-favicon-32x32.png)] Elementor
[![Bricks logo](https://bricksbuilder.io/wp-content/uploads/2024/01/cropped-Bricks-Icon-192x192.png)] Bricks
[![Oxygen logo](https://oxygenbuilder.com/wp-content/uploads/2020/05/oxygen-logo-icon.png)] Oxygen
[![Beaver Builder logo](https://www.wpbeaverbuilder.com/wp-content/uploads/2023/02/cropped-beaver-builder-icon-32x32.png)] Beaver Builder
[![Breakdance logo](https://breakdance.com/wp-content/uploads/2023/03/Breakdance-Icon-IDFKNpcr-4_1.svg)] Breakdance
[![Divi logo](https://www.elegantthemes.com/gallery/wp-content/uploads/2024/01/cropped-divi5-icon-1.png)] Divi 4/5
[![WPBakery logo](https://wpbakery.com/wp-content/uploads/2023/01/wpbakery-icon.png)] WPBakery
[![Visual Composer logo](https://visualcomposer.com/app/uploads/2023/11/visual-composer-favicon-32x32.png)] Visual Composer
[![Brizy logo](https://brizy.io/wp-content/uploads/2024/01/cropped-brizy-logo-32x32.png)] Brizy
[![Thrive Architect logo](https://thrivethemes.com/architect/wp-content/uploads/2023/02/thrive-architect-icon-32x32.png)] Thrive Architect
[![Flatsome logo](https://flatsome.net/wp-content/uploads/2023/01/flatsome-ux-builder-logo.png)] Flatsome
[![Kadence Theme](https://www.kadencethemes.com/wp-content/uploads/2023/01/kadence-logo.png)] Kadence Theme + Blocks

---

## 🚀 Ship WordPress updates at AI speed.

**Same seatbelt · every assistant · every builder**

Independent AI infrastructure layer for WordPress ecosystem.

**Safe by default**, duplicate-first by design, and built for teams that need approvals, traceability, and fast delivery across production WordPress sites.

---

## 🎯 Features

### ⚡️ **166 AI Tools Total**
- **32** Original WordPress tools (Pages, Posts, Media, Plugins, Menus, Taxonomies)
- **32** Additional tools (Users, Comments, Media, Terms, Settings, Themes, Meta, Search, Widgets, Bulk)
- **21** WooCommerce tools (Products, Orders, Inventory, Store Settings)
- **54** Advanced Custom Fields tools (Field Groups, Fields, Post Meta, Options Pages, Types, Bulk, Analysis)
- **27** Widget tools (Individual, Sidebars, Positioning, Analysis)

### 🏗️ **12 Page Builders**
| Builder | Supported | Deep Intelligence |
|---|---|---|
| Gutenberg | ✅ | ✅ |
| Elementor | ✅ | ✅ |
| Divi 4/5 | ✅ | ✅ |
| Bricks | ✅ | ✅ |
| Oxygen | ✅ | ✅ |
| Breakdance | ✅ | ✅ |
| Flatsome | ✅ | ✅ |
| Kadence Theme | ✅ | ✅ |
| Kadence Blocks | ✅ | ✅ |
| WPBakery | ✅ | ❌ |
| Beaver Builder | ✅ | ❌ |
| Brizy | ✅ | ❌ |
| Thrive Architect | ✅ | ❌ |

### 🖥 **CLI Tool** (27+ commands)
```bash
$ wpai setup
$ wpai pages
$ wpai post-update 123 -t "New Title"
$ wpai products
$ wpai acf-field-groups
```

### 🌐 **WebMCP** (Browser AI)
- Native WebMCP support in plugin
- Works with Chrome 146+, Claude Desktop
- No desktop app required
- 166 tools via browser

### 📚 **Skills Library** (26 workflows)
- WordPress Site DNA analysis
- SEO & AEO Amplifier
- WooCommerce Health Check
- Technical Debt Audit
- 7 Builder Migrations (Divi→Bricks, Elementor→Gutenberg, etc.)

---

## 🚀 How it works

### 1. Install & Connect
```bash
# 1. Upload plugin to /wp-content/plugins/
# 2. Activate in WordPress admin
# 3. Go to Settings > WP AI Control
# 4. Generate API key
# 5. Connect your AI tool
```

### 2. Use with your AI assistant

**Claude Desktop:**
```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/path/to/wp-ai-control-mcp/index.js"],
      "env": {
        "WP_URL": "https://yoursite.com",
        "WP_API_KEY": "wpaic_live_xxxxxxxx"
      }
    }
  }
}
```

**Cursor / Cline:**
```json
{
  "mcpServers": {
    "wp-ai-control": {
      "command": "node",
      "args": ["/path/to/wp-ai-control-mcp/index.js"]
    }
  }
}
```

**Browser AI (Chrome 146+):**
- Plugin includes native WebMCP support
- Connect to: `https://yoursite.com/wp-json/wp-ai-control/v1/mcp`

---

## 📊 Available Tools (166 total)

### Context (3 - read-only)
- `get-site-context` - Get site info (WP version, theme, plugins)
- `get-builder-info` - Detect active page builder (12 builders)
- `get-theme-docs` - Get active theme documentation

### Pages (5)
- `list-pages` - List pages with search, status filter, pagination
- `read-page` - Get full page content and metadata
- `update-page` - Update page title, content, or status
- `delete-page` - Move a page to trash
- `create-page-duplicate` - Create exact duplicate of a page

### Posts (4)
- `list-posts` - List posts with search, status filter
- `read-post` - Get full post content and metadata
- `update-post` - Update post title, content, or status
- `delete-post` - Move a post to trash

### Builder (2)
- `extract-builder-content` - Extract page builder content as JSON (12 builders)
- `inject-builder-content` - Replace page builder content from JSON

### Media (4)
- `upload-media` - Upload file to WordPress media library
- `list-media` - List media files with MIME type filter
- `delete-media` - Delete a media file
- `update-media-meta` - Update media meta fields

### Analysis (4 - read-only)
- `analyze-seo` - SEO analysis (meta, headings, keywords)
- `analyze-performance` - Performance analysis (Core Web Vitals)
- `analyze-aeo` - AI Engine Optimization (structured data)
- `analyze-accessibility` - Accessibility analysis (WCAG compliance)

### Plugins (4)
- `list-plugins` - List all installed plugins
- `install-plugin` - Install plugin from WordPress.org
- `activate-plugin` - Activate an installed plugin
- `deactivate-plugin` - Deactivate a plugin

### Menus (3 - read-only)
- `list-menus` - List all navigation menus
- `get-menu` - Get menu structure with all items
- `list-menu-locations` - List theme menu locations

### Taxonomies (2 - read-only)
- `list-taxonomies` - List all registered taxonomies
- `list-terms` - List terms in a taxonomy

### Taxonomy CRUD (4)
- `create-term` - Create a new term in a taxonomy
- `get-term` - Get a term by ID
- `update-term` - Update a term's name, description, or parent
- `delete-term` - Delete a term from a taxonomy

### Snapshots (2)
- `list-snapshots` - List available content snapshots
- `restore-snapshot` - Roll back to a previous snapshot

### Usage (2 - read-only)
- `get-usage` - Get usage statistics
- `get-plan-info` - Get plugin version and feature information

### Users (5)
- `list-users` - List all WordPress users
- `get-user` - Get user details by ID
- `create-user` - Create a new WordPress user
- `update-user` - Update an existing user's details
- `delete-user` - Delete a user from WordPress

### Comments (5)
- `list-comments` - List comments with optional status filter
- `get-comment` - Get a single comment by ID
- `approve-comment` - Approve a comment for display
- `spam-comment` - Mark a comment as spam
- `delete-comment` - Delete a comment permanently

### Site Settings (2)
- `get-site-settings` - Get WordPress site settings
- `update-site-settings` - Update WordPress site settings

### Themes (3)
- `list-themes` - List all installed WordPress themes
- `activate-theme` - Activate a theme by slug
- `update-theme` - Update the active theme to latest version

### Custom Fields / Meta (3)
- `list-post-meta` - List all custom field meta
- `update-post-meta` - Update multiple meta fields
- `delete-post-meta` - Delete a specific meta field

### Search (1)
- `search-content` - Search across posts and pages by keyword

### Bulk Operations (2)
- `bulk-update-posts` - Update multiple posts/pages at once
- `bulk-delete-posts` - Delete multiple posts/pages at once

### WooCommerce (21)
**Products (9):** `list-products`, `get-product`, `create-product`, `update-product`, `delete-product`, `update-product-stock`, `list-product-categories`, `get-product-reviews`, `update-product-review`

**Orders (8):** `list-orders`, `get-order`, `create-order`, `update-order-status`, `delete-order`, `get-order-notes`, `add-order-note`, `delete-order-note`

**Inventory (2):** `get-inventory-report`, `list-stock-alerts`

**Store Settings (2):** `get-store-settings`, `update-store-settings`

### Advanced Custom Fields (54)
**Field Groups (8):** `list-field-groups`, `get-field-group`, `create-field-group`, `update-field-group`, `delete-field-group`, `list-fields-in-group`, `duplicate-field-group`, `assign-field-group`

**Fields (12):** `list-fields`, `get-field`, `create-field`, `update-field`, `delete-field`, `duplicate-field`, `export-fields`, `import-fields`, `validate-field`, `bulk-update-fields`, `clone-field`

**Post Meta with ACF (8):** `get-post-acf-fields`, `update-post-acf-fields`, `get-post-acf-field`, `update-post-acf-field`, `render-post-acf-fields`, `get-post-layouts`, `get-flexible-content`, `get-repeater-field`

**Options Pages (4):** `list-options-pages`, `create-options-page`, `get-options-page`, `get-options-page-fields`

**Field Types & Validation (6):** `list-field-types`, `get-field-type`, `validate-acf-rule`, `list-location-rules`, `update-location-rules`, `list-cloneable-fields`

**Bulk Operations (4):** `bulk-update-acf-meta`, `bulk-clone-fields`, `export-field-group`, `import-field-group`

**Field Group Analysis (6):** `analyze-field-group-usage`, `get-field-dependencies`, `find-orphaned-fields`, `check-duplicate-fields`, `get-conditional-logic`, `analyze-acf-performance`

### Widgets (27)
**Widgets Individual (12):** `list-widgets`, `get-widget`, `create-widget`, `update-widget`, `delete-widget`, `duplicate-widget`, `list-available-widgets`, `get-widget-settings`, `update-widget-settings`, `preview-widget`, `bulk-update-widgets`, `bulk-delete-widgets`

**Sidebars (6):** `list-sidebars`, `get-sidebar`, `register-sidebar`, `unregister-sidebar`, `get-sidebar-widgets`, `clear-sidebar`

**Widget Positioning (5):** `move-widget`, `reorder-widget`, `add-widget-to-sidebar`, `remove-widget-from-sidebar`, `swap-widgets`

**Widget Analysis (4):** `get-widget-usage-stats`, `find-orphaned-widgets`, `find-duplicate-widgets`, `analyze-sidebar-usage`

---

## 💻 Requirements

- **Node.js** 18+
- **WordPress** 6.0+
- **PHP** 7.4+
- **WooCommerce** (optional, for 21 tools)
- **Advanced Custom Fields** (optional, for 54 tools)
- **Chrome 146+** (for WebMCP/Browser AI)

---

## 📄 Installation

### WordPress Plugin
1. Download or clone `wp-ai-control/`
2. Upload to `/wp-content/plugins/`
3. Activate through WordPress admin
4. Go to **Settings > WP AI Control**
5. Generate an API key
6. Configure your AI tool

### MCP Server (Node.js)
```bash
cd wp-ai-control-mcp
npm install
cp .env.example .env
# Edit .env with your WordPress URL and API key
```

### CLI Tool
```bash
npm install -g wp-ai-control-cli
# or use directly
npx wp-ai-control-cli setup
```

### Skills Library
```bash
npx @wp-ai-control/skills setup
```

---

## 🎉 Comparison with Other AI WordPress Tools

| Feature | WP AI Control | Respira | CodeWP |
|---|---|---|---|
| **Total Tools** | **166** | 234 | ~15 |
| **Builders Supported** | **12** | 12 | 1 (Gutenberg) |
| **WooCommerce** | ✅ 21 tools | ✅ 21 tools | ❌ |
| **ACF Support** | ✅ 54 tools | ✅ 54 tools | ❌ |
| **Widgets** | ✅ 27 tools | ❌ 27 tools | ❌ |
| **CLI Tool** | ✅ 27+ commands | ✅ | ❌ |
| **WebMCP** | ✅ Native in plugin | ✅ | ❌ |
| **Skills Library** | ✅ 26 workflows | ✅ 26 | ❌ |
| **Multisite** | ❌ | ✅ | ❌ |
| **Pricing** | Free / Open Source | Paid plans | Freemium |

---

## 🔒 Security & Safety

- **Duplicate-First**: AI only updates duplicate pages. Your live site is never touched until you approve.
- **Audit Logs**: Complete traceability with configurable retention (7-365 days)
- **API Key Auth**: Secure authentication with rate limiting (60 req/min)
- **Sanitization**: All inputs are sanitized before processing
- **Permission Checks**: Read/write/admin permission levels

---

## 📚 Documentation

- **Plugin README**: See `wp-ai-control/README.md`
- **MCP Server README**: See `wp-ai-control-mcp/README.md`
- **CLI Tool README**: See `wp-ai-control-cli/README.md`
- **Skills Library README**: See `wp-ai-control-skills/README.md`

---

## 💼 License

**GPLv2 or later** - Free and open source.

---

## 🤝 Support

- **Issues**: Report bugs or request features via GitHub issues
- **Discussions**: Join the community for help and tips
- **Docs**: Full documentation coming soon

---

**Built with ♡ for the WordPress community.**
