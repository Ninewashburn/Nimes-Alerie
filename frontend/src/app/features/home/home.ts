import { Component, inject, signal, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CurrencyPipe } from '@angular/common';
import { ProductService } from '@core/services/product.service';
import { CartService } from '@core/services/cart.service';
import { Product } from '@core/models/product.model';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  templateUrl: './home.html',
  styleUrl: './home.scss',
})
export class HomeComponent implements OnInit {
  private productService = inject(ProductService);
  private cartService = inject(CartService);

  featuredProducts = signal<Product[]>([]);

  ngOnInit() {
    this.productService.getAll(1, 3).subscribe(({ items }) => {
      this.featuredProducts.set(items);
    });
  }

  addToCart(product: Product) {
    this.cartService.addProduct(product);
  }
}
