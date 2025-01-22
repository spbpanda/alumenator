import React from 'react';
import { Button } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete'; // Иконка удаления
import { useTheme } from '@mui/material/styles';

interface RemoveFromCartButtonProps {
  onClick: (e: any) => void;
}

const RemoveFromCartButton: React.FC<RemoveFromCartButtonProps> = ({ onClick }) => {
  const theme = useTheme(); // Получаем доступ к теме

  return (
    <Button
      variant="contained"
      startIcon={<DeleteIcon />} // Иконка удаления
      onClick={onClick}
      sx={{
        backgroundColor: '#ff4444', // Красный фон
        color: '#fff', // Белый текст
        '&:hover': {
          backgroundColor: '#cd3030', // Темнее красный при наведении
        },
        [theme.breakpoints.down('sm')]: {
          padding: '10px', // Уменьшаем отступы для мобильных устройств
        },
      }}
    >
      Удалить
    </Button>
  );
};

export default RemoveFromCartButton;