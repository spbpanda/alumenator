import React from 'react';
import { Button } from '@mui/material';
import LocalOfferIcon from '@mui/icons-material/LocalOffer';

const FilterButton = ({name, click, isActive}: any) => {
  return (
    <Button
      variant={isActive ? 'contained' : 'outlined'}
      startIcon={<LocalOfferIcon />} 
      sx={{
        height: '42px',
        cursor: 'pointer',
        color: 'white',
        borderRadius: '12px',
        padding: '8px 12px',
        textTransform: 'uppercase',
        transition: '.3s',
        fontWeight: '700',
        border: '2px solid transparent',
        boxShadow: 'none',
        '&:hover': {
            color: '#f5b759',
            border: '2px solid #f5b759',
            background: '#f5b75930',
        },
        '&:active': {
            color: '#f5b759',
            border: '2px solid #f5b759',
            background: '#f5b75930'
        },
        '&.MuiButton-outlined': {
            background: 'none',
        },
        '&.MuiButton-contained': {
            color: '#f5b759',
            border: '2px solid #f5b759',
            background: '#f5b75930'
        }
      }}
      onClick={click}
    >
      {name}
    </Button>
  );
};

export default FilterButton;