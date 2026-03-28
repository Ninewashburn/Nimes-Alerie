import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { Observable, tap } from 'rxjs';
import { environment } from '@env/environment';
import { JwtToken, LoginCredentials, User } from '@core/models/product.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly tokenKey = 'jwt_token';
  private readonly currentUser = signal<User | null>(null);
  private readonly isAuthenticated = signal(false);

  readonly user = this.currentUser.asReadonly();
  readonly loggedIn = this.isAuthenticated.asReadonly();
  readonly isAdmin = computed(() => {
    const user = this.currentUser();
    return user?.roles?.includes('ROLE_ADMIN') ?? false;
  });

  constructor(
    private http: HttpClient,
    private router: Router,
  ) {
    this.checkToken();
  }

  login(credentials: LoginCredentials): Observable<JwtToken> {
    return this.http
      .post<JwtToken>(environment.loginUrl, credentials)
      .pipe(tap((response) => this.setToken(response.token)));
  }

  logout(): void {
    localStorage.removeItem(this.tokenKey);
    this.currentUser.set(null);
    this.isAuthenticated.set(false);
    this.router.navigate(['/login']);
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  private setToken(token: string): void {
    localStorage.setItem(this.tokenKey, token);
    this.isAuthenticated.set(true);
    this.decodeAndSetUser(token);
  }

  private checkToken(): void {
    const token = this.getToken();
    if (token && !this.isTokenExpired(token)) {
      this.isAuthenticated.set(true);
      this.decodeAndSetUser(token);
    } else {
      this.logout();
    }
  }

  private decodeAndSetUser(token: string): void {
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      this.currentUser.set({
        id: payload.id ?? 0,
        email: payload.username ?? payload.email ?? '',
        firstName: payload.firstName ?? '',
        lastName: payload.lastName ?? '',
        roles: payload.roles ?? [],
        address: '',
        city: '',
        birthAt: '',
      });
    } catch {
      this.currentUser.set(null);
    }
  }

  private isTokenExpired(token: string): boolean {
    try {
      const payload = JSON.parse(atob(token.split('.')[1]));
      return payload.exp * 1000 < Date.now();
    } catch {
      return true;
    }
  }
}
