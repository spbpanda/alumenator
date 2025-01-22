const { createProxyMiddleware } = require('http-proxy-middleware');

module.exports = function (app) {
  app.use(
    '/api', // You can change this to any path you want to proxy
    createProxyMiddleware({
      target: 'http://localhost:5000', // Backend server URL
      changeOrigin: true,
    })
  );
};