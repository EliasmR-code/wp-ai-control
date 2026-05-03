=== WP AI Control ===
Contributors: Developer
Tags: AI, REST API, MCP, WordPress, AI agents, automation
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Control your WordPress site via AI agents using a secure REST API and MCP protocol.

== Description ==

WP AI Control allows AI coding assistants like Claude, Cursor, Windsurf, and Cline to safely manage your WordPress site through a secure REST API and Model Context Protocol (MCP).

= Features =

* **Secure REST API** - API key-based authentication with rate limiting
* **32 AI Tools** - Complete CRUD operations for pages, posts, media, plugins, menus, and more
* **Builder Support** - Works with Gutenberg and popular page builders
* **Audit Logging** - Track all changes with configurable retention (7-365 days)
* **No External Dependencies** - All processing happens locally on your server
* **Multiple API Keys** - Create and manage multiple API keys with different permission levels
* **Rate Limiting** - 60 requests per minute per API key

= Supported AI Tools =

* **Context**: Site info, builder info, theme docs
* **Pages**: List, read, update, delete, duplicate
* **Posts**: List, read, update, delete
* **Builder**: Extract and inject builder content
* **Media**: Upload media files
* **Analysis**: SEO, performance, AEO, accessibility
* **Plugins**: List, install, activate, deactivate
* **Menus**: List menus, get menu, list locations
* **Taxonomies**: List taxonomies and terms
* **Snapshots**: List and restore snapshots
* **Usage**: Get usage stats and plan info

= Supported AI Assistants =

* Claude Desktop / Claude Code
* Cursor
* Windsurf
* Cline (VS Code)
* Continue.dev
* Any MCP-compatible AI assistant

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through the 'Plugins' menu in WordPress
3. Go to Settings > WP AI Control
4. Generate an API key
5. Configure your AI tool with the MCP server

== Frequently Asked Questions ==

= Is this free? =

Yes, WP AI Control is completely free with no usage limits.

= Is my data sent to external servers? =

No. All processing happens locally on your WordPress server.

= How do I connect my AI assistant? =

Use the provided MCP server package (wp-ai-control-mcp) with your AI tool.

== Changelog ==

= 1.0.0 =
* Initial release
* 32 tools for complete WordPress management
* Secure API key authentication
* Audit logging with configurable retention
* Rate limiting for security
