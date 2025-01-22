import React, { useState } from 'react';
import {
  Box,
  Typography,
  Button,
  List,
  ListItem,
  IconButton,
  TextField,
  Checkbox,
  FormControlLabel,
  Divider,
  Link,
  Snackbar,
  Alert,
} from '@mui/material';
import { cartService } from '../services/cartService';
import { Good } from '../types/Good';
import RemoveFromCartButton from './ProjectsGallery/RemoveFromCartButton';
import PageHeader from './PageHeader';
import axios from 'axios';

const Cart: React.FC = () => {
  const [cartItems, setCartItems] = useState<Good[]>(cartService.getCartItems());
  const [nickname, setNickname] = useState<string>('');
  const [email, setEmail] = useState<string>('');
  const [agree, setAgree] = useState<boolean>(false);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [snackbarOpen, setSnackbarOpen] = useState<boolean>(false); // Состояние для Snackbar
  const [snackbarMessage, setSnackbarMessage] = useState<string>(''); // Сообщение для Snackbar
  const [snackbarSeverity, setSnackbarSeverity] = useState<'error' | 'success' | 'info' | 'warning'>('error'); // Тип уведомления

  // Удалить товар из корзины
  const handleRemoveItem = (itemId: number) => {
    cartService.removeFromCart(itemId);
    setCartItems(cartService.getCartItems());
  };

  // Общая стоимость
  const totalPrice = cartItems.reduce((sum, item) => sum + item.price, 0);

  // Оформление заказа
  const handleCheckout = async () => {
    if (!nickname || !email || !agree || cartItems.length === 0) {
      showSnackbar('Пожалуйста, заполните все поля и согласитесь с условиями.', 'error');
      return;
    }
  
    setIsLoading(true);
    setError(null);
  
    try {
      // Формируем объект products в формате { "product_id": quantity }
      const products = cartItems.reduce((acc, item) => {
        acc[item.id] = 1; // Количество товара (по умолчанию 1)
        return acc;
      }, {} as Record<number, number>);
  
      // Отправляем запрос на сервер для создания платежа
      const response = await axios.post('/api/create-payment', {
        customer: nickname, // Никнейм покупателя
        server_id: JSON.parse(localStorage.getItem('server')!).id, // Идентификатор сервера (замените на ваш server_id)
        products, // JSON-объект с товарами
        email, // Email покупателя
        success_url: 'https://alumenator.net/thanks', // URL для перенаправления после оплаты
      });
  
      if (response.data.success) {
        // Перенаправляем пользователя на страницу оплаты
        window.location.href = response.data.url;
      } else {
        showSnackbar(response.data.message || 'Ошибка при создании платежа.', 'error');
      }
    } catch (error) {
      console.error('Ошибка при оформлении заказа:', error);
      showSnackbar('Ошибка при оформлении заказа. Пожалуйста, попробуйте позже.', 'error');
    } finally {
      setIsLoading(false);
    }
  };

  // Функция для показа уведомления
  const showSnackbar = (message: string, severity: 'error' | 'success' | 'info' | 'warning') => {
    setSnackbarMessage(message);
    setSnackbarSeverity(severity);
    setSnackbarOpen(true);
  };

  // Функция для закрытия уведомления
  const handleCloseSnackbar = () => {
    setSnackbarOpen(false);
  };

  return (
    <Box sx={{ padding: 3, maxWidth: 800, margin: '0 auto' }}>
      <PageHeader title="МОЙ ЗАКАЗ" />

      {/* Список товаров */}
      <List
        sx={{
          display: 'flex',
          flexDirection: 'column',
          gap: 2,
          background: '#00000087',
          padding: '1rem',
          borderRadius: '20px',
          border: '2px solid #f5b759',
        }}
      >
        {cartItems.map((item) => (
          <ListItem
            key={item.id}
            secondaryAction={
              <RemoveFromCartButton onClick={() => handleRemoveItem(item.id)} />
            }
            sx={{ display: 'flex', alignItems: 'center', maxHeight: '100px' }}
          >
            {/* Изображение товара с динамическими размерами */}
            <Box
              component="img"
              src={item.image}
              alt={item.name}
              sx={{
                width: { xs: '60px', sm: '80px', md: '100px' }, // Динамические размеры
                height: { xs: '60px', sm: '80px', md: '100px' }, // Динамические размеры
                borderRadius: 2,
                marginRight: 2, // Отступ справа
                objectFit: 'cover', // Сохраняет пропорции изображения
              }}
            />
            <Box sx={{ flex: 1 }}>
              <Typography variant="h5" sx={{ fontWeight: 'bold' }}>
                {item.name}
              </Typography>
              <Typography variant="h6" sx={{ fontWeight: 'bold' }}>
                {item.price} ₽
              </Typography>
            </Box>
          </ListItem>
        ))}
      </List>

      {/* Инструкция по покупке */}
      <Box sx={{ mt: 3, mb: 3 }}>
        <Typography variant="h6" sx={{ fontWeight: 'bold', mb: 2 }}>
          Инструкция по покупке
        </Typography>
        <Typography variant="body1" sx={{ mb: 1 }}>
          1. Выбор товара
        </Typography>
        <Typography variant="body2" sx={{ mb: 2 }}>
          Добавьте необходимые товары в корзину и запомните предложенную форму.
        </Typography>
        <Typography variant="body1" sx={{ mb: 1 }}>
          2. Оплата товара
        </Typography>
        <Typography variant="body2" sx={{ mb: 2 }}>
          Оплатите товары, добавленные в корзину.
        </Typography>
        <Typography variant="body1" sx={{ mb: 1 }}>
          3. Активация
        </Typography>
        <Typography variant="body2" sx={{ mb: 2 }}>
          После оплаты товара выдаются на выбранном Вами сервере автоматически.
        </Typography>
      </Box>

      <Divider sx={{ my: 3 }} />

      <Box
        sx={{
          background: '#00000087',
          padding: '1rem',
          borderRadius: '20px',
          border: '2px solid #f5b759',
        }}
      >
        {/* Итоговая сумма */}
        <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
          <Typography variant="h5" sx={{ fontWeight: 'bold' }}>
            ИТОГО
          </Typography>
          <Typography variant="h5" sx={{ fontWeight: 'bold' }}>
            {totalPrice.toFixed(2)} ₽
          </Typography>
        </Box>

        {/* Форма для ввода данных */}
        <Box sx={{ mb: 3 }}>
          <TextField
            fullWidth
            label="Ник на сервере"
            value={nickname}
            onChange={(e) => setNickname(e.target.value)}
            sx={{ mb: 2 }}
          />
          <TextField
            fullWidth
            label="Ваш Email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            sx={{ mb: 2 }}
          />
          <FormControlLabel
            control={
              <Checkbox
                checked={agree}
                onChange={(e) => setAgree(e.target.checked)}
                color="primary"
              />
            }
            label={
              <Typography variant="body2">
                Я принимаю условия{' '}
                <Link
                  href="https://legal.easyx.ru/general/rules"
                  target="_blank"
                  rel="noopener noreferrer"
                  color="primary"
                >
                  пользовательского соглашения
                </Link>{' '}
                и{' '}
                <Link
                  href="https://legal.easyx.ru/easydonate/terms-of-service"
                  target="_blank"
                  rel="noopener noreferrer"
                  color="primary"
                >
                  оказания услуг
                </Link>
              </Typography>
            }
          />
        </Box>

        {/* Кнопка "КУПИТЬ" */}
        <Button
          fullWidth
          variant="contained"
          color="primary"
          disabled={!nickname || !email || !agree || cartItems.length === 0 || isLoading}
          onClick={handleCheckout}
          sx={{ py: 2, fontWeight: 'bold' }}
        >
          {isLoading ? 'Обработка...' : 'КУПИТЬ'}
        </Button>
      </Box>

      {/* Уведомление (Snackbar) */}
      <Snackbar
        open={snackbarOpen}
        autoHideDuration={6000} // Уведомление закроется через 6 секунд
        onClose={handleCloseSnackbar}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }} // Позиция уведомления
      >
        <Alert onClose={handleCloseSnackbar} severity={snackbarSeverity} sx={{ width: '100%' }}>
          {snackbarMessage}
        </Alert>
      </Snackbar>
    </Box>
  );
};

export default Cart;