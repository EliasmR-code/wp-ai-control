import fetch from 'node-fetch';

export async function wpFetch(path, { method = 'GET', body = null, params = {}, site_url = null, api_key = null } = {}) {
  let url = site_url || process.env.WP_URL;
  let apiKey = api_key || process.env.WP_API_KEY;
  
  if (!url || !apiKey) {
    throw new Error('site_url and api_key are required. Pass them as arguments or set WP_URL and WP_API_KEY environment variables.');
  }
  
  url = `${url.replace(/\/$/, '')}/wp-json/wp-ai-control/v1${path}`;

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
      `WP API Error (${response.status}): ${errorData.message || errorData.code || 'Unknown error'}`
    );
  }

  return response.json();
}