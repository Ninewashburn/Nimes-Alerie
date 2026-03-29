import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ProductService } from '@core/services/product.service';
import { Product } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-admin-products',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  templateUrl: './admin-products.html',
  styleUrl: './admin-products.scss',
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
