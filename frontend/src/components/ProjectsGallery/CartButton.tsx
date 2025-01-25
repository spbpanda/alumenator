import React from 'react';
import { Button, Badge, useTheme } from '@mui/material';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';
import { useNavigate } from 'react-router-dom';
import { useCart } from '../../contexts/CartContext';

const CartButton: React.FC = () => {
  const { cartItems } = useCart();
  const navigate = useNavigate();
  const theme = useTheme(); // Получаем доступ к теме

  return (
    <Button
      variant="contained"
      color="primary"
      onClick={() => navigate('/cart')}
      sx={{
        zIndex: 1000,
        borderRadius: '50%',
        maxWidth: '60px',
        height: '60px',
        minWidth: 'unset', // Убираем минимальную ширину, чтобы кнопка была круглой
        backgroundColor: '#f5b759',

        // Стили только для мобильных устройств (ширина экрана < 600px)
        [theme.breakpoints.down('sm')]: {
          position: 'fixed',
          left: '35vh',
          top: '90vh',
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
    </Button>
  );
};

export default CartButton;