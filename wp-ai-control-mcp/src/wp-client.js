import fetch from 'node-fetch';

const WP_URL = process.env.WP_URL;
const WP_API_KEY = process.env.WP_API_KEY;

if (!WP_URL || !WP_API_KEY) {
  throw new Error('WP_URL and WP_API_KEY must be set in .env');
}

export async function wpFetch(path, { method = 'GET', body = null, params = {} } = {}) {
  let url = `${WP_URL}/wp-json/wp-ai-control/v1${path}`;

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
    'X-WPAIC-API-Key': WP_API_KEY,
    'Content-Type': 'application/json',
  };

  const options = {
    method,
    headers,
  };

  if (body && (method === 'POST' || method === 'PUT')) {
    options.body = JSON.stringify(body);
  }

  const response = await fetch(url, options);

  if (!response.ok) {
    const errorText = await response.text();
    let errorData;
    try {
      errorData = JSON.parse(errorText);
    } catch {
      errorData = { message: errorText };
    }
    throw new Error(
      `WP API Error (${response.status}): ${errorData.message || errorData.code || 'Unknown error'}\n${errorText}`
    );
  }

  return response.json();
}
