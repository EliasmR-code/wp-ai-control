#!/usr/bin/env node
import 'dotenv/config';
import { readFileSync, existsSync, writeFileSync } from 'fs';
import { join } from 'path';
import { stdin as input, stdout as output } from 'process';
import readline from 'readline/promises';
import { Command } from 'commander';
import { WPClient } from '../lib/wp-client.js';

function loadDotWpai() {
  const p = join(process.cwd(), '.wpai');
  if (!existsSync(p)) {
    return;
  }
  for (const line of readFileSync(p, 'utf8').split(/\r?\n/)) {
    const t = line.trim();
    if (!t || t.startsWith('#')) {
      continue;
    }
    const i = t.indexOf('=');
    if (i === -1) {
      continue;
    }
    const k = t.slice(0, i).trim();
    const v = t.slice(i + 1).trim();
    if (k) {
      process.env[k] = v;
    }
  }
}

loadDotWpai();

function getClient() {
  const url = process.env.WP_URL;
  const key = process.env.WP_API_KEY;
  if (!url || !key) {
    console.error('Falta WP_URL o WP_API_KEY. Ejecuta `wpai setup` o define las variables de entorno.');
    process.exit(1);
  }
  return new WPClient(url, key);
}

function out(data) {
  console.log(JSON.stringify(data, null, 2));
}

const program = new Command();
program.name('wpai').description('CLI para WP AI Control (REST del plugin)').version('1.0.0');

program
  .command('setup')
  .description('Crea .wpai en el directorio actual con WP_URL y WP_API_KEY')
  .action(async () => {
    const rl = readline.createInterface({ input, output });
    const url = (await rl.question('URL del sitio (ej. https://ejemplo.com): ')).trim();
    const key = (await rl.question('API Key (Bearer, desde Ajustes → WP AI Control): ')).trim();
    await rl.close();
    if (!url || !key) {
      console.error('URL y API Key son obligatorios.');
      process.exit(1);
    }
    const content = `WP_URL=${url}\nWP_API_KEY=${key}\n`;
    writeFileSync(join(process.cwd(), '.wpai'), content, 'utf8');
    console.error('Guardado en .wpai');
  });

program
  .command('site-info')
  .description('Información pública del sitio')
  .action(async () => {
    out(await getClient().get('/site-info'));
  });

program
  .command('builder-info')
  .description('Detectar page builders')
  .action(async () => {
    out(await getClient().get('/builder-info'));
  });

program
  .command('posts')
  .description('Listar entradas')
  .option('-s, --search <q>', 'Buscar')
  .option('--status <status>', 'Estado', 'publish')
  .option('--page <n>', 'Página', '1')
  .option('--per-page <n>', 'Por página', '10')
  .action(async (opts) => {
    const params = { status: opts.status, page: opts.page, per_page: opts.perPage };
    if (opts.search) {
      params.search = opts.search;
    }
    out(await getClient().get('/posts', { params }));
  });

program
  .command('post-read <id>')
  .description('Leer una entrada')
  .action(async (id) => {
    out(await getClient().get(`/posts/${id}`));
  });

program
  .command('post-update <id>')
  .description('Actualizar entrada')
  .option('-t, --title <title>', 'Título')
  .option('-c, --content <content>', 'Contenido')
  .option('--status <status>', 'Estado')
  .action(async (id, opts) => {
    const body = {};
    if (opts.title) {
      body.title = opts.title;
    }
    if (opts.content) {
      body.content = opts.content;
    }
    if (opts.status) {
      body.status = opts.status;
    }
    out(await getClient().put(`/posts/${id}`, body));
  });

program
  .command('post-delete <id>')
  .description('Eliminar entrada')
  .action(async (id) => {
    out(await getClient().delete(`/posts/${id}`));
  });

program
  .command('post-create')
  .description('Crear entrada')
  .requiredOption('-t, --title <title>', 'Título')
  .option('-c, --content <content>', 'Contenido', '')
  .option('--status <status>', 'Estado', 'draft')
  .option('-e, --excerpt <excerpt>', 'Resumen', '')
  .action(async (opts) => {
    const body = {
      title: opts.title,
      content: opts.content || '',
      status: opts.status,
    };
    if (opts.excerpt) {
      body.excerpt = opts.excerpt;
    }
    out(await getClient().post('/posts', { body }));
  });

