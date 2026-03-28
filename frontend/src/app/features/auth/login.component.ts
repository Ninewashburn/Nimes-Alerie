import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule, RouterLink],
  template: `
    <div class="max-w-md mx-auto mt-12">
      <div class="bg-white rounded-xl shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Connexion</h1>

        @if (error()) {
          <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm">
            {{ error() }}
          </div>
        }

        <form (ngSubmit)="onSubmit()">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              id="email"
              type="email"
              [(ngModel)]="email"
              name="email"
              required
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
              placeholder="votre@email.fr"
            />
          </div>

          <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <input
              id="password"
              type="password"
              [(ngModel)]="password"
              name="password"
              required
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500"
            />
          </div>

          <button
            type="submit"
            [disabled]="loading()"
            class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-400 text-white py-3 rounded-lg font-semibold transition"
          >
            @if (loading()) { Connexion en cours... } @else { Se connecter }
          </button>
        </form>

        <p class="text-center mt-4 text-sm text-gray-600">
          Pas encore de compte ?
          <a routerLink="/register" class="text-orange-500 hover:text-orange-600">Créer un compte</a>
        </p>
      </div>
    </div>
  `,
})
export class LoginComponent {
  private authService = inject(AuthService);
  private router = inject(Router);

  email = '';
  password = '';
  error = signal('');
  loading = signal(false);

  onSubmit(): void {
    this.loading.set(true);
    this.error.set('');

    this.authService.login({ email: this.email, password: this.password }).subscribe({
      next: () => {
        this.loading.set(false);
        this.router.navigate(['/']);
      },
      error: () => {
        this.loading.set(false);
        this.error.set('Email ou mot de passe incorrect.');
      },
    });
  }
}
