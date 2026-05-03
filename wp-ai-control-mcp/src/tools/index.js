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

export const tools = [
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
