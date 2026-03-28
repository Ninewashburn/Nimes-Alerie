import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ProductService } from '@core/services/product.service';
import { Product } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-admin-products',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  template: `
    <div class="max-w-6xl mx-auto">
      <div class="flex justify-between items-center mb-8">
        <div>
          <a routerLink="/admin" class="text-orange-500 hover:text-orange-600 text-sm">← Admin</a>
          <h1 class="text-3xl font-bold">Gestion des produits</h1>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left py-3 px-4">Titre</th>
              <th class="text-right py-3 px-4">Prix</th>
              <th class="text-right py-3 px-4">Stock</th>
              <th class="text-right py-3 px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            @for (product of products(); track product.id) {
              <tr class="border-t hover:bg-gray-50">
                <td class="py-3 px-4">{{ product.title }}</td>
                <td class="py-3 px-4 text-right">{{ +product.price | currency: 'EUR' }}</td>
                <td class="py-3 px-4 text-right">{{ product.quantity }}</td>
                <td class="py-3 px-4 text-right">
                  <button (click)="deleteProduct(product.id)" class="text-red-500 hover:text-red-700 text-sm">
                    Supprimer
                  </button>
                </td>
              </tr>
            }
          </tbody>
        </table>
      </div>
    </div>
  `,
})
export class AdminProductsComponent implements OnInit {
  private productService = inject(ProductService);
  products = signal<Product[]>([]);

  ngOnInit(): void {
    this.loadProducts();
  }

  deleteProduct(id: number): void {
    if (confirm('Supprimer ce produit ?')) {
      this.productService.delete(id).subscribe(() => this.loadProducts());
    }
  }

  private loadProducts(): void {
    this.productService.getAll().subscribe((p) => this.products.set(p));
  }
}
