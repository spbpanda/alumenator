import express from 'express';
import cors from 'cors';
import bodyParser from 'body-parser';
import axios from 'axios';

const app = express();
const PORT = 5000;
const ShopKey = '537e232713010526cc1ae04c14ed979d'

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Функция для задержки
const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

// Функция для выполнения запроса с повторными попытками
const fetchWithRetry: any = async (url: string, options: any, retries = 3, backoff = 300) => {
  try {
    const response = await axios.get(url, options);
    return response.data;
  } catch (error: any) {
    if (error.response && error.response.status === 429 && retries > 0) {
      // Если ошибка 429 и остались попытки, повторяем запрос
      await delay(backoff); // Задержка перед повторной попыткой
      return fetchWithRetry(url, options, retries - 1, backoff * 2); // Увеличиваем задержку
    }
    throw error; // Если попытки закончились или ошибка не 429, выбрасываем ошибку
  }
};

// Routes
app.get('/', (req, res) => {
  res.send('Backend is running!');
});

// Mock data for server images
const serverImages = [
  {
    name: "Магическое выживание",
    url: "/images/magic-survival.jpg",
  },
  {
    name: "Бедрок выживания",
    url: "/images/survival.jpg",
  },
];

// API to get list of servers with images
app.get('/servers', async (req, res) => {
  try {
    // Fetch servers from EasyDonate API
    const easydonateResponse = await fetchWithRetry('https://easydonate.ru/api/v3/shop/servers', {
      headers: {
        'Shop-Key': '537e232713010526cc1ae04c14ed979d',
      },
    });

    // Map server names to images
    const serversWithImages = serverImages.map((server: any) => {
      const serverResp = easydonateResponse.response.find((serverResp: any) => serverResp.name.includes(server.name));
      return {
        ...serverResp,
        imageUrl: server ? server.url : '/images/background.jpeg', // Add image URL if found
      };
    });

    res.json(serversWithImages);
  } catch (error: any) {
    console.error('Error fetching servers from EasyDonate:', error.response ? error.response.data : error.message);
    res.status(500).json({
      message: 'Failed to fetch servers from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Goods API
app.get('/goods', async (req, res) => {
  const page = parseInt(req.query.page as string) || 1; // Страница
  const limit = parseInt(req.query.limit as string) || 6; // Лимит товаров на странице
  const type = req.query.type || 'all'; // Тип
  const server = req.query.server || 'Магическое выживание №1 и №2'; // Сервер
  const searchQuery = req.query.search || ''; // Поисковый запрос

  try {
    // Получаем товары из EasyDonate API
    const easydonateResponse = await fetchWithRetry('https://easydonate.ru/api/v3/shop/products', {
      headers: {
        'Shop-Key': ShopKey,
      },
    });

    let allGoods = easydonateResponse.response.map((product: any) => ({
      ...product,
      servers: product.servers.map((server: any) => ({name: server.name, id: server.id})),
    }));

    // Фильтруем товары по категории
    if (type !== 'all') {
      allGoods = allGoods.filter((good: { type: any; }) => good.type === type);
    }

    // Фильтруем товары по серверу
    if (server) {
      allGoods = allGoods.filter((good: { servers: any[] }) =>
        good.servers.some((s: any) => s.name === server)
      );
    }

    // Фильтруем товары по поисковому запросу
    if (searchQuery) {
      allGoods = allGoods.filter((good: { name: string; }) => {
        return good.name.toLowerCase().includes((searchQuery as string).toLowerCase());
      });
    }

    // Применяем пагинацию
    const startIndex = (page - 1) * limit;
    const endIndex = page * limit;
    const results = allGoods.slice(startIndex, endIndex);

    // Возвращаем результат
    res.json({
      goods: results,
      currentPage: page,
      totalPages: Math.ceil(allGoods.length / limit),
    });
  } catch (error: any) {
    console.error('Error fetching products from EasyDonate:', error.response ? error.response.data : error.message);

    // Возвращаем ошибку клиенту
    res.status(500).json({
      message: 'Failed to fetch products from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Get a single good by ID
app.get('/goods/:id', async (req, res) => {
  const goodId = req.params.id;

  try {
    // Получаем товар из EasyDonate API по ID
    const easydonateResponse = await fetchWithRetry(`https://easydonate.ru/api/v3/shop/product/${goodId}`, {
      headers: {
        'Shop-Key': ShopKey,
      },
    });

    const good = {
      ...easydonateResponse.response,
      servers: easydonateResponse.response.servers.map((server: any) => ({name: server.name, id: server.id})),
    };

    res.json(good);
  } catch (error: any) {
    console.error('Error fetching product from EasyDonate:', error.response ? error.response.data : error.message);

    // Возвращаем ошибку клиенту
    res.status(500).json({
      message: 'Failed to fetch product from EasyDonate',
      error: error.response ? error.response.data : error.message,
    });
  }
});

// Маршрут для создания платежа
app.post('/create-payment', async (req, res) => {
  const { customer, server_id, products, email, success_url } = req.body;
  const params = new URLSearchParams({
    customer: customer, // Никнейм покупателя
    server_id: server_id, // ID сервера
    products: JSON.stringify(products), // Товары в формате { "product_id": quantity }
    email: email, // Email покупателя
    success_url: success_url, // URL для перенаправления после успешной оплаты
  });

  console.log(params.toString())

  try {
    const url = `https://easydonate.ru/api/v3/shop/payment/create?${params.toString()}`;

    const response = await axios.get(url, {
      headers: {
        'Shop-Key': ShopKey, // Замените на ваш Shop-Key
      },
    });

    // Если запрос успешен, возвращаем ссылку на оплату
    if (response.data.success) {
      res.json({
        success: true,
        url: response.data.response.url, // Ссылка на оплату
      });
    } else {
      console.log(response)
      res.status(400).json({
        success: false,
        message: response.data.message || 'Ошибка при создании платежа',
      });
    }
  } catch (error: any) {
    console.error('Ошибка при создании платежа:', error.response ? error.response.data : error.message);
    res.status(500).json({
      success: false,
      message: 'Ошибка при создании платежа',
      error: error.response ? error.response.data : error.message,
    });
  }
});

app.get('/server-status', async (req, res) => {
  const address = 'play.alumenator.net:25565';

  try {
    const responce = await fetchWithRetry(`https://api.mcstatus.io/v2/status/java/${address}`);

    res.json(responce);
  } catch (error: any) {
    console.error('Failed to fetch server status', error.response ? error.response.data : error.message);

    // Возвращаем ошибку клиенту
    res.status(500).json({
      message: 'Failed to fetch server status',
      error: error.response ? error.response.data : error.message,
    });
  }
})

// Start server
app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});