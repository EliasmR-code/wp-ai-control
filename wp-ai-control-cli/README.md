# WP AI Control CLI

A command-line interface for managing WordPress sites via the WP AI Control plugin. Provides 27+ commands for pages, posts, media, WooCommerce, ACF, widgets, and more.

## Installation

```bash
npm install -g wp-ai-control-cli
```

Or use directly without installing:

```bash
npx wp-ai-control-cli <command>
```

## Quick Start

### 1. Setup (creates `.wpai` config file)

```bash
wpai setup
```

You'll be prompted for:
- WordPress URL
- API Key (generated from Settings > WP AI Control)

### 2. Use commands

```bash
# List pages
wpai pages

# Search posts
wpai posts -s "news"

# Read a page
wpai page-read 123

# Update a post
wpai post-update 456 -t "New Title" -c "New content"

# Install a plugin
wpai plugin-install contact-form-7

# WooCommerce: list products
wpai products

# ACF: list field groups
wpai acf-field-groups

# Widgets: list sidebars
wpai sidebars
```

## All Commands

### Setup
- `setup` - Interactive setup wizard (creates `.wpai`)

### Pages (5 commands)
- `pages` - List pages (with search, pagination)
- `page-read <id>` - Read a page
- `page-update <id>` - Update page (--title, --content, --status)
- `page-delete <id>` - Delete page (use --force)
- `page-duplicate <id>` - Duplicate a page

### Posts (4 commands)
- `posts` - List posts
- `post-read <id>` - Read a post
- `post-update <id>` - Update post
- `post-delete <id>` - Delete post

### Site Info (2 commands)
- `site-info` - Get WordPress site information
- `builder-info` - Detect active page builder

### Users (2 commands)
- `users` - List all users
- `user-create <username> <email>` - Create a user

### Comments (2 commands)
- `comments` - List comments
- `comment-approve <id>` - Approve a comment

### Media (1 command)
- `media` - List media files

### WooCommerce (2 commands)
- `products` - List products
- `product-create <name>` - Create a product

### Plugins (3 commands)
- `plugins` - List installed plugins
- `plugin-install <slug>` - Install plugin
- `plugin-activate <slug>` - Activate plugin

### Themes (2 commands)
- `themes` - List installed themes
- `theme-activate <slug>` - Activate theme

### Analysis (2 commands)
- `analyze-seo <page_id>` - SEO analysis
- `analyze-performance <page_id>` - Performance analysis

### Menus (1 command)
- `menus` - List navigation menus

### API Keys (2 commands)
- `api-keys` - List API keys
- `api-key-generate <name>` - Generate new API key

### Bulk Operations (1 command)
- `bulk-update-posts` - Update multiple posts

### Widgets (2 commands)
- `widgets` - List all widgets
- `sidebars` - List sidebars

### ACF (2 commands)
- `acf-field-groups` - List ACF field groups
- `acf-fields <group_id>` - List fields in group

### Raw API (1 command)
- `run <command>` - Run raw API command (--method, --body)

## Examples

```bash
# Export all pages to JSON
wpai export pages.json -t pages

# Import data from JSON
wpai import data.json -t posts

# Bulk update posts
wpai bulk-update-posts -i "123,456,789" -t "New Title"

# Run raw API call
wpai run "/wc/orders" -m GET

# Use with different config
WP_URL=https://other-site.com wpai pages
```

## Configuration

The CLI reads from `.wpai` file in current directory:

```
WP_URL=https://yoursite.com
WP_API_KEY=wpaic_live_xxxxxxxxxxxxxxxxxxxxxxxx
```

Or use environment variables:
```bash
export WP_URL=https://yoursite.com
export WP_API_KEY=wpaic_live_xxxxxxxxxxxxxxxxxxxxxxxx
```

## Requirements

- Node.js 18+
- WP AI Control plugin installed and activated
- Valid API key with read/write permissions

## License

MIT
