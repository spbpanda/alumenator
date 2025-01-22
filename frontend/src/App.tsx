import React from 'react';
import { Route, Routes } from 'react-router-dom';
import { Box, CssBaseline } from '@mui/material';
import Navbar from './components/Navbar/Navbar';
import Main from './components/Main';
import ProjectDetails from './components/GoodDetails';
import ProjectsGallery from './components/ProjectsGallery/ProjectsGallery';
import Cart from './components/Cart';
import SnowBackground from './components/SnowBackground'; // Импортируем SnowBackground
import Thanks from './components/Thanks';
import Footer from './components/Footer';

const App: React.FC = () => {
  return (
    <>
      <CssBaseline />
      <Box
        sx={{
          minHeight: '100vh',
          position: 'relative',
        }}
      >
        <SnowBackground /> {/* Используем SnowBackground */}
        <Navbar />
        <Main />
        <Routes>
          <Route path="/" element={<ProjectsGallery />} />
          <Route path="/goods/:id" element={<ProjectDetails />} />
          <Route path="/cart" element={<Cart />} />
          <Route path="/thanks" element={<Thanks />} />
        </Routes>
        <Footer />
      </Box>
    </>
  );
};

export default App;