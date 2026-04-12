import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CurrencyPipe } from '@angular/common';
import { CartService } from '@core/services/cart.service';
import { CheckoutService } from '@core/services/checkout.service';
import { AuthService } from '@core/services/auth.service';
import { OrderConfirmation } from '@core/models/product.model';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [FormsModule, CurrencyPipe, RouterLink],
  templateUrl: './checkout.html',
  styleUrl: './checkout.scss',
})
export class CheckoutComponent implements OnInit {
  private readonly cartService = inject(CartService);
  private readonly checkoutSvc = inject(CheckoutService);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  // Steps : 1 = adresse, 2 = paiement, 3 = confirmation
  readonly step = signal<1 | 2 | 3>(1);

  // Delivery form
  readonly deliveryAddress = signal('');
  readonly deliveryCity = signal('');
  readonly deliveryPostalCode = signal('');
  readonly deliveryCountry = signal('France');

  // Payment
  readonly paymentMethod = signal<'card' | 'paypal'>('card');

  // State
  readonly isLoading = signal(false);
  readonly errorMessage = signal<string | null>(null);
  readonly confirmation = signal<OrderConfirmation | null>(null);

  // Computed from cart
  readonly cartItems = this.cartService.cartItems;
  readonly totalTTC = this.cartService.totalTTC;
  readonly totalHT = this.cartService.totalHT;
  readonly itemCount = this.cartService.itemCount;

  readonly isAddressValid = computed(
    () =>
      this.deliveryAddress().trim().length > 3 &&
      this.deliveryCity().trim().length > 1 &&
      this.deliveryPostalCode().trim().length >= 4,
  );

  ngOnInit(): void {
    if (this.cartItems().length === 0) {
      this.router.navigate(['/cart']);
      return;
    }

    // Pre-fill with user profile
    const user = this.authService.user();
    if (user) {
      this.deliveryAddress.set(user.address ?? '');
      this.deliveryCity.set(user.city ?? '');
      this.deliveryPostalCode.set(user.postalCode ?? '');
      this.deliveryCountry.set(user.country ?? 'France');
    }
  }

  goToPayment(): void {
    if (this.isAddressValid()) {
      this.step.set(2);
      this.errorMessage.set(null);
    }
  }

  goToAddress(): void {
    this.step.set(1);
  }

  placeOrder(): void {
    this.isLoading.set(true);
    this.errorMessage.set(null);

    const items = this.cartItems().map((item) => ({
      productId: item.product.id,
      title: item.product.title,
      quantity: item.quantity,
      priceTTC: item.product.priceTTC,
    }));

    this.checkoutSvc
      .placeOrder({
        items,
        deliveryAddress: this.deliveryAddress(),
        deliveryCity: this.deliveryCity(),
        deliveryPostalCode: this.deliveryPostalCode(),
        deliveryCountry: this.deliveryCountry(),
        paymentMethod: this.paymentMethod(),
        total: this.totalTTC(),
      })
      .subscribe({
        next: (order) => {
          this.confirmation.set(order);
          this.cartService.clearCart();
          this.step.set(3);
          this.isLoading.set(false);
        },
        error: (err) => {
          this.errorMessage.set(
            err?.error?.error ?? 'Une erreur est survenue. Veuillez réessayer.',
          );
          this.isLoading.set(false);
        },
      });
  }
}
