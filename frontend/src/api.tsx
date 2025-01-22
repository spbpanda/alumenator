import axios from 'axios';

const api = axios.create({
  baseURL: process.env.NODE_ENV === 'production'
    ? 'https://alumenator-api.netlify.app/api' // Для production (Netlify)
    : 'http://localhost:5000', // Для локальной разработки
});

export default api;