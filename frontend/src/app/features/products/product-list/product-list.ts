import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductService } from '@core/services/product.service';
import { CartService } from '@core/services/cart.service';
import { Product, Category } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-product-list',
  standalone: true,
  imports: [RouterLink, CurrencyPipe, FormsModule],
  templateUrl: './product-list.html',
  styleUrl: './product-list.scss',
})
export class ProductListComponent implements OnInit {
  private productService = inject(ProductService);
  private cartService = inject(CartService);

  products = signal<Product[]>([]);
  categories = signal<Category[]>([]);
  loading = signal(true);
  loadingMore = signal(false);
  currentPage = 1;
  totalProducts = 0;
  readonly pageSize = 9;

  search = signal('');
  selectedCategoryId = signal<number | null>(null);
  maxPrice = signal(1000);
  maxProductPrice = signal(1000);

  filteredProducts = computed(() => {
    const s = this.search().toLowerCase().trim();
    const catId = this.selectedCategoryId();
    const max = this.maxPrice();

    return this.products().filter((p) => {
      const matchSearch = !s || p.title.toLowerCase().includes(s);
      const matchCat = catId === null || this.productHasCategory(p, catId);
      const matchPrice = +p.priceTTC <= max;
      return matchSearch && matchCat && matchPrice;
    });
  });

  get hasMore(): boolean {
    return this.products().length < this.totalProducts;
  }

  ngOnInit(): void {
    this.productService.getAll(1, this.pageSize).subscribe({
      next: ({ items, total }) => {
        this.products.set(items);
        this.totalProducts = total;
        const prices = items.map((p) => +p.priceTTC).filter((p) => !isNaN(p));
        const max = prices.length ? Math.ceil(Math.max(...prices)) : 1000;
        this.maxProductPrice.set(max);
        this.maxPrice.set(max);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });

    this.productService.getCategories().subscribe({
      next: (cats) => this.categories.set(cats),
    });
  }

  productHasCategory(product: Product, categoryId: number): boolean {
    return (
      product.category?.some((c) => {
        if (typeof c === 'string') return (c as string).endsWith(`/${categoryId}`);
        return (c as Category).id === categoryId;
      }) ?? false
    );
  }

  getCategoryName(product: Product): string {
    const cats = this.categories();
    if (!product.category?.length) return 'Sans catégorie';
    const first = product.category[0];
    if (typeof first === 'string') {
      const id = +(first as string).split('/').pop()!;
      return cats.find((c) => c.id === id)?.title ?? 'Sans catégorie';
    }
    return (first as Category).title ?? 'Sans catégorie';
  }

  selectedCategoryTitle = computed(() => {
    const id = this.selectedCategoryId();
    if (id === null) return null;
    return this.categories().find((c) => c.id === id)?.title ?? null;
  });

  setCategory(id: number | null): void {
    this.selectedCategoryId.set(id);
  }

  loadMore(): void {
    if (this.loadingMore() || !this.hasMore) return;
    this.loadingMore.set(true);
    this.currentPage++;
    this.productService.getAll(this.currentPage, this.pageSize).subscribe({
      next: ({ items }) => {
        this.products.set([...this.products(), ...items]);
        this.loadingMore.set(false);
      },
      error: () => this.loadingMore.set(false),
    });
  }

  resetFilters(): void {
    this.search.set('');
    this.selectedCategoryId.set(null);
  }

  addToCart(product: Product): void {
    this.cartService.addProduct(product);
  }
}
