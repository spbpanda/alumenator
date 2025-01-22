import { ThemeProvider, CssBaseline } from '@mui/material'; // Импортируем CssBaseline
import React from 'react';
import { createRoot } from 'react-dom/client'; // Используем createRoot из react-dom/client
import { BrowserRouter } from 'react-router-dom';
import App from './App'; // Основной компонент приложения
import './index.scss'; // Глобальные стили (если нужны)
import theme from './theme'; // Кастомная тема MUI

// Error Boundary для отлавливания ошибок
class ErrorBoundary extends React.Component<{ children: React.ReactNode }, { hasError: boolean }> {
  constructor(props: { children: React.ReactNode }) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    console.error('Error caught by ErrorBoundary:', error, errorInfo);
  }

  render() {
    if (this.state.hasError) {
      return <div>Something went wrong. Please refresh the page.</div>;
    }
    return this.props.children;
  }
}

// Получаем корневой элемент
const container = document.getElementById('root');

// Проверяем, что корневой элемент существует
if (container) {
  const root = createRoot(container); // Создаем root

  // Рендерим приложение
  root.render(
    <React.StrictMode>
      <ThemeProvider theme={theme}>
        <CssBaseline /> {/* Сбрасываем стили браузера и применяем базовые стили MUI */}
        <BrowserRouter>
          <ErrorBoundary> {/* Обертываем приложение в ErrorBoundary */}
            <App /> {/* Основной компонент приложения */}
          </ErrorBoundary>
        </BrowserRouter>
      </ThemeProvider>
    </React.StrictMode>
  );
} else {
  console.error('Root element not found'); // Обработка ошибки, если корневой элемент не найден
}