import context from './context.js';
import pages from './pages.js';
import posts from './posts.js';
import builder from './builder.js';
import media from './media.js';
import analysis from './analysis.js';
import plugins from './plugins.js';
import menus from './menus.js';
import taxonomies from './taxonomies.js';
import snapshots from './snapshots.js';
import usage from './usage.js';
import users from './users.js';
import comments from './comments.js';
import mediaAdditional from './media-additional.js';
import terms from './terms-crud.js';
import settings from './settings.js';
import themes from './themes.js';
import meta from './meta.js';
import search from './search.js';
import widgets from './widgets.js';
import bulk from './bulk.js';
import woocommerce from './woocommerce.js';
import acf from './acf.js';

const allTools = [
  ...context,
  ...pages,
  ...posts,
  ...builder,
  ...media,
  ...analysis,
  ...plugins,
  ...menus,
  ...taxonomies,
  ...snapshots,
  ...usage,
  ...users,
  ...comments,
  ...mediaAdditional,
  ...terms,
  ...settings,
  ...themes,
  ...meta,
  ...search,
  ...widgets,
  ...bulk,
  ...woocommerce,
  ...acf,
];

const sharedProps = {
  site_url: { type: 'string', description: 'WordPress site URL (e.g., https://example.com)' },
  api_key: { type: 'string', description: 'API key from WP AI Control plugin settings' }
};

export const tools = allTools.map(tool => {
  const newTool = { ...tool };
  const props = newTool.inputSchema.properties;
  newTool.inputSchema = {
    type: 'object',
    properties: { ...sharedProps, ...props },
    required: ['site_url', 'api_key', ...(newTool.inputSchema.required || [])]
  };
  return newTool;
});