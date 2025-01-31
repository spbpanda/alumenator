import React from 'react';
import { AppBar, Toolbar, Button, Container, Box, useTheme } from '@mui/material';
import DiscordIcon from './Icons/DiscordIcon';
import TelegramIcon from './Icons/TelegramIcon';
import VKIcon from './Icons/VKIcon';
import YoutubeIcon from './Icons/YoutubeIcon';
import LogoIcon from './Icons/LogoIcon';
import CartButton from './ProjectsGallery/CartButton';

const Navbar: React.FC = () => {
  const theme = useTheme(); // Получаем доступ к теме

  const contacts = (
    <>
      <Button
        color="inherit"
        startIcon={<VKIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
        href="https://vk.com/alumenator"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent' }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<TelegramIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
        href="https://t.me/AlumenatoR"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'  }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<DiscordIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
        href="https://discord.gg/alumenator"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'  }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<YoutubeIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
        href="https://www.youtube.com/@AlumenatoR"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'  }}
      >
      </Button>
    </>
  );

  return (
    <AppBar elevation={0}
      sx={{
        position: 'sticky',
        background: "#00000087",
        backdropFilter: 'blur(5px)',
        top: '0',
        color: 'white',
      }}
    >
      <Container maxWidth="lg">
        <Toolbar disableGutters>
          
          <Box 
            sx={{ 
              textTransform: 'none', 
              padding: '4px',
              justifyContent: 'flex-start',
              flexGrow: 1,  
          }}>
            <Button
              color="inherit"
              startIcon={<LogoIcon style={{width: '50px', height: '32px', fill: '#fff'}}/>}
              href="/"
              sx={{ "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'}}
            >
            </Button>
          </Box>
          <Box 
            sx={{
              display: 'none', // По умолчанию скрываем текст
              [theme.breakpoints.up('sm')]: {
                display: 'block', // Показываем текст на десктопах
              },
            }}>
            <CartButton />
          </Box>
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>{contacts}</Box>
        </Toolbar>
      </Container>
    </AppBar>
  );
};

export default Navbar;