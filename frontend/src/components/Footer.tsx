import React from 'react';
import { Box, Typography, Link, Grid, Button } from '@mui/material';
import { Email } from '@mui/icons-material';
import YoutubeIcon from './Icons/YoutubeIcon';
import DiscordIcon from './Icons/DiscordIcon';
import TelegramIcon from './Icons/TelegramIcon';
import VKIcon from './Icons/VKIcon';

const Footer: React.FC = () => {
  return (
    <Box
      sx={{
        backgroundColor: '#1a1a1a', // Темный фон, как на сайте
        color: '#ffffff', // Белый текст
        padding: '20px 10px',
        marginTop: 'auto', // Чтобы футер прижимался к низу страницы
      }}
    >
      <Grid container spacing={2} justifyContent="center">
        {/* Логотип и описание */}
        <Grid item xs={12} md={4} textAlign="center">
          <Typography variant="h5" sx={{ fontWeight: 'bold', mb: 1 }}>
            Alumenator
          </Typography>
          <Typography variant="body2" sx={{ mb: 1 }}>
            Мы предоставляем лучшие товары для вашего игрового опыта. Присоединяйтесь к нам!
          </Typography>
        </Grid>

        {/* Контактная информация */}
        <Grid item xs={12} md={4} textAlign="center">
          <Typography variant="h6" sx={{ fontWeight: 'bold', mb: 1 }}>
            Контакты
          </Typography>
          <Box sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center', mb: 1 }}>
            <Email sx={{ marginRight: 1 }} />
            <Link href="mailto:support@alumenator.net" color="inherit" underline="hover">
              support@alumenator.net
            </Link>
          </Box>
        </Grid>

        <Grid item xs={12} md={4} textAlign="center">
          <Typography variant="h6" sx={{ fontWeight: 'bold', mb: 2 }}>
            Мы в соцсетях
          </Typography>
          <Box>
            {/* Кнопка VK */}
            <Button
              color="inherit"
              startIcon={<VKIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
              href="https://vk.com/alumenator"
              sx={{
                textTransform: 'none',
                padding: '4px',
                minWidth: '32px',
                '& .MuiButton-startIcon': { margin: '0px' },
                background: 'transparent',
              }}
            ></Button>

            {/* Кнопка Telegram */}
            <Button
              color="inherit"
              startIcon={<TelegramIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
              href="https://t.me/AlumenatoR"
              sx={{
                textTransform: 'none',
                padding: '4px',
                minWidth: '32px',
                '& .MuiButton-startIcon': { margin: '0px' },
                background: 'transparent',
              }}
            ></Button>

            {/* Кнопка Discord */}
            <Button
              color="inherit"
              startIcon={<DiscordIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
              href="https://discord.gg/alumenator"
              sx={{
                textTransform: 'none',
                padding: '4px',
                minWidth: '32px',
                '& .MuiButton-startIcon': { margin: '0px' },
                background: 'transparent',
              }}
            ></Button>

            {/* Кнопка YouTube */}
            <Button
              color="inherit"
              startIcon={<YoutubeIcon style={{width: '32px', height: '32px', fill: '#fff'}}/>}
              href="https://www.youtube.com/@AlumenatoR"
              sx={{
                textTransform: 'none',
                padding: '4px',
                minWidth: '32px',
                '& .MuiButton-startIcon': { margin: '0px' },
                background: 'transparent',
              }}
            ></Button>
          </Box>
        </Grid>
      </Grid>

      {/* Нижняя часть футера */}
      <Box
        sx={{
          borderTop: '1px solid #444',
          paddingTop: '10px',
          marginTop: '10px',
          textAlign: 'center',
        }}
      >
        <Typography variant="body2" sx={{ mb: 1 }}>
          © {new Date().getFullYear()} Alumenator. Все права защищены.
        </Typography>
          <Link
            href="https://legal.easyx.ru/general/rules"
            target="_blank"
            rel="noopener noreferrer"
            color="inherit"
            underline="hover"
            sx={{ marginRight: 2 }}
          >
            Пользовательское соглашение
          </Link>
          <Link
            href="https://legal.easyx.ru/easydonate/terms-of-service"
            target="_blank"
            rel="noopener noreferrer"
            color="inherit"
            underline="hover"
          >
            Условия оказания услуг
          </Link>
      </Box>
    </Box>
  );
};

export default Footer;