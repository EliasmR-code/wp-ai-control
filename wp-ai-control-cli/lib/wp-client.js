import fetch from 'node-fetch';

export class WPClient {
  constructor(url, apiKey) {
    this.baseUrl = url.replace(/\/$, '');
    this.apiKey = apiKey;
  }

  async get(path, { params = {} } = {}) {
    let url = `${this.baseUrl}/wp-json/wp-ai-control/v1${path}`;
    const query = new URLSearchParams();
    for (const [key, value] of Object.entries(params)) {
      if (value !== undefined && value !== null) {
        query.append(key, String(value));
      }
    }
    const queryString = query.toString();
    if (queryString) url += `?${queryString}`;

    return this.request(url, { method: 'GET' });
  }

  async post(path, { body = null } = {}) {
    const url = `${this.baseUrl}/wp-json/wp-ai-control/v1${path}`;
    return this.request(url, { method: 'POST', body });
  }

  async put(path, body) {
    const url = `${this.baseUrl}/wp-json/wp-ai-control/v1${path}`;
    return this.request(url, { method: 'PUT', body });
  }

  async delete(path) {
    const url = `${this.baseUrl}/wp-json/wp-ai-control/v1${path}`;
    return this.request(url, { method: 'DELETE' });
  }

  async request(url, { method = 'GET', body = null } = {}) {
    const headers = {
      'X-WPAIC-API-Key': this.apiKey,
      'Content-Type': 'application/json',
    };

    const options = { method, headers };
    if (body && (method === 'POST' || method === 'PUT')) {
      options.body = JSON.stringify(body);
    }

    const response = await fetch(url, options);
    if (!response.ok) {
      const errorText = await response.text();
      let errorData;
      try { errorData = JSON.parse(errorText); } catch { errorData = { message: errorText }; }
      throw new Error(`WP API Error (${response.status}): ${errorData.message || 'Unknown error'}`);
    }

    return response.json();
  }
}
