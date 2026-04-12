import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '@env/environment';
import { CheckoutRequest, OrderConfirmation } from '@core/models/product.model';

export interface MyOrder {
  id: number;
  status: string;
  total: string;
  items: { title: string; quantity: number; priceTTC: string }[];
  createdAt: string;
  billNumber: string;
  payment: string;
}

@Injectable({ providedIn: 'root' })
export class CheckoutService {
  private readonly http = inject(HttpClient);

  placeOrder(payload: CheckoutRequest): Observable<OrderConfirmation> {
    return this.http.post<OrderConfirmation>(`${environment.apiUrl}/orders`, payload);
  }

  getMyOrders(): Observable<MyOrder[]> {
    return this.http.get<MyOrder[]>(`${environment.apiUrl}/my-orders`);
  }
}
