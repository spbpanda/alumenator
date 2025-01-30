import React, { useState, useEffect, useRef } from 'react';
import {
  Box,
  Typography,
  CircularProgress,
  Pagination,
  Snackbar,
  Alert,
  InputAdornment,
  IconButton,
  TextField,
} from '@mui/material';
import ClearIcon from '@mui/icons-material/Clear';
import ServerSlider from './ServerSlider';
import FilterButtons from './FilterButtons';
import GoodsList from './GoodsList';
import { Good } from '../../types/Good';
import { useCart } from '../../contexts/CartContext';
import api from '../../api';
import axios, { CancelTokenSource } from 'axios';

const ProjectsGallery: React.FC = () => {
  const [goods, setGoods] = useState<Good[]>([]);
  const [selectedType, setSelectedType] = useState<string>(
    localStorage.getItem('selectedType') || 'all'
  );
  const [loading, setLoading] = useState<boolean>(true);
  const [currentPage, setCurrentPage] = useState<number>(1);
  const [totalPages, setTotalPages] = useState<number>(1);
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedServer, setSelectedServer] = useState<string>(
    localStorage.getItem('selectedServer') || 'Магическое выживание №1 и №2'
  );
  const [servers, setServers] = useState<any[]>([]);
  const [snackbarOpen, setSnackbarOpen] = useState<boolean>(false);
  const [snackbarMessage, setSnackbarMessage] = useState<string>('');
  const [snackbarSeverity, setSnackbarSeverity] = useState<'error' | 'success' | 'info' | 'warning'>('error');
  const [initialSelectedIndex, setInitialSelectedIndex] = useState<number>(0);
  const { cartItems, addToCart, removeFromCart, clearCart } = useCart();

  const cancelTokenSourceRef = useRef<CancelTokenSource | null>(null);

  const handleSearchChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(event.target.value);
    setCurrentPage(1);
  };

  const handleClearSearch = () => {
    setSearchQuery('');
    setCurrentPage(1);
  };

  useEffect(() => {
    const savedServer = localStorage.getItem('selectedServer');
    const savedType = localStorage.getItem('selectedType');
    if (savedServer) {
      setSelectedServer(savedServer);
    }
    if (savedType) {
      setSelectedType(savedType);
    }
  }, []);

  useEffect(() => {
    localStorage.setItem('selectedServer', selectedServer);
  }, [selectedServer]);

  useEffect(() => {
    localStorage.setItem('selectedType', selectedType);
  }, [selectedType]);

  useEffect(() => {
    if (servers.length > 0) {
      const index = servers.findIndex(server => server.name === selectedServer);
      setInitialSelectedIndex(index >= 0 ? index : 0);
    }
  }, [servers, selectedServer]);

  const fetchProjects = async (
    page: number,
    type: string = selectedType,
    query: string = searchQuery,
    server: string = selectedServer
  ) => {
    // Отмена предыдущего запроса, если он существует
    if (cancelTokenSourceRef.current) {
      cancelTokenSourceRef.current.cancel('Request canceled due to new request');
    }

    // Создаем новый источник токена отмены
    const cancelTokenSource = axios.CancelToken.source();
    cancelTokenSourceRef.current = cancelTokenSource;

    try {
      setLoading(true);

      const response = await api.get('/goods', {
        params: {
          page,
          limit: 8,
          type,
          server,
          search: query,
        },
        cancelToken: cancelTokenSource.token,
      });

      setGoods(response.data.goods);
      setTotalPages(response.data.totalPages);
      setLoading(false);
    } catch (error) {
      if (axios.isCancel(error)) {
        console.log('Request canceled:', error.message);
      } else {
        console.error('Error fetching projects:', error);
        setLoading(false);
      }
    } finally {
      // Сбрасываем токен отмены после завершения запроса
      cancelTokenSourceRef.current = null;
    }
  };

  useEffect(() => {
    fetchProjects(currentPage, selectedType, searchQuery, selectedServer);

    // Очистка при размонтировании компонента
    return () => {
      if (cancelTokenSourceRef.current) {
        cancelTokenSourceRef.current.cancel('Component unmounted');
      }
    };
  }, [currentPage, selectedType, searchQuery, selectedServer]);

  const fetchServers = async () => {
    try {
      const response = await api.get('/servers');
      setServers(response.data);
    } catch (error) {
      console.error('Error fetching servers:', error);
      showSnackbar('Failed to load servers. Please try again later.', 'error');
      setServers([]);
      setInitialSelectedIndex(0);
    }
  };

  useEffect(() => {
    fetchServers();
  }, []);

  const handlePageChange = (event: React.ChangeEvent<unknown>, page: number) => {
    setCurrentPage(page);
  };

  const handleServerSelect = (serverName: string) => {
    if (cartItems.length > 0) {
      clearCart();
      showSnackbar('Корзина очищена. К сожалению, нет возможности одновременно купить товары для разных серверов.', 'info');
    }
    setSelectedServer(serverName);
    setCurrentPage(1);
  };

  const handleTypeChange = (type: string) => {
    setSelectedType(type);
    setCurrentPage(1);
  };

  const showSnackbar = (message: string, severity: 'error' | 'success' | 'info' | 'warning') => {
    setSnackbarMessage(message);
    setSnackbarSeverity(severity);
    setSnackbarOpen(true);
  };

  const handleCloseSnackbar = () => {
    setSnackbarOpen(false);
  };

  return (
    <Box sx={{ padding: 3, maxWidth: 1200, margin: 'auto' }}>
      <ServerSlider
        servers={servers}
        initialSelectedIndex={initialSelectedIndex}
        onServerSelect={handleServerSelect}
      />
      <Typography variant="h4" gutterBottom>Товары</Typography>
      <TextField
        fullWidth
        variant="outlined"
        placeholder="Введите товар который хотите найти..."
        value={searchQuery}
        onChange={handleSearchChange}
        sx={{ marginBottom: 3 }}
        InputProps={{
          endAdornment: (
            <InputAdornment position="end">
              {searchQuery && (
                <IconButton onClick={handleClearSearch} edge="end" sx={{ color: 'white' }}>
                  <ClearIcon />
                </IconButton>
              )}
            </InputAdornment>
          ),
        }}
      />
      <FilterButtons selectedType={selectedType} onTypeChange={handleTypeChange} />
      {loading ? (
        <Box display="flex" justifyContent="center" alignItems="center" minHeight="200px">
          <CircularProgress />
        </Box>
      ) : goods.length === 0 ? (
        <Box display="flex" justifyContent="center" alignItems="center" minHeight="200px">
          <Typography variant="h6" color="white">
            Товаров для этого сервера нет
          </Typography>
        </Box>
      ) : (
        <GoodsList
          goods={goods}
          cartItems={cartItems}
          onAddToCart={addToCart}
          onRemoveFromCart={removeFromCart}
        />
      )}
      <Box sx={{ display: 'flex', justifyContent: 'center', marginTop: 3 }}>
        <Pagination
          count={totalPages}
          page={currentPage}
          onChange={handlePageChange}
          color="primary"
        />
      </Box>

      <Snackbar
        open={snackbarOpen}
        autoHideDuration={6000}
        onClose={handleCloseSnackbar}
        anchorOrigin={{ vertical: 'top', horizontal: 'right' }}
      >
        <Alert onClose={handleCloseSnackbar} severity={snackbarSeverity} sx={{ width: '100%' }}>
          {snackbarMessage}
        </Alert>
      </Snackbar>
    </Box>
  );
};

export default ProjectsGallery;