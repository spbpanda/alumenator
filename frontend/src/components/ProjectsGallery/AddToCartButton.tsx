import React from 'react';
import { Button } from '@mui/material';
import ShoppingCartIcon from '@mui/icons-material/ShoppingCart';
import { Good } from '../../types/Good';
import { useTheme } from '@mui/material/styles';

const AddToCartButton = ({onClick}: {item: Good, onClick?: any}) => {
    const theme = useTheme(); // Получаем доступ к теме
    return (
        <Button
            variant="contained"
            startIcon={<ShoppingCartIcon />} // Иконка перед текстом
            onClick={onClick}
            sx={{
                backgroundColor: '#f5b759',
                color: '#fff',
                '&:hover': {
                    backgroundColor: '#e0a548',
                },
                [theme.breakpoints.down('sm')]: {
                 padding: '10px', // Уменьшаем отступы
                }
            }}
        >
            В корзину
        </Button>
    );
};

export default AddToCartButton;