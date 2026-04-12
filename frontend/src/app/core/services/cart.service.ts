import { Injectable, signal, computed } from '@angular/core';
import { Product, CartItem } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class CartService {
  private readonly storageKey = 'cart';
  private readonly items = signal<CartItem[]>(this.loadCart());

  readonly cartItems = this.items.asReadonly();
  readonly itemCount = computed(() => this.items().reduce((sum, item) => sum + item.quantity, 0));
  readonly totalHT = computed(() =>
    this.items().reduce((sum, item) => sum + parseFloat(item.product.priceHT) * item.quantity, 0),
  );
  readonly totalTTC = computed(() =>
    this.items().reduce((sum, item) => sum + parseFloat(item.product.priceTTC) * item.quantity, 0),
  );

  addProduct(product: Product): void {
    const current = this.items();
    const existing = current.find((item) => item.product.id === product.id);

    if (existing) {
      this.items.set(
        current.map((item) =>
          item.product.id === product.id ? { ...item, quantity: item.quantity + 1 } : item,
        ),
      );
    } else {
      this.items.set([...current, { product, quantity: 1 }]);
    }

    this.saveCart();
  }

  removeOne(productId: number): void {
    const current = this.items();
    const existing = current.find((item) => item.product.id === productId);

    if (existing && existing.quantity > 1) {
      this.items.set(
        current.map((item) =>
          item.product.id === productId ? { ...item, quantity: item.quantity - 1 } : item,
        ),
      );
    } else {
      this.items.set(current.filter((item) => item.product.id !== productId));
    }

    this.saveCart();
  }

  removeProduct(productId: number): void {
    this.items.set(this.items().filter((item) => item.product.id !== productId));
    this.saveCart();
  }

  clearCart(): void {
    this.items.set([]);
    this.saveCart();
  }

  private saveCart(): void {
    localStorage.setItem(this.storageKey, JSON.stringify(this.items()));
  }

  private loadCart(): CartItem[] {
    try {
      const data = localStorage.getItem(this.storageKey);
      return data ? JSON.parse(data) : [];
    } catch {
      return [];
    }
  }
}
