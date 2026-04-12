import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@env/environment';
import { ApiCollection, Product, Brand, Category } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class ProductService {
  private readonly apiUrl = `${environment.apiUrl}/products`;

  constructor(private http: HttpClient) {}

  getAll(page = 1, itemsPerPage = 9): Observable<{ items: Product[]; total: number }> {
    return this.http
      .get<ApiCollection<Product>>(`${this.apiUrl}?page=${page}&itemsPerPage=${itemsPerPage}`)
      .pipe(map((res) => ({ items: res['hydra:member'], total: res['hydra:totalItems'] })));
  }

  getById(id: number): Observable<Product> {
    return this.http.get<Product>(`${this.apiUrl}/${id}`);
  }

  create(product: Partial<Product>): Observable<Product> {
    return this.http.post<Product>(this.apiUrl, product);
  }

  update(id: number, product: Partial<Product>): Observable<Product> {
    return this.http.patch<Product>(`${this.apiUrl}/${id}`, product, {
      headers: { 'Content-Type': 'application/merge-patch+json' },
    });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }

  getBrands(): Observable<Brand[]> {
    return this.http
      .get<ApiCollection<Brand>>(`${environment.apiUrl}/brands`)
      .pipe(map((res) => res['hydra:member']));
  }

  getCategories(): Observable<Category[]> {
    return this.http
      .get<ApiCollection<Category>>(`${environment.apiUrl}/categories`)
      .pipe(map((res) => res['hydra:member']));
  }
}
