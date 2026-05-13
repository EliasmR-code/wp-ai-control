import fetch from 'node-fetch';

const DEFAULT_TIMEOUT_MS = 60000;

export async function wpFetch(path, { method = 'GET', body = null, params = {}, site_url = null, api_key = null } = {}) {
  let url = site_url || process.env.WP_URL;
  let apiKey = api_key || process.env.WP_API_KEY;
  
  if (!url || !apiKey) {
    throw new Error('site_url and api_key are required. Pass them as arguments or set WP_URL and WP_API_KEY environment variables.');
  }

  const timeoutMs = Math.min(
    300000,
    Math.max(5000, parseInt(process.env.WP_FETCH_TIMEOUT_MS || String(DEFAULT_TIMEOUT_MS), 10) || DEFAULT_TIMEOUT_MS)
  );

  const trimmed = url.replace(/\/$/, '');
  const apiBase = '/wp-json/wp-ai-control/v1';
  const relPath = path.startsWith('/') ? path : `/${path}`;
  if (trimmed.endsWith(apiBase)) {
    url = `${trimmed}${relPath}`;
  } else {
    url = `${trimmed}${apiBase}${relPath}`;
  }

  const query = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    if (value !== undefined && value !== null) {
      query.append(key, String(value));
    }
  }
  const queryString = query.toString();
  if (queryString) {
    url += `?${queryString}`;
  }

  const headers = {
    'Authorization': `Bearer ${apiKey}`,
    'Content-Type': 'application/json',
    Accept: 'application/json',
    'User-Agent': process.env.WP_FETCH_USER_AGENT || 'wp-ai-control-mcp/1.1 (+https://github.com/EliasmR-code/wp-ai-control)',
  };

  const options = {
    method,
    headers,
    signal: AbortSignal.timeout(timeoutMs),
  };

  if (body && (method === 'POST' || method === 'PUT')) {
    options.body = JSON.stringify(body);
  }

  let response;
  try {
    response = await fetch(url, options);
  } catch (e) {
    const msg = e && e.name === 'TimeoutError' ? `Request timed out after ${timeoutMs}ms` : (e && e.message) || String(e);
    throw new Error(`WP fetch failed: ${msg}`);
  }
  const text = await response.text();

  if (!response.ok) {
    let errorData;
    try {
      errorData = JSON.parse(text);
    } catch {
      errorData = { message: text };
    }
    throw new Error(
      `WP API Error (${response.status}): ${errorData.error || errorData.message || errorData.code || 'Unknown error'}`
    );
  }

  if (!text) {
    return {};
  }
  try {
    return JSON.parse(text);
  } catch {
    return { _raw: text };
  }
}