import { Good } from "../types/Good";

type CartUpdateCallback = (items: Good[]) => void;

class CartService {
  private cart: Good[] = [];
  private subscribers: CartUpdateCallback[] = [];

  constructor() {
    this.loadCart(); // Загружаем корзину при инициализации
    if (typeof window !== 'undefined') {
      window.addEventListener('storage', this.handleStorageEvent);
    }
  }

  // Обработчик события изменения localStorage
  private handleStorageEvent = (event: StorageEvent) => {
    if (event.key === 'cart') {
      this.loadCart();
      this.notifySubscribers();
    }
  };
  
  // В деструкторе (если используете классы с жизненным циклом)
  // Не забудьте удалить обработчик при уничтожении сервиса
  destroy() {
    if (typeof window !== 'undefined') {
      window.removeEventListener('storage', this.handleStorageEvent);
    }
  }

  // Добавить товар в корзину
  addToCart(item: Good) {
    this.cart.push(item);
    this.updateLocalStorage();
    this.notifySubscribers(); // Уведомляем подписчиков
  }

  // Удалить товар из корзины по ID
  removeFromCart(itemId: number) {
    this.cart = this.cart.filter((item) => item.id !== itemId);
    this.updateLocalStorage();
    this.notifySubscribers(); // Уведомляем подписчиков
  }

  // Получить все товары в корзине
  getCartItems(): Good[] {
    return this.cart;
  }

  // Очистить корзину
  clearCart() {
    this.cart = [];
    this.updateLocalStorage();
    this.notifySubscribers(); // Уведомляем подписчиков
  }

  // Сохранить корзину в localStorage
  private updateLocalStorage() {
    if (typeof window !== 'undefined') { // Проверяем, что код выполняется в браузере
      localStorage.setItem('cart', JSON.stringify(this.cart));
      this.notifySubscribers();
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

  // Подписка на изменения корзины
  subscribe(callback: CartUpdateCallback) {
    this.subscribers.push(callback);
  }

  // Отписка от изменений
  unsubscribe(callback: CartUpdateCallback) {
    this.subscribers = this.subscribers.filter((sub) => sub !== callback);
  }

  // Уведомление подписчиков об изменениях
  private notifySubscribers() {
    this.subscribers.forEach((callback) => callback(this.cart));
  }
}

export const cartService = new CartService();