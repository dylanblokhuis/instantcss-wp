const defaultOptions = {
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': window.icss_params.nonce
  }
}

function api(url, options) {
  return fetch(window.icss_params.rest_url + url, { ...defaultOptions, ...options }).then(res => res.json());
}

export default api;
