const axios = require('axios');

const API_URL = 'https://gipay.xyz/api';

const API_HEADERS = token => ({
  headers: {
    Authorization: `Bearer ${token}`,
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }
});

const users = {
  get: token => axios.get(`${API_URL}/admin/users`, API_HEADERS(token)),
  patch: (url, obj = {}, token) => axios.patch(url, obj, API_HEADERS(token))
};

export default {
  users
};