program
  .command('pages')
  .description('Listar páginas')
  .option('--status <status>', 'Estado', 'publish')
  .option('--per-page <n>', 'Máximo', '50')
  .action(async (opts) => {
    out(await getClient().get('/pages', { params: { status: opts.status, per_page: opts.perPage } }));
  });

program
  .command('page-create')
  .description('Crear página')
  .requiredOption('-t, --title <title>', 'Título')
  .option('-c, --content <content>', 'Contenido', '')
  .option('--status <status>', 'Estado', 'draft')
  .action(async (opts) => {
    out(
      await getClient().post('/pages', {
        body: { title: opts.title, content: opts.content || '', status: opts.status },
      })
    );
  });

program
  .command('page-read <id>')
  .description('Leer una página')
  .action(async (id) => {
    out(await getClient().get(`/pages/${id}`));
  });

program
  .command('page-update <id>')
  .description('Actualizar página')
  .option('-t, --title <title>', 'Título')
  .option('-c, --content <content>', 'Contenido')
  .option('--status <status>', 'Estado')
  .action(async (id, opts) => {
    const body = {};
    if (opts.title) {
      body.title = opts.title;
    }
    if (opts.content) {
      body.content = opts.content;
    }
    if (opts.status) {
      body.status = opts.status;
    }
    out(await getClient().put(`/pages/${id}`, body));
  });

program
  .command('page-delete <id>')
  .description('Eliminar página')
  .action(async (id) => {
    out(await getClient().delete(`/pages/${id}`));
  });

program
  .command('page-duplicate <id>')
  .description('Duplicar página')
  .action(async (id) => {
    out(await getClient().post(`/pages/${id}/duplicate`, { body: {} }));
  });

program
  .command('users')
  .description('Listar usuarios')
  .action(async () => {
    out(await getClient().get('/users'));
  });

program
  .command('user-create <username> <email>')
  .description('Crear usuario')
  .option('-p, --password <password>', 'Contraseña (si no se indica, WordPress genera una)')
  .option('-r, --role <role>', 'Rol', 'subscriber')
  .action(async (username, email, opts) => {
    const body = { username, email, role: opts.role };
    if (opts.password) {
      body.password = opts.password;
    }
    out(await getClient().post('/users', { body }));
  });

program
  .command('media')
  .description('Listar medios')
  .action(async () => {
    out(await getClient().get('/media'));
  });

program
  .command('media-upload')
  .description('Importar archivo a la biblioteca desde una URL http(s)')
  .requiredOption('--url <url>', 'URL del archivo')
  .option('--filename <name>', 'Nombre de archivo (opcional)')
  .option('--alt <text>', 'Texto alternativo para imágenes')
  .action(async (opts) => {
    const body = { url: opts.url };
    if (opts.filename) {
      body.filename = opts.filename;
    }
    if (opts.alt) {
      body.alt_text = opts.alt;
    }
    out(await getClient().post('/media/upload', { body }));
  });

program
  .command('comments')
  .description('Listar comentarios')
  .action(async () => {
    out(await getClient().get('/comments'));
  });

program
  .command('comment-approve <id>')
  .description('Aprobar comentario')
  .action(async (id) => {
    out(await getClient().post(`/comments/${id}/approve`, { body: {} }));
  });

program
  .command('plugins')
  .description('Plugins instalados')
  .action(async () => {
    out(await getClient().get('/plugins'));
  });

program
  .command('plugin-install <slug>')
  .description('Instalar plugin desde el directorio de WordPress.org')
  .action(async (slug) => {
    out(await getClient().post('/plugins/install', { body: { slug } }));
  });

program
  .command('plugin-activate <slug>')
  .description('Activar plugin por slug')
  .action(async (slug) => {
    out(await getClient().post(`/plugins/${slug}/activate`, { body: {} }));
  });

program
  .command('plugin-deactivate <slug>')
  .description('Desactivar plugin por slug')
  .action(async (slug) => {
    out(await getClient().post(`/plugins/${slug}/deactivate`, { body: {} }));
  });

