import React, { useState } from 'react';
import { Box, Typography, Button, Snackbar, Alert } from '@mui/material';
import ContentCopyIcon from '@mui/icons-material/ContentCopy'; // Иконка копирования

const Main: React.FC = () => {
  const [openSnackbar, setOpenSnackbar] = useState(false); // Состояние для управления Snackbar

  // Функция для копирования текста в буфер обмена
  const handleCopyText = () => {
    navigator.clipboard
      .writeText('play.alumenator.net')
      .then(() => {
        setOpenSnackbar(true); // Показываем Snackbar
      })
      .catch((err) => {
        console.error('Ошибка при копировании:', err);
      });
  };

  // Функция для закрытия Snackbar
  const handleCloseSnackbar = () => {
    setOpenSnackbar(false);
  };

  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'center',
        alignItems: 'center',
        flexDirection: 'column', // Добавляем вертикальное выравнивание
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

        {/* Кнопка для копирования */}
        <Button
          variant="contained"
          onClick={handleCopyText}
          sx={{
            marginTop: 3, // Отступ сверху
          }}
          endIcon={<ContentCopyIcon />} // Иконка копирования
        >
          play.alumenator.net
        </Button>
      </Box>
      {/* Уведомление Snackbar */}
      <Snackbar
        open={openSnackbar}
        autoHideDuration={3000} // Закрыть через 3 секунды
        onClose={handleCloseSnackbar}
        anchorOrigin={{ vertical: 'top', horizontal: 'center' }} // Позиция уведомления
      >
        <Alert onClose={handleCloseSnackbar} severity="success" sx={{ width: '100%' }}>
          Скопировано: play.alumenator.net
        </Alert>
      </Snackbar>
    </Box>
  );
};

export default Main;