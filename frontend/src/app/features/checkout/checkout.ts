import { Component, inject, signal, computed, OnInit, effect } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CurrencyPipe } from '@angular/common';
import { loadScript } from '@paypal/paypal-js';
import { CartService } from '@core/services/cart.service';
import { CheckoutService } from '@core/services/checkout.service';
import { AuthService } from '@core/services/auth.service';
import { OrderConfirmation } from '@core/models/product.model';
import { environment } from '@env/environment';

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
  readonly paypalLoading = signal(false);
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

  // Track whether the PayPal button has been rendered in the current container
  private paypalRendered = false;

  constructor() {
    // Watch for when the user reaches step 2 with PayPal selected,
    // then mount the PayPal button once the container div is in the DOM.
    effect(() => {
      const isPayPalStep = this.step() === 2 && this.paymentMethod() === 'paypal';

      if (!isPayPalStep) {
        // Reset so the button is re-rendered if the user switches back to PayPal
        this.paypalRendered = false;
        return;
      }

      if (this.paypalRendered) return;

      // Give Angular one tick to render the container div before calling PayPal SDK
      setTimeout(() => this.renderPayPalButton(), 50);
    });
  }

  ngOnInit(): void {
    if (this.cartItems().length === 0) {
      this.router.navigate(['/cart']);
      return;
    }

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

  // ─── Card flow (unchanged) ───────────────────────────────────────────────
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
        paymentMethod: 'card',
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

  // ─── PayPal flow ─────────────────────────────────────────────────────────
  private async renderPayPalButton(): Promise<void> {
    const container = document.getElementById('paypal-button-container');
    if (!container || this.paypalRendered) return;

    this.paypalLoading.set(true);
    this.errorMessage.set(null);

    try {
      const paypal = await loadScript({
        clientId: environment.paypalClientId,
        currency: 'EUR',
        intent: 'capture',
      });

      if (!paypal?.Buttons) {
        throw new Error('PayPal SDK non disponible');
      }

      await paypal.Buttons({
        style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'pay' },

        // Step 1: backend creates the PayPal order and returns its ID
        createOrder: async () => {
          const token = this.authService.getToken();
          const res = await fetch(`${environment.apiUrl}/paypal/create-order`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({ amount: this.totalTTC() }),
          });

          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err['error'] ?? 'Impossible de créer la commande PayPal');
          }

          const data = await res.json();
          return data['paypalOrderId'] as string;
        },

        // Step 2: user approved — backend captures + creates local order
        onApprove: async (data: { orderID: string }) => {
          this.isLoading.set(true);
          this.errorMessage.set(null);

          const token = this.authService.getToken();
          const items = this.cartItems().map((item) => ({
            productId: item.product.id,
            title: item.product.title,
            quantity: item.quantity,
            priceTTC: item.product.priceTTC,
          }));

          const res = await fetch(`${environment.apiUrl}/paypal/capture-order`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify({
              paypalOrderId: data.orderID,
              items,
              deliveryAddress: this.deliveryAddress(),
              deliveryCity: this.deliveryCity(),
              deliveryPostalCode: this.deliveryPostalCode(),
              deliveryCountry: this.deliveryCountry(),
            }),
          });

          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            this.errorMessage.set(err['error'] ?? 'Erreur lors de la capture du paiement.');
            this.isLoading.set(false);
            return;
          }

          const order: OrderConfirmation = await res.json();
          this.confirmation.set(order);
          this.cartService.clearCart();
          this.step.set(3);
          this.isLoading.set(false);
        },

        onCancel: () => {
          this.errorMessage.set('Paiement annulé.');
        },

        onError: (err: unknown) => {
          console.error('PayPal error:', err);
          this.errorMessage.set('Une erreur PayPal est survenue. Veuillez réessayer.');
          this.isLoading.set(false);
        },
      }).render('#paypal-button-container');

      this.paypalRendered = true;
    } catch (err) {
      console.error('PayPal init error:', err);
      this.errorMessage.set('Impossible de charger PayPal. Vérifiez votre connexion.');
    } finally {
      this.paypalLoading.set(false);
    }
  }
}
