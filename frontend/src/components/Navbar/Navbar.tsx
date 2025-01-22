import React from 'react';
import { AppBar, Toolbar, Button, Container, Box } from '@mui/material';
import DiscordIcon from '../Icons/DiscordIcon';
import TelegramIcon from '../Icons/TelegramIcon';
import VKIcon from '../Icons/VKIcon';
import YoutubeIcon from '../Icons/YoutubeIcon';
import styles from './Navbar.module.scss';
import LogoIcon from '../Icons/LogoIcon';

const Navbar: React.FC = () => {

  const contacts = (
    <>
      <Button
        color="inherit"
        startIcon={<VKIcon className={styles.icon} />}
        href="https://vk.com/alumenator"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent' }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<TelegramIcon className={styles.icon} />}
        href="https://t.me/AlumenatoR"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'  }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<DiscordIcon className={styles.icon} />}
        href="https://discord.gg/alumenator"
        sx={{ textTransform: 'none', padding: '4px', minWidth: '32px', "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'  }}
      >
      </Button>
      <Button
        color="inherit"
        startIcon={<YoutubeIcon className={styles.icon} />}
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
              startIcon={<LogoIcon className={styles.iconLogo} />}
              href="/"
              sx={{ "& .MuiButton-startIcon": { margin: "0px" }, background: 'transparent'}}
            >
            </Button>
          </Box>

          <Box sx={{ display: 'flex', alignItems: 'center', gap: 0.5 }}>{contacts}</Box>
        </Toolbar>
      </Container>
    </AppBar>
  );
};

export default Navbar;