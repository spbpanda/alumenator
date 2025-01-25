// GoodDetails.tsx
import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import {
  Box,
  Typography,
  CircularProgress,
  Alert,
  Paper,
  Grid,
  Chip,
  Rating,
  ImageList,
  ImageListItem,
} from '@mui/material';
import AddToCartButton from './ProjectsGallery/AddToCartButton';
import { useCart } from '../contexts/CartContext';
import PageHeader from './PageHeader';
import { Good } from '../types/Good';
import api from '../api';

const GoodDetails: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const [good, setGood] = useState<Good | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);
  const { addToCart } = useCart();

  useEffect(() => {
    api
      .get(`/goods/${id}`)
      .then((response) => {
        setGood(response.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error('Error fetching good details:', error);
        setError('Failed to load good details. Please try again later.');
        setLoading(false);
      });
  }, [id]);

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="100vh">
        <CircularProgress />
      </Box>
    );
  }

  if (error) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="100vh">
        <Alert severity="error" sx={{ width: '100%', maxWidth: 600 }}>
          {error}
        </Alert>
      </Box>
    );
  }

  if (!good) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="100vh">
        <Alert severity="warning" sx={{ width: '100%', maxWidth: 600 }}>
          Good not found.
        </Alert>
      </Box>
    );
  }

  return (
    <Box sx={{ padding: 2, maxWidth: 1200, margin: '0 auto' }}>
      <PageHeader title={good.name} />

      <Paper elevation={3} sx={{ padding: 2, backgroundColor: 'transparent', boxShadow: 'none' }}>
        <Grid container spacing={4}>
          {/* Изображение товара */}
          <Grid item xs={12} md={6}>
            <Box
              component="img"
              src={good.image}
              alt={good.name}
              sx={{
                width: '100%',
                height: 'auto',
                borderRadius: 2,
                marginBottom: 2,
              }}
            />
            {good.additionalImages && good.additionalImages.length > 0 && (
              <ImageList cols={3} gap={8}>
                {good.additionalImages.map((img, index) => (
                  <ImageListItem key={index}>
                    <img
                      src={img}
                      alt={`${good.name} ${index + 1}`}
                      loading="lazy"
                      style={{ borderRadius: 8 }}
                    />
                  </ImageListItem>
                ))}
              </ImageList>
            )}
          </Grid>

          {/* Информация о товаре */}
          <Grid item xs={12} md={6}>
            {/* Рейтинг */}
            {good.rating && (
              <Box sx={{ display: 'flex', alignItems: 'center', mb: 2 }}>
                <Rating value={good.rating} precision={0.5} readOnly />
                <Typography variant="body2" sx={{ ml: 1, color: 'text.secondary' }}>
                  ({good.rating.toFixed(1)})
                </Typography>
              </Box>
            )}

            <Box sx={{ display: 'flex', flexDirection: 'row', justifyContent: 'space-between', alignItems: 'baseline' }}>
              {/* Цена */}
              <Typography variant="h4" sx={{ fontWeight: 'bold', mb: 2 }}>
                {good.price} ₽
              </Typography>
              {/* Кнопка покупки */}
              <AddToCartButton item={good} onClick={() => addToCart(good)} />
            </Box>

            <Box sx={{ display: 'flex', flexDirection: 'row', alignItems: 'baseline', flexWrap: 'wrap', gap: 1 }}>
              {/* Сервер */}
              {good.servers.map((server) => (
                <Chip label={server.name} color="primary" sx={{ mb: 2 }} key={server.id} />
              ))}
            </Box>

            {/* Описание товара */}
            {good.description && (
              <Box sx={{ marginTop: 3, background: '#00000087', padding: '1rem', borderRadius: '20px', border: '2px solid #f5b759' }}>
                <Typography variant="h5" sx={{ fontWeight: 'bold', mb: 2 }}>
                  Описание
                </Typography>
                <Typography variant="body1" sx={{ whiteSpace: 'pre-line' }}>
                  {good.description}
                </Typography>
              </Box>
            )}
          </Grid>
        </Grid>
      </Paper>
    </Box>
  );
};

export default GoodDetails;