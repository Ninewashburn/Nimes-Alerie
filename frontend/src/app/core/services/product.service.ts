import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@env/environment';
import { ApiCollection, Product, Brand, Category, Rate } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class ProductService {
  private readonly apiUrl = `${environment.apiUrl}/products`;

  constructor(private http: HttpClient) {}

  getAll(page = 1, itemsPerPage = 9, search = ''): Observable<{ items: Product[]; total: number }> {
    const searchParam = search ? `&title=${encodeURIComponent(search)}` : '';
    return this.http
      .get<ApiCollection<Product>>(`${this.apiUrl}?page=${page}&itemsPerPage=${itemsPerPage}${searchParam}`)
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

  uploadImage(id: number, file: File): Observable<Product> {
    const formData = new FormData();
    formData.append('image', file);
    return this.http.post<Product>(`${this.apiUrl}/${id}/image`, formData);
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

  getRates(productId: number): Observable<Rate[]> {
    const iri = encodeURIComponent(`/api/products/${productId}`);
    return this.http
      .get<ApiCollection<Rate>>(`${environment.apiUrl}/rates?product=${iri}`)
      .pipe(map((res) => res['hydra:member']));
  }

  postRate(productId: number, rate: number, testimonial: string): Observable<Rate> {
    return this.http.post<Rate>(`${environment.apiUrl}/rates`, {
      rate,
      testimonial: testimonial || null,
      product: `/api/products/${productId}`,
    });
  }
}
