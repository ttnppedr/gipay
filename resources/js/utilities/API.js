const axios = require('axios');

const API_URL = 'https://gipay.xyz/api/admin';

const API_HEADERS = token => ({
  headers: {
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
});

const users = {
  get: token => axios.get(`${API_URL}/users`, API_HEADERS(token)),
  patch: (url, obj = {}, token) => axios.patch(url, obj, API_HEADERS(token))
};

export default {
  users
};
