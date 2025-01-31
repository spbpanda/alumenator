"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
const express_1 = __importDefault(require("express"));
const cors_1 = __importDefault(require("cors"));
const body_parser_1 = __importDefault(require("body-parser"));
const axios_1 = __importDefault(require("axios"));
const app = (0, express_1.default)();
const PORT = 5000;
const ShopKey = '537e232713010526cc1ae04c14ed979d';
// Middleware
app.use((0, cors_1.default)());
app.use(body_parser_1.default.json());
// Функция для задержки
const delay = (ms) => new Promise(resolve => setTimeout(resolve, ms));
// Функция для выполнения запроса с повторными попытками
const fetchWithRetry = (url_1, options_1, ...args_1) => __awaiter(void 0, [url_1, options_1, ...args_1], void 0, function* (url, options, retries = 3, backoff = 300) {
    try {
        const response = yield axios_1.default.get(url, options);
        return response.data;
    }
    catch (error) {
        if (error.response && error.response.status === 429 && retries > 0) {
            // Если ошибка 429 и остались попытки, повторяем запрос
            yield delay(backoff); // Задержка перед повторной попыткой
            return fetchWithRetry(url, options, retries - 1, backoff * 2); // Увеличиваем задержку
        }
        throw error; // Если попытки закончились или ошибка не 429, выбрасываем ошибку
    }
});
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
app.get('/servers', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    try {
        // Fetch servers from EasyDonate API
        const easydonateResponse = yield fetchWithRetry('https://easydonate.ru/api/v3/shop/servers', {
            headers: {
                'Shop-Key': '537e232713010526cc1ae04c14ed979d',
            },
        });
        // Map server names to images
        const serversWithImages = serverImages.map((server) => {
            const serverResp = easydonateResponse.response.find((serverResp) => serverResp.name.includes(server.name));
            return Object.assign(Object.assign({}, serverResp), { imageUrl: server ? server.url : '/images/background.jpeg' });
        });
        res.json(serversWithImages);
    }
    catch (error) {
        console.error('Error fetching servers from EasyDonate:', error.response ? error.response.data : error.message);
        res.status(500).json({
            message: 'Failed to fetch servers from EasyDonate',
            error: error.response ? error.response.data : error.message,
        });
    }
}));
// Goods API
app.get('/goods', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const page = parseInt(req.query.page) || 1; // Страница
    const limit = parseInt(req.query.limit) || 6; // Лимит товаров на странице
    const type = req.query.type || 'all'; // Тип
    const server = req.query.server || 'Магическое выживание'; // Сервер
    const searchQuery = req.query.search || ''; // Поисковый запрос
    try {
        // Получаем товары из EasyDonate API
        const easydonateResponse = yield fetchWithRetry('https://easydonate.ru/api/v3/shop/products', {
            headers: {
                'Shop-Key': ShopKey,
            },
        });
        let allGoods = easydonateResponse.response.map((product) => (Object.assign(Object.assign({}, product), { servers: product.servers.map((server) => ({ name: server.name, id: server.id })) })));
        // Фильтруем товары по категории
        if (type !== 'all') {
            allGoods = allGoods.filter((good) => good.type === type);
        }
        // Фильтруем товары по серверу
        if (server) {
            allGoods = allGoods.filter((good) => good.servers.some((s) => s.name === server));
        }
        // Фильтруем товары по поисковому запросу
        if (searchQuery) {
            allGoods = allGoods.filter((good) => {
                return good.name.toLowerCase().includes(searchQuery.toLowerCase());
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
    }
    catch (error) {
        console.error('Error fetching products from EasyDonate:', error.response ? error.response.data : error.message);
        // Возвращаем ошибку клиенту
        res.status(500).json({
            message: 'Failed to fetch products from EasyDonate',
            error: error.response ? error.response.data : error.message,
        });
    }
}));
// Get a single good by ID
app.get('/goods/:id', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const goodId = req.params.id;
    try {
        // Получаем товар из EasyDonate API по ID
        const easydonateResponse = yield fetchWithRetry(`https://easydonate.ru/api/v3/shop/product/${goodId}`, {
            headers: {
                'Shop-Key': ShopKey,
            },
        });
        const good = Object.assign(Object.assign({}, easydonateResponse.response), { servers: easydonateResponse.response.servers.map((server) => ({ name: server.name, id: server.id })) });
        res.json(good);
    }
    catch (error) {
        console.error('Error fetching product from EasyDonate:', error.response ? error.response.data : error.message);
        // Возвращаем ошибку клиенту
        res.status(500).json({
            message: 'Failed to fetch product from EasyDonate',
            error: error.response ? error.response.data : error.message,
        });
    }
}));
// Маршрут для создания платежа
app.post('/create-payment', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const { customer, server_id, products, email, success_url } = req.body;
    const params = new URLSearchParams({
        customer: customer, // Никнейм покупателя
        server_id: server_id, // ID сервера
        products: JSON.stringify(products), // Товары в формате { "product_id": quantity }
        email: email, // Email покупателя
        success_url: success_url, // URL для перенаправления после успешной оплаты
    });
    console.log(params.toString());
    try {
        const url = `https://easydonate.ru/api/v3/shop/payment/create?${params.toString()}`;
        console.log(url);
        const response = yield axios_1.default.get(url, {
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
        }
        else {
            console.log(response);
            res.status(400).json({
                success: false,
                message: response.data.message || 'Ошибка при создании платежа',
            });
        }
    }
    catch (error) {
        console.error('Ошибка при создании платежа:', error.response ? error.response.data : error.message);
        res.status(500).json({
            success: false,
            message: 'Ошибка при создании платежа',
            error: error.response ? error.response.data : error.message,
        });
    }
}));
app.get('/server-status', (req, res) => __awaiter(void 0, void 0, void 0, function* () {
    const address = 'play.alumenator.net:25565';
    try {
        const responce = yield fetchWithRetry(`https://api.mcstatus.io/v2/status/java/${address}`);
        res.json(responce);
    }
    catch (error) {
        console.error('Failed to fetch server status', error.response ? error.response.data : error.message);
        // Возвращаем ошибку клиенту
        res.status(500).json({
            message: 'Failed to fetch server status',
            error: error.response ? error.response.data : error.message,
        });
    }
}));
// Start server
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
