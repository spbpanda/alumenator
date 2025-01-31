import axios from 'axios';

const api = axios.create({
  withCredentials: true,
  baseURL: process.env.NODE_ENV === 'production'
    // ? 'https://alumenator-api.netlify.app/api' // Для production (Netlify)
    ? 'https://api-shop.alumenator.net/'
    : 'http://localhost:5000', // Для локальной разработки
});

// Добавляем интерсептор для включения Origin в каждый запрос
api.interceptors.request.use((config) => {
  config.headers['Origin'] = process.env.NODE_ENV === 'production'
    ? 'https://alumenator.net'
    : 'http://localhost:3000'; // Указываем Origin в зависимости от среды
  return config;
}, (error) => {
  return Promise.reject(error);
});

export default api;