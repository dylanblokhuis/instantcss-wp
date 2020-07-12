function api(url) {
  return fetch(window.icss_params.rest_url + url, {
    headers: {
      'Content-Type': 'application/json',
      'X-WP-Nonce': window.icss_params.nonce
    }
  }).then(res => res.json());
}

export default api;
