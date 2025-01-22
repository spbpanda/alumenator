import { createTheme } from "@mui/material/styles";

const theme = createTheme({
  typography: {
    fontFamily: "Comfortaa, Arial, sans-serif",
    allVariants: {
      color: "#fff",
    },
  },
  components: {
    MuiCssBaseline: {
      styleOverrides: {
        body: {
          fontFamily: "Comfortaa, Arial, sans-serif",
        },
      },
    },
    MuiButton: {
      styleOverrides: {
        root: {
          fontFamily: "Comfortaa, sans-serif",
          fontWeight: 700,
          borderRadius: "20px",
          padding: "10px 30px",
          color: '#fff', // Цвет текста
          backgroundColor: '#f5b759', // Цвет фона
          textTransform: 'none', // Отключаем автоматическое преобразование текста в верхний регистр
          '&:hover': {
            backgroundColor: '#e0a548', // Цвет фона при наведении
          },
          '&:active': {
            backgroundColor: '#d0933e', // Цвет фона при нажатии
          },
          '&.Mui-disabled': {
            backgroundColor: '#f5b75980', // Цвет фона для отключенной кнопки (с прозрачностью)
            color: '#ffffff80', // Цвет текста для отключенной кнопки (с прозрачностью)
          },
        },
      },
    },
    MuiPagination: {
      styleOverrides: {
        text: {
          fontSize: "1rem",
          fontWeight: 600,
        },
        root: {
          "& .MuiPaginationItem-root": {
            color: "#fff",
            backgroundColor: "#f5b75930",

            "&:hover": {
              backgroundColor: "#f5b75930",
            },
            "&.Mui-selected": {
              border: "2px solid #f5b759",
              color: "#f5b759",
              backgroundColor: "#f5b75930",
              "&:hover": {
                backgroundColor: "#f5b75930",
              },
            },
          },
        },
      },
    },
    MuiChip: {
      styleOverrides: {
        root: {
          backgroundColor: "#f5b75930",
          color: "#fff",
          "&:hover": {
            backgroundColor: "#e0a548",
          },
        },
      },
    },
    MuiCircularProgress: {
      styleOverrides: {
        root: {
          color: "#f5b759",
        },
      },
    },
    MuiCheckbox: {
      styleOverrides: {
        root: {
          color: "#f5b759",
          '&.Mui-checked': {
            color: '#f5b759', // Цвет, когда чекбокс активен
          },
        },
      },
    },
    MuiOutlinedInput: {
      styleOverrides: {
        root: {
          borderRadius: '20px', // Добавляем border-radius для поля ввода
          background: '#f5b75930',
          '& .MuiOutlinedInput-notchedOutline': {
            borderColor: '#f5b759', // Цвет рамки
          },
          '&:hover .MuiOutlinedInput-notchedOutline': {
            borderColor: '#e0a548', // Цвет рамки при наведении
          },
          '&.Mui-focused .MuiOutlinedInput-notchedOutline': {
            borderColor: '#f5b759', // Цвет рамки при фокусе
          },
        },
      },
    },
    MuiInputBase: {
      styleOverrides: {
        root: {
          // Стили для MuiInputBase
          color: '#fff', // Цвет текста
          '&.Mui-focused': {
            color: '#fff', // Цвет текста при фокусе
          },
        },
        input: {
          // Стили для input внутри MuiInputBase
          '&::placeholder': {
            color: '#f5b759', // Цвет плейсхолдера
            opacity: 0.7, // Прозрачность плейсхолдера
          },
        },
      },
    },
    MuiInputLabel: {
      styleOverrides: {
        root: {
          color: '#f5b759', // Цвет лейбла по умолчанию
          '&.Mui-focused': {
            color: '#f5b759', // Цвет лейбла при фокусе
          },
        },
      },
    },
    MuiLink: {
      styleOverrides: {
        root: {
          color: '#f5b759', // Основной цвет ссылки
          textDecoration: 'none', // Убираем подчеркивание
          '&:hover': {
            color: '#e0a548', // Цвет ссылки при наведении
            textDecoration: 'underline', // Подчеркивание при наведении
          },
        },
      },
    },
  },
});

export default theme;
