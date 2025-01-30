import React, { useEffect, useState } from 'react';
import { Box, CircularProgress, Grid, Typography } from '@mui/material';
import GoodsItem from './GoodsItem';
import { Good } from '../../types/Good';
import axios from 'axios';


const ServerInfo: React.FC = ({ }) => {
    const [serverInfo, setServerInfo] = useState<any>(null);
    const [serverLoading, setServerLoading] = useState<boolean>(false);
    const [serverError, setServerError] = useState<string | null>(null);

    const fetchServerInfo = async (address: string) => {
        setServerLoading(true);
        setServerError(null);

        try {
            const response = await axios.get(`https://api.mcstatus.io/v2/status/java/${address}`);
            setServerInfo(response.data);
        } catch (error) {
            console.error('Ошибка при получении информации о сервере:', error);
            setServerError('Не удалось загрузить информацию о сервере.');
        } finally {
            setServerLoading(false);
        }
    };

    useEffect(() => {
        const address = 'play.alumenator.net:25565'; // Замените на адрес выбранного сервера
        fetchServerInfo(address);
    }, []);

    return (
        <Box sx={{ marginTop: 1 }}>
            {serverLoading ? (
                <CircularProgress />
            ) : serverError ? (
                <Typography color="error">{serverError}</Typography>
            ) : serverInfo ? (
                <Box sx={{display: 'flex', flexDirection: 'row', alignItems: 'center', gap: 1, justifyContent: 'center'}}>
                    <Box
                        sx={{
                            width: 18,
                            height: 18,
                            borderRadius: '50%',
                            boxShadow: serverInfo.online
                                ? '0 0 10px 2px green' // Зеленое свечение
                                : '0 0 10px 2px red',  // Красное свечение
                            backgroundColor: serverInfo.online ? 'green' : 'red', // Цвет зависит от статуса
                        }}
                    />
                    {serverInfo.players ? ((
                        <Typography variant="h6">
                            Онлайн{' '}
                            <Box component="span" sx={{ color: '#f5b759', fontWeight: 'bold', fontSize: 24 }}>
                                {serverInfo.players.online}
                            </Box>{' '}
                            из{' '}
                            <Box component="span" sx={{ color: '#f5b759', fontWeight: 'bold', fontSize: 24 }}>
                                {serverInfo.players.max}
                            </Box>
                        </Typography>
                    )) : ((
                        <Typography variant="h6">Онлайн: 0</Typography>
                    ))}
                </Box>
            ) : (
                <Typography>Информация о сервере отсутствует.</Typography>
            )}
        </Box>
    );
};

export default ServerInfo;