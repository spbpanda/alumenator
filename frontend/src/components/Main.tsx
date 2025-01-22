import React from 'react';
import { Box, Typography } from '@mui/material';

const Main: React.FC = () => {
  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
      }}
    >
      <Box
        sx={{
          textAlign: 'center',
          maxWidth: '800px', // Ограничение ширины контента
          padding: '20px',
        }}
      >
        <Typography
          variant="h1"
          sx={{
            fontFamily: 'Comfortaa, sans-serif', // Шрифт Comfortaa
            fontWeight: 700, // Жирный
            color: '#f5b759', // Цвет текста
            fontSize: { xs: '2.5rem', sm: '3rem', md: '4rem' }, // Адаптивный размер
          }}
        >
          AlumenatoR
        </Typography>
      </Box>
    </Box>
  );
};

export default Main;