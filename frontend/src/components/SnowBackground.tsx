import React, { useEffect, useRef } from 'react';
import { Box } from '@mui/material';

const SnowBackground: React.FC = () => {
  const snowContainerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const snowContainer = snowContainerRef.current;
    if (!snowContainer) return;

    const totalSnowflakes = 200; // Количество снежинок

    for (let i = 0; i < totalSnowflakes; i++) {
      const randomX = Math.random() * 100; // Случайная позиция по X
      const randomOffset = (Math.random() * 200 - 100) / 100; // Случайное смещение
      const randomScale = Math.random(); // Случайный масштаб
      const fallDuration = Math.random() * 20 + 10; // Длительность падения
      const fallDelay = Math.random() * -30; // Задержка начала анимации

      const snowflake = document.createElement('div');
      snowflake.style.position = 'absolute';
      snowflake.style.width = '10px';
      snowflake.style.height = '10px';
      snowflake.style.backgroundColor = 'white';
      snowflake.style.borderRadius = '50%';
      snowflake.style.opacity = `${Math.random()}`;
      snowflake.style.transform = `translate(${randomX}vw, -10px) scale(${randomScale})`;
      snowflake.style.animation = `fall-${i} ${fallDuration}s ${fallDelay}s linear infinite`;

      // Динамическое создание keyframes
      const styleSheet = document.styleSheets[0];
      const keyframes = `
        @keyframes fall-${i} {
          0% {
            transform: translate(${randomX + randomOffset}vw, -10px) scale(${randomScale});
          }
          100% {
            transform: translate(${randomX + randomOffset / 2}vw, 100vh) scale(${randomScale});
          }
        }
      `;
      styleSheet.insertRule(keyframes, styleSheet.cssRules.length);

      snowContainer.appendChild(snowflake);
    }

    // Удаление снежинок при размонтировании
    return () => {
      snowContainer.innerHTML = '';
    };
  }, []);

  return (
    <Box
      ref={snowContainerRef}
      sx={{
        position: 'fixed', // Используем fixed, чтобы SnowBackground был на весь экран
        top: 0,
        left: 0,
        width: '100vw', // Ширина на весь экран
        height: '100vh', // Высота на весь экран
        overflow: 'hidden', // Избегаем прокрутки
        zIndex: 0, // Убедитесь, что SnowBackground находится под другими элементами
        pointerEvents: 'none', // Чтобы SnowBackground не перехватывал клики
      }}
    />
  );
};

export default SnowBackground;