# WP AI Control Skills Library

43 AI-powered workflows for WordPress management via MCP protocol.

## Installation

```bash
cd wp-ai-control-skills
npm install
```

## Configuration

Set environment variables:

```bash
export WP_URL=http://your-wordpress.com/wp-json/wp-ai-control/v1
export WP_API_KEY=your-api-key
```

## Running

```bash
npm start
```

## Skills (43)

### AI Content
| Skill | Description |
|-------|-------------|
| wp-ai-content-generator | Generate posts/pages from AI prompts |
| wp-ai-image-generator | Generate AI images for featured images |
| wp-ai-seo-writer | AI-powered SEO content writing |

### Content & SEO
| Skill | Description |
|-------|-------------|
| wp-content-analyzer | Analyze posts, pages, media, categories |
| wp-seo-audit | Complete SEO audit - meta, sitemaps, schema |
| wp-accessibility-check | WCAG compliance, ARIA, contrast |
| wp-schema-generator | Generate schema.org markup |
| wp-content-translator | Translate content to any language |

### Media & Performance
| Skill | Description |
|-------|-------------|
| wp-image-optimizer | Compress, resize, generate WebP |
| wp-performance-test | Speed, Core Web Vitals, TTFB |
| wp-cache-manager | Object, page, transients, CDN |

### Database & Maintenance
| Skill | Description |
|-------|-------------|
| wp-db-cleaner | Remove revisions, trash, optimize |
| wp-backup-create | Full backup - files and database |
| wp-migration-export | Export XML, WXR, SQL |
| wp-log-analyzer | Error, debug, audit logs |
| wp-url-replacer | Bulk search/replace URLs |
| wp-database-merge | Merge WordPress databases |

### Security
| Skill | Description |
|-------|-------------|
| wp-security-scan | Permissions, vulnerabilities |
| wp-malware-scanner | Detect malware, backdoors |
| wp-firewall-config | Configure security rules |
| wp-login-protector | Brute force protection |
| wp-plugin-audit | Conflicts, outdated, security |
| wp-theme-analyzer | Files, functions, hooks |

### WooCommerce
| Skill | Description |
|-------|-------------|
| wp-ecommerce-audit | Products, orders, settings |
| wp-product-importer | Bulk import from CSV |
| wp-inventory-manager | Stock levels, tracking |
| wp-price-optimizer | Dynamic pricing rules |

### Health & Monitoring
| Skill | Description |
|-------|-------------|
| wp-health-monitor | Real-time health checks |
| wp-uptime-monitor | Uptime and response time |
| wp-traffic-analyzer | Traffic patterns analysis |

### Staging & Deployment
| Skill | Description |
|-------|-------------|
| wp-staging-creator | Create staging environment |
| wp-deployment-manager | Deploy to production |
| wp-clone-site | Clone entire site |

### Development
| Skill | Description |
|-------|-------------|
| wp-code-linter | PHP/JS code quality |
| wp-git-integration | Version control |
| wp-form-generator | Contact, checkout forms |
| wp-menu-builder | Create, organize menus |
| wp-template-creator | Custom templates |
| wp-shortcode-generator | Custom shortcodes |
| wp-hook-explorer | Actions, filters |
| wp-taxonomy-manager | Manage taxonomies |
| wp-user-manager | Roles, capabilities |
| wp-api-tester | Test REST API |
| wp-cron-manager | Schedule, monitor |
| wp-multisite-manager | Network, sites |

## Usage Example

```javascript
{
  name: "wp-content-analyzer",
  arguments: { site_url: "https://example.com" }
}
```

## Dependencies

- @modelcontextprotocol/sdk
- axios

## License

MIT