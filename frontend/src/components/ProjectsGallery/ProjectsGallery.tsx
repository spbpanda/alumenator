import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import {
  Box,
  Typography,
  CircularProgress,
  Pagination,
  Snackbar,
  Alert,
} from '@mui/material';
import CartButton from './CartButton';
import ServerSlider from './ServerSlider';
import FilterButtons from './FilterButtons';
import GoodsList from './GoodsList';
import { Good } from '../../types/Good';
import { cartService } from '../../services/cartService';


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
  const [cartItems, setCartItems] = useState<Good[]>([]);
  const [snackbarOpen, setSnackbarOpen] = useState<boolean>(false);
  const [snackbarMessage, setSnackbarMessage] = useState<string>('');
  const [snackbarSeverity, setSnackbarSeverity] = useState<'error' | 'success' | 'info' | 'warning'>('error');
  const [initialSelectedIndex, setInitialSelectedIndex] = useState<number>(0);
  const navigate = useNavigate();

  // Загружаем корзину при монтировании компонента
  useEffect(() => {
    setCartItems(cartService.getCartItems());
  }, []);

  // Загружаем выбранный сервер и тип товаров из localStorage при монтировании компонента
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

  // Сохраняем выбранный сервер в localStorage при его изменении
  useEffect(() => {
    localStorage.setItem('selectedServer', selectedServer);
  }, [selectedServer]);

  // Сохраняем выбранный тип товаров в localStorage при его изменении
  useEffect(() => {
    localStorage.setItem('selectedType', selectedType);
  }, [selectedType]);

  // Вычисляем initialSelectedIndex после загрузки servers и при изменении selectedServer
  useEffect(() => {
    if (servers.length > 0) {
      const index = servers.findIndex(server => server.name === selectedServer);
      setInitialSelectedIndex(index >= 0 ? index : 0);
    }
  }, [servers, selectedServer]);

  const delay = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

  const fetchProjects = async (page: number, type: string = selectedType, query: string = searchQuery, server: string = selectedServer) => {
    try {
      const response = await axios.get('/api/goods', {
        params: {
          page,
          limit: 8,
          type,
          server,
          search: query,
        },
      });
      setGoods(response.data.goods);
      setTotalPages(response.data.totalPages);
      setLoading(false);
    } catch (error) {
      console.error('Error fetching projects:', error);
      setLoading(false);
      showSnackbar('Failed to load projects. Please try again later.', 'error');
    }
  };

  // Загружаем товары при изменении currentPage, selectedType, searchQuery или selectedServer
  useEffect(() => {
    fetchProjects(currentPage, selectedType, searchQuery, selectedServer);
  }, [currentPage, selectedType, searchQuery, selectedServer]);

  const fetchServers = async () => {
    try {
      const response = await axios.get('/api/servers');
      await delay(1000);
      setServers(response.data);
    } catch (error) {
      console.error('Error fetching servers:', error);
      showSnackbar('Failed to load servers. Please try again later.', 'error');
      setServers([]); // Устанавливаем пустой массив, чтобы избежать ошибок
      setInitialSelectedIndex(0); // Устанавливаем индекс по умолчанию
    }
  };

  // Загружаем серверы при монтировании компонента
  useEffect(() => {
    fetchServers();
  }, []);

  const handlePageChange = (event: React.ChangeEvent<unknown>, page: number) => {
    setCurrentPage(page);
  };

  const handleServerSelect = (serverName: string) => {
    if (cartItems.length > 0) {
      cartService.clearCart();
      setCartItems([]);
      showSnackbar('Корзина очищена. К сожалению, нет возможности одновременно купить товары для разных серверов.', 'info');
    }
    setSelectedServer(serverName);
    setCurrentPage(1);
  };

  const handleTypeChange = (type: string) => {
    setSelectedType(type);
    setCurrentPage(1);
  };

  const handleGoToCart = () => {
    navigate('/cart');
  };

  const handleAddToCart = (project: Good) => {
    cartService.addToCart(project);
    setCartItems([...cartService.getCartItems()]);
  };

  const handleRemoveFromCart = (projectId: number) => {
    cartService.removeFromCart(projectId);
    setCartItems([...cartService.getCartItems()]);
  };

  const showSnackbar = (message: string, severity: 'error' | 'success' | 'info' | 'warning') => {
    setSnackbarMessage(message);
    setSnackbarSeverity(severity);
    setSnackbarOpen(true);
  };

  const handleCloseSnackbar = () => {
    setSnackbarOpen(false);
  };

  if (loading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="200px">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Box sx={{ padding: 3 }}>
      <CartButton
        onClick={handleGoToCart}
        itemCount={cartItems.length}
      />
      <ServerSlider
        servers={servers}
        initialSelectedIndex={initialSelectedIndex}
        onServerSelect={handleServerSelect}
      />
      <Typography variant="h4" gutterBottom>Товары</Typography>
      <FilterButtons selectedType={selectedType} onTypeChange={handleTypeChange} />
      {goods.length === 0 ? (
        <Box display="flex" justifyContent="center" alignItems="center" minHeight="200px">
          <Typography variant="h6" color="textSecondary">
            Товаров для этого сервера нет
          </Typography>
        </Box>
      ) : (
        <GoodsList
          goods={goods}
          cartItems={cartItems}
          onAddToCart={handleAddToCart}
          onRemoveFromCart={handleRemoveFromCart}
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