#!/usr/bin/env node
import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { CallToolRequestSchema, ListToolsRequestSchema } from '@modelcontextprotocol/sdk/types.js';

const WP_URL = process.env.WP_URL || 'http://localhost/wp-json/wp-ai-control/v1';
const WP_API_KEY = process.env.WP_API_KEY || '';

const skills = {
  'wp-content-analyzer': {
    name: 'wp-content-analyzer',
    description: 'Analyze WordPress site content - posts, pages, media, categories',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-seo-audit': {
    name: 'wp-seo-audit',
    description: 'Complete SEO audit - meta tags, sitemaps, robots, schema',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-image-optimizer': {
    name: 'wp-image-optimizer',
    description: 'Optimize WordPress images - compress, resize, generate WebP',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, quality: { type: 'number', default: 80 } }, required: ['site_url'] }
  },
  'wp-db-cleaner': {
    name: 'wp-db-cleaner',
    description: 'Clean WordPress database - remove revisions, trash, optimize tables',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, options: { type: 'object' } }, required: ['site_url'] }
  },
  'wp-security-scan': {
    name: 'wp-security-scan',
    description: 'Security scan - check permissions, vulnerabilities, exposed files',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-backup-create': {
    name: 'wp-backup-create',
    description: 'Create WordPress backup - files and database',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, destination: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-migration-export': {
    name: 'wp-migration-export',
    description: 'Export WordPress for migration - XML, WXR, database dump',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, format: { type: 'string', enum: ['xml', 'wxr', 'sql'] } }, required: ['site_url'] }
  },
  'wp-theme-analyzer': {
    name: 'wp-theme-analyzer',
    description: 'Analyze WordPress theme - files, functions, hooks, compatibility',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, theme_name: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-plugin-audit': {
    name: 'wp-plugin-audit',
    description: 'Audit WordPress plugins - check conflicts, outdated, security',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-performance-test': {
    name: 'wp-performance-test',
    description: 'Performance test - speed, Core Web Vitals, TTFB',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-accessibility-check': {
    name: 'wp-accessibility-check',
    description: 'Check accessibility - WCAG compliance, ARIA, contrast',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-schema-generator': {
    name: 'wp-schema-generator',
    description: 'Generate schema.org markup - Article, Product, LocalBusiness',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, schema_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-content-translator': {
    name: 'wp-content-translator',
    description: 'Translate WordPress content - posts, pages, strings',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, content_id: { type: 'number' }, target_lang: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-ecommerce-audit': {
    name: 'wp-ecommerce-audit',
    description: 'WooCommerce audit - products, orders, settings, performance',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-form-generator': {
    name: 'wp-form-generator',
    description: 'Generate WordPress forms - contact, checkout, survey forms',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, form_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-menu-builder': {
    name: 'wp-menu-builder',
    description: 'Build WordPress menus - create, organize, assign locations',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, menu_name: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-template-creator': {
    name: 'wp-template-creator',
    description: 'Create page templates - custom, archive, singular templates',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, template_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-shortcode-generator': {
    name: 'wp-shortcode-generator',
    description: 'Generate WordPress shortcodes - custom, wrapped, enclosed',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, shortcode_name: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-hook-explorer': {
    name: 'wp-hook-explorer',
    description: 'Explore WordPress hooks - actions, filters, documentation',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, hook_type: { type: 'string', enum: ['action', 'filter', 'all'] } }, required: ['site_url'] }
  },
  'wp-taxonomy-manager': {
    name: 'wp-taxonomy-manager',
    description: 'Manage taxonomies - create, modify, register custom taxonomies',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, taxonomy_name: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-user-manager': {
    name: 'wp-user-manager',
    description: 'Manage WordPress users - roles, capabilities, bulk operations',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, action: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-api-tester': {
    name: 'wp-api-tester',
    description: 'Test WordPress REST API - endpoints, authentication, responses',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, endpoint: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-cache-manager': {
    name: 'wp-cache-manager',
    description: 'Manage cache - object, page, transients, CDN configuration',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, cache_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-log-analyzer': {
    name: 'wp-log-analyzer',
    description: 'Analyze WordPress logs - error, debug, audit logs',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, log_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-cron-manager': {
    name: 'wp-cron-manager',
    description: 'Manage cron jobs - schedule, monitor, create custom schedules',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, action: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-multisite-manager': {
    name: 'wp-multisite-manager',
    description: 'Manage WordPress Multisite - sites, users, network settings',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, network_action: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-ai-content-generator': {
    name: 'wp-ai-content-generator',
    description: 'Generate WordPress posts/pages from AI prompts',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, prompt: { type: 'string' }, post_type: { type: 'string', default: 'post' } }, required: ['site_url', 'prompt'] }
  },
  'wp-ai-image-generator': {
    name: 'wp-ai-image-generator',
    description: 'Generate AI images for featured images',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, prompt: { type: 'string' }, post_id: { type: 'number' } }, required: ['site_url', 'prompt'] }
  },
  'wp-ai-seo-writer': {
    name: 'wp-ai-seo-writer',
    description: 'AI-powered SEO content writing with keywords',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, topic: { type: 'string' }, keywords: { type: 'array' } }, required: ['site_url', 'topic'] }
  },
  'wp-product-importer': {
    name: 'wp-product-importer',
    description: 'Bulk import WooCommerce products from CSV',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, csv_url: { type: 'string' } }, required: ['site_url', 'csv_url'] }
  },
  'wp-inventory-manager': {
    name: 'wp-inventory-manager',
    description: 'Manage WooCommerce inventory - stock levels, tracking',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, action: { type: 'string' }, product_id: { type: 'number' } }, required: ['site_url'] }
  },
  'wp-price-optimizer': {
    name: 'wp-price-optimizer',
    description: 'Dynamic pricing based on rules and competition',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, product_id: { type: 'number' }, strategy: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-health-monitor': {
    name: 'wp-health-monitor',
    description: 'Real-time WordPress health status checks',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, check_type: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-uptime-monitor': {
    name: 'wp-uptime-monitor',
    description: 'Monitor website uptime and response time',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, interval: { type: 'number', default: 5 } }, required: ['site_url'] }
  },
  'wp-traffic-analyzer': {
    name: 'wp-traffic-analyzer',
    description: 'Analyze website traffic patterns',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, period: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-malware-scanner': {
    name: 'wp-malware-scanner',
    description: 'Detect malware, backdoors, malicious code',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, scan_deep: { type: 'boolean' } }, required: ['site_url'] }
  },
  'wp-firewall-config': {
    name: 'wp-firewall-config',
    description: 'Configure security firewall rules',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, rules: { type: 'object' } }, required: ['site_url'] }
  },
  'wp-login-protector': {
    name: 'wp-login-protector',
    description: 'Protect login from brute force attacks',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, max_attempts: { type: 'number', default: 5 } }, required: ['site_url'] }
  },
  'wp-staging-creator': {
    name: 'wp-staging-creator',
    description: 'Create WordPress staging environment',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, staging_url: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-deployment-manager': {
    name: 'wp-deployment-manager',
    description: 'Manage deployments from staging to production',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, action: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-clone-site': {
    name: 'wp-clone-site',
    description: 'Clone entire WordPress site to new location',
    inputSchema: { type: 'object', properties: { source_url: { type: 'string' }, destination_url: { type: 'string' } }, required: ['source_url', 'destination_url'] }
  },
  'wp-code-linter': {
    name: 'wp-code-linter',
    description: 'Analyze PHP/JS code quality and issues',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, file_path: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-git-integration': {
    name: 'wp-git-integration',
    description: 'Git version control for WordPress themes/plugins',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, repo_path: { type: 'string' }, action: { type: 'string' } }, required: ['site_url'] }
  },
  'wp-url-replacer': {
    name: 'wp-url-replacer',
    description: 'Bulk search and replace URLs in database',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, search: { type: 'string' }, replace: { type: 'string' } }, required: ['site_url', 'search', 'replace'] }
  },
  'wp-database-merge': {
    name: 'wp-database-merge',
    description: 'Merge WordPress databases',
    inputSchema: { type: 'object', properties: { site_url: { type: 'string' }, source_db: { type: 'object' } }, required: ['site_url'] }
  }
};

