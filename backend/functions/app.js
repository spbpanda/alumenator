const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const axios = require('axios');
const serverless = require('serverless-http');

const app = express();
const router = express.Router();
const PORT = 5000;
const ShopKey = '537e232713010526cc1ae04c14ed979d';

// Middleware
app.use(cors({
    origin: 'https://alumenator.netlify.app',
  }));
app.use(bodyParser.json());

// Функция для задержки
const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Функция для выполнения запроса с повторными попытками
const fetchWithRetry = async (url, options, retries = 3, backoff = 300) => {
  try {
    const response = await axios.get(url, options);
    return response.data;
  } catch (error) {
    if (error.response && error.response.status === 429 && retries > 0) {
      await delay(backoff);
      return fetchWithRetry(url, options, retries - 1, backoff * 2);
    }
    throw error;
  }
};

// Mock data for server images
const serverImages = [
  {
    name: "Магическое выживание №1 и №2",
    url: "/images/magic-survival.jpg",
  },
  {
    name: "Бедрок выживания",
    url: "/images/survival.jpg",
  },
];

// API to get list of servers with images
router.get('/servers', async (req, res) => {
  try {
    const easydonateResponse = await fetchWithRetry('https://easydonate.ru/api/v3/shop/servers', {
      headers: {
        'Shop-Key': ShopKey,
      },
    });

    const serversWithImages = serverImages.map((server) => {
      const serverResp = easydonateResponse.response.find((serverResp) => serverResp.name === server.name);
      return {
        ...serverResp,
        imageUrl: serverResp ? server.url : '/images/background.jpeg',
      };
    });

    res.json(serversWithImages);
  } catch (error) {
    console.error('Error fetching servers from EasyDonate:', error.response ? error.response.data : error.message);
    res.status(500).json({
      message: 'Failed to fetch servers from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Goods API
router.get('/goods', async (req, res) => {
  const page = parseInt(req.query.page) || 1;
  const limit = parseInt(req.query.limit) || 6;
  const type = req.query.type || 'all';
  const server = req.query.server || 'Магическое выживание №1 и №2';
  const searchQuery = req.query.search || '';

  try {
    const easydonateResponse = await fetchWithRetry('https://easydonate.ru/api/v3/shop/products', {
      headers: {
        'Shop-Key': ShopKey,
      },
    });

    let allGoods = easydonateResponse.response.map((product) => ({
      ...product,
      servers: product.servers.map((server) => ({ name: server.name, id: server.id })),
    }));

    if (type !== 'all') {
      allGoods = allGoods.filter((good) => good.type === type);
    }

    if (server) {
      allGoods = allGoods.filter((good) =>
        good.servers.some((s) => s.name === server)
      );
    }

    if (searchQuery) {
      allGoods = allGoods.filter((good) =>
        good.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        good.description.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    const startIndex = (page - 1) * limit;
    const endIndex = page * limit;
    const results = allGoods.slice(startIndex, endIndex);

    res.json({
      goods: results,
      currentPage: page,
      totalPages: Math.ceil(allGoods.length / limit),
    });
  } catch (error) {
    console.error('Error fetching products from EasyDonate:', error.response ? error.response.data : error.message);
    res.status(500).json({
      message: 'Failed to fetch products from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Get a single good by ID
router.get('/goods/:id', async (req, res) => {
  const goodId = req.params.id;

  try {
    const easydonateResponse = await fetchWithRetry(`https://easydonate.ru/api/v3/shop/product/${goodId}`, {
      headers: {
        'Shop-Key': ShopKey,
      },
    });

    const good = {
      ...easydonateResponse.response,
      servers: easydonateResponse.response.servers.map((server) => ({ name: server.name, id: server.id })),
    };

    res.json(good);
  } catch (error) {
    console.error('Error fetching product from EasyDonate:', error.response ? error.response.data : error.message);
    res.status(500).json({
      message: 'Failed to fetch product from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Маршрут для создания платежа
router.post('/create-payment', async (req, res) => {
  const { customer, server_id, products, email, success_url } = req.body;
  const params = new URLSearchParams({
    customer: customer, // Никнейм покупателя
    server_id: server_id, // ID сервера
    products: JSON.stringify(products), // Товары в формате { "product_id": quantity }
    email: email, // Email покупателя
    success_url: success_url, // URL для перенаправления после успешной оплаты
  });

  try {
    const url = `https://easydonate.ru/api/v3/shop/payment/create?${params.toString()}`;

    const response = await axios.get(url, {
      headers: {
        'Shop-Key': ShopKey, // Замените на ваш Shop-Key
      },
    });

    if (response.data.success) {
      res.json({
        success: true,
        url: response.data.response.url,
      });
    } else {
      console.log(response);
      res.status(400).json({
        success: false,
        message: response.data.message || 'Ошибка при создании платежа',
      });
    }
  } catch (error) {
    console.error('Ошибка при создании платежа:', error.response ? error.response.data : error.message);
    res.status(500).json({
      success: false,
      message: 'Ошибка при создании платежа',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Подключите router к app
app.use('/api', router);

// Локальный сервер (для разработки)
if (process.env.NODE_ENV !== 'production') {
  app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
  });
}

// Экспорт для serverless
module.exports.handler = serverless(app);