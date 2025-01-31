import React from 'react';
import { Button, Badge, useTheme, Box } from '@mui/material';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../../contexts/CartContext';

const CartButton: React.FC = () => {
  const { cartItems } = useCart();
  const navigate = useNavigate();
  const theme = useTheme(); // Получаем доступ к теме

  return (
    <Box
      onClick={() => navigate('/cart')}
      sx={{
        zIndex: 1000,
        borderRadius: '30px',
        minWidth: 'unset', // Убираем минимальную ширину, чтобы кнопка была круглой
        backgroundColor: '#f5b759cf',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        gap: 1, // Отступ между иконкой и текстом
      }}>
      <Button
        variant="contained"
        color="primary"
        sx={{
          zIndex: 1000,
          borderRadius: '50%',
          maxWidth: '60px',
          height: '60px',
          minWidth: 'unset', // Убираем минимальную ширину, чтобы кнопка была круглой
          backgroundColor: '#f5b759',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'center',
          gap: 1, // Отступ между иконкой и текстом

          // Стили для десктопных устройств (ширина экрана >= 600px)
          [theme.breakpoints.up('sm')]: {
            borderRadius: '30px', // Делаем кнопку более прямоугольной
            maxWidth: 'none', // Убираем ограничение по ширине
            padding: '8px 16px', // Добавляем отступы для текста
            position: 'static', // Возвращаем кнопку в обычный поток
          },
        }}
      >
        <Badge
          badgeContent={cartItems.length} // Отображаем количество товаров
          color="secondary" // Цвет счетчика
          overlap="circular" // Чтобы счетчик не выходил за границы кнопки
          sx={{
            '& .MuiBadge-badge': {
              top: -5, // Позиционируем счетчик
              right: -5,
              width: '24px',
              height: '24px',
              fontSize: '16px',
              backgroundColor: '#f5b759',
            },
          }}
        >
          <ShoppingCartIcon />
        </Badge>
        {/* Текст "Корзина" для десктопных устройств */}
      </Button>
      <Box
        sx={{
          padding: '0 15px 0 0',
        }}
      >
        Корзина
      </Box>
    </Box>
  );
};

export default CartButton;