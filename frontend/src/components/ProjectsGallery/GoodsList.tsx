import React from 'react';
import { Grid, Box, Typography } from '@mui/material';
import GoodsItem from './GoodsItem';
import { Good } from '../../types/Good';

interface GoodsListProps {
  goods: Good[];
  cartItems: Good[];
  onAddToCart: (project: Good) => void;
  onRemoveFromCart: (projectId: number) => void;
}

const GoodsList: React.FC<GoodsListProps> = ({
  goods,
  cartItems,
  onAddToCart,
  onRemoveFromCart,
}) => {
  return (
    <Grid container spacing={3}>
      {goods.map((project) => (
        <Grid item xs={6} sm={4} md={3} key={project.id}>
          <GoodsItem
            project={project}
            isInCart={cartItems.some((item) => item.id === project.id)}
            onAddToCart={onAddToCart}
            onRemoveFromCart={onRemoveFromCart}
          />
        </Grid>
      ))}
    </Grid>
  );
};

export default GoodsList;