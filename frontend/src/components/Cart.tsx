// Cart.tsx
import React, { useState } from 'react';
import {
  Box,
  Typography,
  Button,
  List,
  ListItem,
  TextField,
  Checkbox,
  FormControlLabel,
  Divider,
  Link,
  Snackbar,
  Alert,
  useTheme,
} from '@mui/material';
import { useCart } from '../contexts/CartContext';
import RemoveFromCartButton from './ProjectsGallery/RemoveFromCartButton';
import PageHeader from './PageHeader';
import api from '../api';

const Cart: React.FC = () => {
  const { cartItems, removeFromCart } = useCart();
  const [nickname, setNickname] = useState<string>('');
  const [email, setEmail] = useState<string>('');
  const [agree, setAgree] = useState<boolean>(false);
  const [isLoading, setIsLoading] = useState<boolean>(false);
  const [snackbarOpen, setSnackbarOpen] = useState<boolean>(false);
  const [snackbarMessage, setSnackbarMessage] = useState<string>('');
  const [snackbarSeverity, setSnackbarSeverity] = useState<'error' | 'success' | 'info' | 'warning'>('error');
  const theme = useTheme(); // Получаем доступ к теме

  // Общая стоимость
  const totalPrice = cartItems.reduce((sum, item) => sum + item.price, 0);

  // Оформление заказа
  const handleCheckout = async () => {
    if (!nickname || !email || !agree || cartItems.length === 0) {
      showSnackbar('Пожалуйста, заполните все поля и согласитесь с условиями.', 'error');
      return;
    }

    setIsLoading(true);

    try {
      const products = cartItems.reduce((acc, item) => {
        acc[item.id] = 1; // Количество товара (по умолчанию 1)
        return acc;
      }, {} as Record<number, number>);

      const response = await api.post('/create-payment', {
        customer: nickname,
        server_id: JSON.parse(localStorage.getItem('server')!).id,
        products,
        email,
        success_url: 'https://alumenator.net/thanks',
      });

      if (response.data.success) {
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
            sx={{ 
              display: 'flex', 
              alignItems: 'center', 
              maxHeight: '100px',
              [theme.breakpoints.down("sm")]: {
                flexDirection: 'column',
                maxHeight: '300px',
              }
            }}
          >
            <Box
              component="img"
              src={item.image}
              alt={item.name}
              sx={{
                width: { xs: '60px', sm: '80px', md: '100px' },
                height: { xs: '60px', sm: '80px', md: '100px' },
                borderRadius: 2,
                marginRight: 2,
                objectFit: 'cover'
              }}
            />
            <Box sx={{ flex: 1 }}>
              <Typography variant="h5" sx={{ 
                  fontWeight: 'bold',
                  [theme.breakpoints.down("sm")]: {
                    fontSize: '1rem'
                  } 
                }}>
                {item.name}
              </Typography>
              <Typography variant="h6" sx={{ fontWeight: 'bold' }}>
                {item.price} ₽
              </Typography>
            </Box>
            <RemoveFromCartButton onClick={() => removeFromCart(item.id)} />
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
        autoHideDuration={6000}
        onClose={handleCloseSnackbar}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
      >
        <Alert onClose={handleCloseSnackbar} severity={snackbarSeverity} sx={{ width: '100%' }}>
          {snackbarMessage}
        </Alert>
      </Snackbar>
    </Box>
  );
};

export default Cart;