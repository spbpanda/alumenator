const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function (app) {
  app.use(
    '/api', // You can change this to any path you want to proxy
    createProxyMiddleware({
      // target: 'http://localhost:5000', // Backend server URL
      // target: 'https://alumenator-api.netlify.app/.netlify/functions/app', // Backend server URL
      target: 'https://api-shop.alumenator.net/', // Backend server URL
      changeOrigin: true,
    })
  );
};