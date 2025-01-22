import React from 'react';
import { Box, Button, Typography } from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import { useNavigate } from 'react-router-dom';

interface PageHeaderProps {
  title: string; // Название, которое нужно отображать
}

const PageHeader: React.FC<PageHeaderProps> = ({ title }) => {
  const navigate = useNavigate();

  return (
    <Box
      sx={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: { xs: 'center', sm: 'baseline' },
        mb: 2,
        gap: 2,
      }}
    >
      {/* Кнопка "Назад" */}
      <Button
        startIcon={<ArrowBackIcon />}
        onClick={() => navigate(-1)} // Возврат на предыдущую страницу
        sx={{
          '& .MuiButton-startIcon': {
            marginRight: { xs: 0, sm: '8px' }, // Убираем отступ для иконки на мобильных устройствах
          },
        }}
      >
        {/* Условно отображаем текст "Назад" только на десктопах */}
        <Box component="span" sx={{ display: { xs: 'none', sm: 'inline' } }}>
          Назад
        </Box>
      </Button>

      {/* Заголовок */}
      <Typography
        variant="h4"
        sx={{
          fontWeight: 'bold',
          fontSize: { xs: '1.5rem', sm: '2rem' }, // Адаптивный размер текста
          flex: '1 1 100%', // Занимает все доступное пространство
          textAlign: 'center', // Центрируем текст
        }}
      >
        {title}
      </Typography>

      {/* Пустой Box для выравнивания */}
      <Box sx={{ width: 100 }} />
    </Box>
  );
};

export default PageHeader;