import React from 'react';
import { Button, Badge } from '@mui/material';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';

interface CartButtonProps {
  onClick: () => void;
  itemCount: number; // Количество товаров в корзине
}

const CartButton: React.FC<CartButtonProps> = ({ onClick, itemCount }) => {
  return (
    <Button
      variant="contained"
      color="primary"
      onClick={onClick}
      sx={{
        position: 'fixed',
        bottom: 20,
        right: 20,
        zIndex: 1000,
        borderRadius: '50%',
        width: '64px',
        height: '64px',
        minWidth: 'unset', // Убираем минимальную ширину, чтобы кнопка была круглой
        backgroundColor: '#f5b759',
      }}
    >
      <Badge
        badgeContent={itemCount} // Отображаем количество товаров
        color="secondary" // Цвет счетчика
        overlap="circular" // Чтобы счетчик не выходил за границы кнопки
        sx={{
          '& .MuiBadge-badge': {
            top: -10, // Позиционируем счетчик
            right: -10,
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