const skillHandlers = {
  'wp-content-analyzer': async (args) => {
    const response = await fetch(`${WP_URL}/content/analyze?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-seo-audit': async (args) => {
    const response = await fetch(`${WP_URL}/seo/audit?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-image-optimizer': async (args) => {
    const response = await fetch(`${WP_URL}/media/optimize`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, quality: args.quality || 80 })
    });
    return await response.json();
  },
  'wp-db-cleaner': async (args) => {
    const response = await fetch(`${WP_URL}/database/clean`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, options: args.options || {} })
    });
    return await response.json();
  },
  'wp-security-scan': async (args) => {
    const response = await fetch(`${WP_URL}/security/scan?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-backup-create': async (args) => {
    const response = await fetch(`${WP_URL}/backup/create`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, destination: args.destination || 'local' })
    });
    return await response.json();
  },
  'wp-migration-export': async (args) => {
    const response = await fetch(`${WP_URL}/migration/export`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, format: args.format || 'xml' })
    });
    return await response.json();
  },
  'wp-theme-analyzer': async (args) => {
    const response = await fetch(`${WP_URL}/theme/analyze`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, theme_name: args.theme_name || '' })
    });
    return await response.json();
  },
  'wp-plugin-audit': async (args) => {
    const response = await fetch(`${WP_URL}/plugins/audit?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-performance-test': async (args) => {
    const response = await fetch(`${WP_URL}/performance/test?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-accessibility-check': async (args) => {
    const response = await fetch(`${WP_URL}/accessibility/check?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-schema-generator': async (args) => {
    const response = await fetch(`${WP_URL}/schema/generate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, schema_type: args.schema_type || 'Article' })
    });
    return await response.json();
  },
  'wp-content-translator': async (args) => {
    const response = await fetch(`${WP_URL}/content/translate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, content_id: args.content_id, target_lang: args.target_lang })
    });
    return await response.json();
  },
  'wp-ecommerce-audit': async (args) => {
    const response = await fetch(`${WP_URL}/ecommerce/audit?site_url=${encodeURIComponent(args.site_url)}`, {
      headers: { 'Authorization': `Bearer ${WP_API_KEY}` }
    });
    return await response.json();
  },
  'wp-form-generator': async (args) => {
    const response = await fetch(`${WP_URL}/forms/generate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, form_type: args.form_type || 'contact' })
    });
    return await response.json();
  },
  'wp-menu-builder': async (args) => {
    const response = await fetch(`${WP_URL}/menus/build`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, menu_name: args.menu_name || 'Main Menu' })
    });
    return await response.json();
  },
  'wp-template-creator': async (args) => {
    const response = await fetch(`${WP_URL}/templates/create`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, template_type: args.template_type || 'page' })
    });
    return await response.json();
  },
  'wp-shortcode-generator': async (args) => {
    const response = await fetch(`${WP_URL}/shortcodes/generate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, shortcode_name: args.shortcode_name || '' })
    });
    return await response.json();
  },
  'wp-hook-explorer': async (args) => {
    const response = await fetch(`${WP_URL}/hooks/explore`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, hook_type: args.hook_type || 'all' })
    });
    return await response.json();
  },
  'wp-taxonomy-manager': async (args) => {
    const response = await fetch(`${WP_URL}/taxonomies/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, taxonomy_name: args.taxonomy_name || '' })
    });
    return await response.json();
  },
  'wp-user-manager': async (args) => {
    const response = await fetch(`${WP_URL}/users/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, action: args.action || 'list' })
    });
    return await response.json();
  },
  'wp-api-tester': async (args) => {
    const response = await fetch(`${WP_URL}/api/test`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, endpoint: args.endpoint || '/wp/v2/posts' })
    });
    return await response.json();
  },
  'wp-cache-manager': async (args) => {
    const response = await fetch(`${WP_URL}/cache/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, cache_type: args.cache_type || 'all' })
    });
    return await response.json();
  },
  'wp-log-analyzer': async (args) => {
    const response = await fetch(`${WP_URL}/logs/analyze`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, log_type: args.log_type || 'error' })
    });
    return await response.json();
  },
  'wp-cron-manager': async (args) => {
    const response = await fetch(`${WP_URL}/cron/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, action: args.action || 'list' })
    });
    return await response.json();
  },
  'wp-multisite-manager': async (args) => {
    const response = await fetch(`${WP_URL}/multisite/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, network_action: args.network_action || 'list' })
    });
    return await response.json();
  },
  'wp-ai-content-generator': async (args) => {
    const response = await fetch(`${WP_URL}/ai/content/generate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, prompt: args.prompt, post_type: args.post_type || 'post' })
    });
    return await response.json();
  },
  'wp-ai-image-generator': async (args) => {
    const response = await fetch(`${WP_URL}/ai/image/generate`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, prompt: args.prompt, post_id: args.post_id })
    });
    return await response.json();
  },
  'wp-ai-seo-writer': async (args) => {
    const response = await fetch(`${WP_URL}/ai/seo/write`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, topic: args.topic, keywords: args.keywords || [] })
    });
    return await response.json();
  },
  'wp-product-importer': async (args) => {
    const response = await fetch(`${WP_URL}/ecommerce/import`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, csv_url: args.csv_url })
    });
    return await response.json();
  },
  'wp-inventory-manager': async (args) => {
    const response = await fetch(`${WP_URL}/ecommerce/inventory`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, action: args.action || 'list', product_id: args.product_id })
    });
    return await response.json();
  },
  'wp-price-optimizer': async (args) => {
    const response = await fetch(`${WP_URL}/ecommerce/pricing`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, product_id: args.product_id, strategy: args.strategy || 'manual' })
    });
    return await response.json();
  },
  'wp-health-monitor': async (args) => {
    const response = await fetch(`${WP_URL}/health/monitor`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, check_type: args.check_type || 'full' })
    });
    return await response.json();
  },
  'wp-uptime-monitor': async (args) => {
    const response = await fetch(`${WP_URL}/monitor/uptime`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, interval: args.interval || 5 })
    });
    return await response.json();
  },
  'wp-traffic-analyzer': async (args) => {
    const response = await fetch(`${WP_URL}/analytics/traffic`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, period: args.period || '7d' })
    });
    return await response.json();
  },
  'wp-malware-scanner': async (args) => {
    const response = await fetch(`${WP_URL}/security/malware`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, scan_deep: args.scan_deep || false })
    });
    return await response.json();
  },
  'wp-firewall-config': async (args) => {
    const response = await fetch(`${WP_URL}/security/firewall`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, rules: args.rules || {} })
    });
    return await response.json();
  },
  'wp-login-protector': async (args) => {
    const response = await fetch(`${WP_URL}/security/login-protect`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, max_attempts: args.max_attempts || 5 })
    });
    return await response.json();
  },
  'wp-staging-creator': async (args) => {
    const response = await fetch(`${WP_URL}/staging/create`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, staging_url: args.staging_url })
    });
    return await response.json();
  },
  'wp-deployment-manager': async (args) => {
    const response = await fetch(`${WP_URL}/deployment/manage`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, action: args.action || 'list' })
    });
    return await response.json();
  },
  'wp-clone-site': async (args) => {
    const response = await fetch(`${WP_URL}/site/clone`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ source_url: args.source_url, destination_url: args.destination_url })
    });
    return await response.json();
  },
  'wp-code-linter': async (args) => {
    const response = await fetch(`${WP_URL}/dev/lint`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, file_path: args.file_path })
    });
    return await response.json();
  },
  'wp-git-integration': async (args) => {
    const response = await fetch(`${WP_URL}/dev/git`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, repo_path: args.repo_path, action: args.action || 'status' })
    });
    return await response.json();
  },
  'wp-url-replacer': async (args) => {
    const response = await fetch(`${WP_URL}/migration/url-replace`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, search: args.search, replace: args.replace })
    });
    return await response.json();
  },
  'wp-database-merge': async (args) => {
    const response = await fetch(`${WP_URL}/migration/db-merge`, {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${WP_API_KEY}`, 'Content-Type': 'application/json' },
      body: JSON.stringify({ site_url: args.site_url, source_db: args.source_db })
    });
    return await response.json();
  }
};

class WP AISkillsServer {
  constructor() {
    this.server = new Server(
      { name: 'wp-ai-control-skills', version: '1.1.0' },
      { capabilities: { tools: {} } }
    );
    this.setupHandlers();
  }

  setupHandlers() {
    this.server.setRequestHandler(ListToolsRequestSchema, async () => ({
      tools: Object.values(skills)
    }));

    this.server.setRequestHandler(CallToolRequestSchema, async (request) => {
      const { name, arguments: args } = request.params;
      const handler = skillHandlers[name];
      if (!handler) return { content: [{ type: 'text', text: `Skill ${name} not found` }] };
      try {
        const result = await handler(args);
        return { content: [{ type: 'text', text: JSON.stringify(result, null, 2) }] };
      } catch (error) {
        return { content: [{ type: 'text', text: `Error: ${error.message}` }] };
      }
    });
  }

  async run() {
    const transport = new StdioServerTransport();
    await this.server.connect(transport);
    console.error('WP AI Control Skills Server running on stdio');
  }
}

new WP AISkillsServer().run();