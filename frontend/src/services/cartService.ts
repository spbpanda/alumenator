import { Good } from "../types/Good";

class CartService {
  private cart: Good[] = [];

  constructor() {
    this.loadCart(); // Загружаем корзину при инициализации
  }

  // Добавить товар в корзину
  addToCart(item: Good) {
    this.cart.push(item);
    this.updateLocalStorage();
  }

  // Удалить товар из корзины по ID
  removeFromCart(itemId: number) {
    this.cart = this.cart.filter((item) => item.id !== itemId);
    this.updateLocalStorage();
  }

  // Получить все товары в корзине
  getCartItems(): Good[] {
    return this.cart;
  }

  // Очистить корзину
  clearCart() {
    this.cart = [];
    this.updateLocalStorage();
  }

  // Сохранить корзину в localStorage
  private updateLocalStorage() {
    if (typeof window !== 'undefined') { // Проверяем, что код выполняется в браузере
      localStorage.setItem('cart', JSON.stringify(this.cart));
    }
  }

  // Загрузить корзину из localStorage
  private loadCart() {
    if (typeof window !== 'undefined') { // Проверяем, что код выполняется в браузере
      const cart = localStorage.getItem('cart');
      if (cart) {
        this.cart = JSON.parse(cart);
      }
    }
  }
}

export const cartService = new CartService();