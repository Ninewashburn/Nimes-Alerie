import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ProductService } from '@core/services/product.service';
import { CartService } from '@core/services/cart.service';
import { Product } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  template: `
    @if (product(); as p) {
      <div class="max-w-4xl mx-auto">
        <a routerLink="/products" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Retour à la boutique</a>

        <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col md:flex-row">
          @if (p.image || p.cover) {
            <img [src]="p.image || p.cover" [alt]="p.title" class="w-full md:w-1/2 h-80 object-cover" />
          } @else {
            <div class="w-full md:w-1/2 h-80 bg-gray-200 flex items-center justify-center text-gray-400">
              Pas d'image
            </div>
          }
          <div class="p-8 flex-1">
            <h1 class="text-3xl font-bold mb-4">{{ p.title }}</h1>
            <p class="text-gray-600 mb-6">{{ p.description }}</p>

            <div class="mb-6">
              <p class="text-2xl font-bold text-orange-500">{{ +p.price | currency: 'EUR' }} HT</p>
              <p class="text-lg text-gray-500">{{ +p.price * 1.2 | currency: 'EUR' }} TTC</p>
            </div>

            <p class="text-sm text-gray-500 mb-6">En stock: {{ p.quantity }}</p>

            <div class="flex gap-4">
              <button (click)="addToCart(p)" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                Ajouter au panier
              </button>
              <a routerLink="/cart" class="border border-gray-300 hover:border-orange-500 px-6 py-3 rounded-lg transition">
                Voir le panier
              </a>
            </div>
          </div>
        </div>
      </div>
    } @else {
      <div class="text-center py-12">
        <p class="text-gray-500">Chargement du produit...</p>
      </div>
    }
  `,
})
export class ProductDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private productService = inject(ProductService);
  private cartService = inject(CartService);

  product = signal<Product | null>(null);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.productService.getById(id).subscribe((product) => {
      this.product.set(product);
    });
  }

  addToCart(product: Product): void {
    this.cartService.addProduct(product);
  }
}