program
  .command('bulk-update-posts')
  .description('Actualizar varias entradas (mismos campos para todas)')
  .requiredOption('-i, --ids <ids>', 'IDs separados por coma, ej. 1,2,3')
  .option('-t, --title <title>', 'Nuevo título')
  .option('-c, --content <content>', 'Nuevo contenido')
  .option('--status <status>', 'Nuevo estado')
  .action(async (opts) => {
    const post_ids = opts.ids.split(',').map((s) => s.trim()).filter(Boolean);
    const updates = {};
    if (opts.title) {
      updates.title = opts.title;
    }
    if (opts.content) {
      updates.content = opts.content;
    }
    if (opts.status) {
      updates.status = opts.status;
    }
    out(await getClient().post('/bulk-update-posts', { body: { post_ids, updates } }));
  });

program
  .command('bulk-delete-posts')
  .description('Eliminar varias entradas')
  .requiredOption('-i, --ids <ids>', 'IDs separados por coma')
  .action(async (opts) => {
    const post_ids = opts.ids.split(',').map((s) => s.trim()).filter(Boolean);
    out(await getClient().post('/bulk-delete-posts', { body: { post_ids } }));
  });

program
  .command('themes')
  .description('Temas instalados')
  .action(async () => {
    out(await getClient().get('/themes'));
  });

program
  .command('theme-activate <slug>')
  .description('Activar tema')
  .action(async (slug) => {
    out(await getClient().post(`/themes/${slug}/activate`, { body: {} }));
  });

program
  .command('menus')
  .description('Listar menús')
  .action(async () => {
    out(await getClient().get('/menus'));
  });

program
  .command('menu-read <id>')
  .description('Menú con ítems')
  .action(async (id) => {
    out(await getClient().get(`/menus/${id}`));
  });

program
  .command('menu-locations')
  .description('Ubicaciones de menú del tema')
  .action(async () => {
    out(await getClient().get('/menus/locations'));
  });

program
  .command('products')
  .description('Listar productos WooCommerce')
  .action(async () => {
    out(await getClient().get('/wc/products'));
  });

program
  .command('product-create <name>')
  .description('Crear producto WooCommerce')
  .option('--regular-price <price>', 'Precio')
  .action(async (name, opts) => {
    const body = { name };
    if (opts.regularPrice) {
      body.regular_price = opts.regularPrice;
    }
    out(await getClient().post('/wc/products', { body }));
  });

program
  .command('sidebars')
  .description('Listar sidebars (widgets)')
  .action(async () => {
    out(await getClient().get('/sidebars'));
  });

program
  .command('widgets')
  .description('Listar widgets')
  .action(async () => {
    out(await getClient().get('/widgets'));
  });

program
  .command('acf-field-groups')
  .description('Listar grupos de campos ACF')
  .action(async () => {
    out(await getClient().get('/acf/field-groups'));
  });

program
  .command('acf-fields <groupId>')
  .description('Campos de un grupo ACF')
  .action(async (groupId) => {
    out(await getClient().get(`/acf/field-groups/${groupId}/fields`));
  });

program
  .command('analyze-seo <pageId>')
  .description('Análisis SEO de una página')
  .action(async (pageId) => {
    out(await getClient().get(`/analyze/seo/${pageId}`));
  });

program
  .command('analyze-performance <pageId>')
  .description('Análisis de rendimiento')
  .action(async (pageId) => {
    out(await getClient().get(`/analyze/performance/${pageId}`));
  });

program
  .command('run <path>')
  .description('Petición REST bajo /wp-json/wp-ai-control/v1 (ej. /wc/orders)')
  .option('-m, --method <method>', 'Método HTTP', 'GET')
  .option('-b, --body <json>', 'Cuerpo JSON (POST/PUT)')
  .action(async (pathArg, opts) => {
    const method = (opts.method || 'GET').toUpperCase();
    let body = null;
    if (opts.body) {
      try {
        body = JSON.parse(opts.body);
      } catch (e) {
        console.error('JSON inválido en --body:', e.message);
        process.exit(1);
      }
    }
    const c = getClient();
    if (method === 'GET') {
      out(await c.get(pathArg));
    } else if (method === 'POST') {
      out(await c.post(pathArg, { body }));
    } else if (method === 'PUT') {
      out(await c.put(pathArg, body));
    } else if (method === 'DELETE') {
      out(await c.delete(pathArg));
    } else {
      console.error('Método no soportado. Usa GET, POST, PUT o DELETE.');
      process.exit(1);
    }
  });

program.parse();
