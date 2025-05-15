/**
 * Config
 * -------------------------------------------------------------------------------------
 * ! IMPORTANT: Make sure you clear the browser local storage In order to see the config changes in the template.
 * ! To clear local storage: (https://www.leadshook.com/help/how-to-clear-local-storage-in-google-chrome-browser/).
 */

'use strict';

// JS global variables
let config = {
  colors: {
    primary: '#fe6c00',
    secondary: '#8592a3',
    success: '#71dd37',
    info: '#03c3ec',
    warning: '#ffab00',
    danger: '#ff3e1d',
    dark: '#233446',
    black: '#000',
    white: '#fff',
    cardColor: '#fff',
    bodyBg: '#f5f5f9',
    bodyColor: '#697a8d',
    headingColor: '#566a7f',
    textMuted: '#a1acb8',
    borderColor: '#eceef1'
  },
  colors_label: {
    primary: '#353535',
    secondary: '#8897aa1a',
    success: '#28d0941a',
    info: '#1e9ff21a',
    warning: '#ff91491a',
    danger: '#ff49611a',
    dark: '#181c211a'
  },
  colors_dark: {
    cardColor: '#252525',
    bodyBg: '#1A1A1A',
    bodyColor: '#ccafa3',
    headingColor: '#e2d5cb',
    textMuted: '#818181',
    borderColor: '#464646'
  },
  enableMenuLocalStorage: true // Enable menu state with local storage support
};

let assetsPath = document.documentElement.getAttribute('data-assets-path'),
  baseUrl = document.documentElement.getAttribute('data-base-url') + '/',
  templateName = document.documentElement.getAttribute('data-template'),
  rtlSupport = true; // set true for rtl support (rtl + ltr), false for ltr only.
