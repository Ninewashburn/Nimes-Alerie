import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '@env/environment';
import { ApiCollection, Article } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class ArticleService {
  private readonly apiUrl = `${environment.apiUrl}/articles`;

  constructor(private http: HttpClient) {}

  getAll(): Observable<Article[]> {
    return this.http
      .get<ApiCollection<Article>>(this.apiUrl)
      .pipe(map((res) => res['hydra:member']));
  }

  getById(id: number): Observable<Article> {
    return this.http.get<Article>(`${this.apiUrl}/${id}`);
  }

  create(article: Partial<Article>): Observable<Article> {
    return this.http.post<Article>(this.apiUrl, article);
  }

  update(id: number, article: Partial<Article>): Observable<Article> {
    return this.http.patch<Article>(`${this.apiUrl}/${id}`, article, {
      headers: { 'Content-Type': 'application/merge-patch+json' },
    });
  }

  delete(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}`);
  }
}
