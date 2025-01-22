import React from 'react';
import { Box, Typography, IconButton } from '@mui/material';
import { Link } from 'react-router-dom';
import AddToCartButton from './AddToCartButton';
import { Good } from '../../types/Good';
import { useTheme } from '@mui/material/styles';
import RemoveFromCartButton from './RemoveFromCartButton';

interface GoodsItemProps {
  project: Good;
  isInCart: boolean;
  onAddToCart: (project: Good) => void;
  onRemoveFromCart: (projectId: number) => void;
}

const GoodsItem: React.FC<GoodsItemProps> = ({
  project,
  isInCart,
  onAddToCart,
  onRemoveFromCart,
}) => {
  const theme = useTheme();

  return (
    <Box
      sx={{
        border: '2px solid rgba(255,255,255,.4)',
        borderRadius: '10px',
        overflow: 'hidden',
        padding: '14px',
        transition: 'transform 0.2s, box-shadow 0.2s',
        '&:hover': {
          transform: 'translateY(-4px)',
          boxShadow: 3,
          borderColor: '#f5b759',
          background: '#f5b75930',
        },
        [theme.breakpoints.down('sm')]: {
          padding: '8px',
          border: '1px solid rgba(255,255,255,.4)',
          '&:hover': {
            transform: 'translateY(-2px)',
            boxShadow: 2,
          },
        },
      }}
    >
      <Link to={`/goods/${project.id}`} style={{ textDecoration: 'none' }}>
        <img
          src={project.image}
          alt={project.name}
          style={{ width: '60%', height: 'auto', margin: 'auto', display: 'flex' }}
        />
        <Box
          sx={{
            padding: 2,
            [theme.breakpoints.down('sm')]: {
              padding: '2px',
            },
          }}
        >
          <Typography variant="h6" gutterBottom color="#f5b759">
            {project.name}
          </Typography>
          <Typography variant="h6" gutterBottom>
            {project.price} ₽
          </Typography>
        </Box>
      </Link>

      <Box sx={{ display: 'flex', justifyContent: 'center', marginTop: 0 }}>
        {isInCart ? (
        <RemoveFromCartButton
            onClick={(e: any) => {
                e.stopPropagation();
                onRemoveFromCart(project.id)
            }}
        />
        ) : (
          <AddToCartButton
            item={project}
            onClick={(e: any) => {
              e.stopPropagation();
              onAddToCart(project);
            }}
          />
        )}
      </Box>
    </Box>
  );
};

export default GoodsItem;