import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@env/environment';
import { Order, ApiCollection } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class OrderService {
  private readonly apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getMyOrders(): Observable<Order[]> {
    return this.http.get<Order[]>(`${this.apiUrl}/my-orders`);
  }

  getAllOrders(page = 1, itemsPerPage = 20): Observable<{ items: any[]; total: number }> {
    return this.http
      .get<ApiCollection<any>>(
        `${this.apiUrl}/admin/orders?page=${page}&itemsPerPage=${itemsPerPage}`,
      )
      .pipe(map((res) => ({ items: res['hydra:member'], total: res['hydra:totalItems'] })));
  }

  updateStatus(id: number, status: string): Observable<any> {
    return this.http.patch<any>(
      `${this.apiUrl}/admin/orders/${id}/status`,
      { status },
      { headers: { 'Content-Type': 'application/merge-patch+json' } },
    );
  }
}
