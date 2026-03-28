import { Component, inject } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CartService } from '@core/services/cart.service';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-cart',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  template: `
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold mb-8">Votre Panier</h1>

      @if (cartService.cartItems().length === 0) {
        <div class="text-center py-12 bg-white rounded-xl shadow-md">
          <p class="text-gray-500 text-lg mb-4">Votre panier est vide</p>
          <a routerLink="/products" class="text-orange-500 hover:text-orange-600">Parcourir la boutique →</a>
        </div>
      } @else {
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="text-left py-3 px-4">Produit</th>
                <th class="text-center py-3 px-4">Quantité</th>
                <th class="text-right py-3 px-4">Prix HT</th>
                <th class="text-right py-3 px-4">Actions</th>
              </tr>
            </thead>
            <tbody>
              @for (item of cartService.cartItems(); track item.product.id) {
                <tr class="border-t">
                  <td class="py-3 px-4">{{ item.product.title }}</td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex items-center justify-center gap-2">
                      <button (click)="cartService.removeOne(item.product.id)" class="w-8 h-8 rounded border hover:bg-gray-100">-</button>
                      <span class="font-semibold">{{ item.quantity }}</span>
                      <button (click)="cartService.addProduct(item.product)" class="w-8 h-8 rounded border hover:bg-gray-100">+</button>
                    </div>
                  </td>
                  <td class="py-3 px-4 text-right">{{ +item.product.price * item.quantity | currency: 'EUR' }}</td>
                  <td class="py-3 px-4 text-right">
                    <button (click)="cartService.removeProduct(item.product.id)" class="text-red-500 hover:text-red-700">
                      Supprimer
                    </button>
                  </td>
                </tr>
              }
            </tbody>
          </table>

          <div class="border-t p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-2">
              <span class="font-semibold">Total HT:</span>
              <span class="text-lg">{{ cartService.totalHT() | currency: 'EUR' }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="font-semibold">TVA (20%):</span>
              <span>{{ cartService.totalTTC() - cartService.totalHT() | currency: 'EUR' }}</span>
            </div>
            <div class="flex justify-between items-center text-xl font-bold text-orange-500">
              <span>Total TTC:</span>
              <span>{{ cartService.totalTTC() | currency: 'EUR' }}</span>
            </div>
          </div>

          <div class="p-4 flex justify-between">
            <button (click)="cartService.clearCart()" class="text-red-500 hover:text-red-700">
              Vider le panier
            </button>
            <a routerLink="/products" class="text-orange-500 hover:text-orange-600">
              Continuer les achats →
            </a>
          </div>
        </div>
      }
    </div>
  `,
})
export class CartComponent {
  cartService = inject(CartService);
}
