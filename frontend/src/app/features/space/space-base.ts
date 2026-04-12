import { Component, inject, signal, computed, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { CurrencyPipe } from '@angular/common';
import { AuthService } from '@core/services/auth.service';
import { CheckoutService, MyOrder } from '@core/services/checkout.service';

@Component({
  selector: 'app-space-base',
  standalone: true,
  imports: [RouterLink, CurrencyPipe],
  templateUrl: './space-base.html',
  styleUrl: './space-base.scss',
})
export class SpaceBaseComponent implements OnInit {
  authService = inject(AuthService);
  private checkoutService = inject(CheckoutService);

  orders = signal<MyOrder[]>([]);
  loading = signal(true);
  error = signal('');

  totalSpent = computed(() => this.orders().reduce((sum, o) => sum + (+o.total || 0), 0));

  readonly statusLabels: Record<string, string> = {
    pending: 'En attente',
    preparing: 'En préparation',
    shipped: 'Expédiée',
    refunded: 'Remboursée',
  };

  readonly statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
    preparing: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
    shipped: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
    refunded: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
  };

  ngOnInit() {
    this.checkoutService.getMyOrders().subscribe({
      next: (orders) => {
        this.orders.set(orders);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('Impossible de charger vos commandes.');
        this.loading.set(false);
      },
    });
  }
}
