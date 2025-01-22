import React from 'react';
import { Box, Typography, Button } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { cartService } from '../services/cartService';

const Thanks: React.FC = () => {
  const navigate = useNavigate();

  const handleGoHome = () => {
    cartService.clearCart();
    navigate('/');
  };

  return (
    <Box
      sx={{
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center',
        minHeight: '50vh',
        textAlign: 'center',
        padding: 3,
        background: '#00000087',
        borderRadius: '20px',
        border: '2px solid #f5b759',
        maxWidth: 600,
        margin: '0 auto',
      }}
    >
      <Typography variant="h3" sx={{ fontWeight: 'bold', mb: 3 }}>
        Спасибо за покупку!
      </Typography>
      <Typography variant="h5" sx={{ mb: 3 }}>
        Ваш заказ успешно оформлен. Товары будут автоматически выданы на выбранном сервере.
      </Typography>
      <Typography variant="body1" sx={{ mb: 3 }}>
        Если у вас возникнут вопросы, свяжитесь с нашей поддержкой.
      </Typography>
      <Button
        variant="contained"
        color="primary"
        onClick={handleGoHome}
        sx={{ py: 2, fontWeight: 'bold' }}
      >
        Вернуться на главную
      </Button>
    </Box>
  );
};

export default Thanks;