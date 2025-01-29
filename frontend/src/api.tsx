import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NODE_ENV === 'production'
    // ? 'https://alumenator-api.netlify.app/api' // Для production (Netlify)
    ? 'http://45.145.43.153:5000'
    : 'http://localhost:5000', // Для локальной разработки
});

export default api;