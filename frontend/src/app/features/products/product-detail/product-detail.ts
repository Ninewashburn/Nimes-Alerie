import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductService } from '@core/services/product.service';
import { CartService } from '@core/services/cart.service';
import { AuthService } from '@core/services/auth.service';
import { Product, Rate } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

@Component({
  selector: 'app-product-detail',
  standalone: true,
  imports: [RouterLink, CurrencyPipe, FormsModule],
  templateUrl: './product-detail.html',
  styleUrl: './product-detail.scss',
})
export class ProductDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private productService = inject(ProductService);
  private cartService = inject(CartService);
  readonly authService = inject(AuthService);

  product = signal<Product | null>(null);
  ratings = signal<Rate[]>([]);

  // Rating form
  selectedStar = signal(0);
  hoveredStar = signal(0);
  testimonialInput = '';
  submitting = signal(false);
  submitSuccess = signal(false);
  submitError = signal('');

  readonly averageRating = computed(() => {
    const list = this.ratings();
    if (list.length === 0) return 0;
    return list.reduce((sum, r) => sum + r.rate, 0) / list.length;
  });

  readonly userHasRated = computed(() => {
    const me = this.authService.user();
    if (!me) return false;
    return this.ratings().some((r) => r.user?.id === me.id);
  });

  readonly starsArray = [1, 2, 3, 4, 5];

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.productService.getById(id).subscribe((product) => {
      this.product.set(product);
    });
    this.loadRatings(id);
  }

  loadRatings(productId: number): void {
    this.productService.getRates(productId).subscribe((rates) => {
      this.ratings.set(rates);
    });
  }

  addToCart(product: Product): void {
    this.cartService.addProduct(product);
  }

  setHovered(star: number): void {
    this.hoveredStar.set(star);
  }

  selectStar(star: number): void {
    this.selectedStar.set(star);
  }

  displayStar(star: number): 'full' | 'empty' {
    const active = this.hoveredStar() || this.selectedStar();
    return star <= active ? 'full' : 'empty';
  }

  submitRating(): void {
    if (this.selectedStar() === 0) {
      this.submitError.set('Veuillez sélectionner une note.');
      return;
    }
    const productId = this.product()?.id;
    if (!productId) return;

    this.submitting.set(true);
    this.submitError.set('');

    this.productService.postRate(productId, this.selectedStar(), this.testimonialInput).subscribe({
      next: (newRate) => {
        this.ratings.update((list) => [...list, newRate]);
        this.selectedStar.set(0);
        this.testimonialInput = '';
        this.submitting.set(false);
        this.submitSuccess.set(true);
        setTimeout(() => this.submitSuccess.set(false), 3000);
      },
      error: (err) => {
        this.submitError.set(err?.error?.['hydra:description'] ?? 'Une erreur est survenue.');
        this.submitting.set(false);
      },
    });
  }

  roundedAvg(): string {
    return this.averageRating().toFixed(1);
  }
}
