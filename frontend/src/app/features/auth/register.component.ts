import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [FormsModule, RouterLink],
  template: `
    <div class="max-w-lg mx-auto mt-12">
      <div class="bg-white rounded-xl shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6 text-center">Créer un compte</h1>

        @if (error()) {
          <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm">{{ error() }}</div>
        }

        <form (ngSubmit)="onSubmit()">
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
              <input type="text" [(ngModel)]="form.firstName" name="firstName" required
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
              <input type="text" [(ngModel)]="form.lastName" name="lastName" required
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" [(ngModel)]="form.email" name="email" required
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <input type="password" [(ngModel)]="form.password" name="password" required minlength="6"
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
            <input type="date" [(ngModel)]="form.birthAt" name="birthAt" required
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <input type="text" [(ngModel)]="form.address" name="address" required
              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
              <input type="text" [(ngModel)]="form.city" name="city" required
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
              <input type="tel" [(ngModel)]="form.telephone" name="telephone"
                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500" />
            </div>
          </div>

          <button type="submit" [disabled]="loading()"
            class="w-full bg-orange-500 hover:bg-orange-600 disabled:bg-gray-400 text-white py-3 rounded-lg font-semibold transition">
            @if (loading()) { Création en cours... } @else { Créer mon compte }
          </button>
        </form>

        <p class="text-center mt-4 text-sm text-gray-600">
          Déjà un compte ?
          <a routerLink="/login" class="text-orange-500 hover:text-orange-600">Se connecter</a>
        </p>
      </div>
    </div>
  `,
})
export class RegisterComponent {
  private http = inject(HttpClient);
  private authService = inject(AuthService);
  private router = inject(Router);

  form = {
    email: '', password: '', firstName: '', lastName: '',
    birthAt: '', address: '', city: '', telephone: '',
  };
  error = signal('');
  loading = signal(false);

  onSubmit(): void {
    this.loading.set(true);
    this.error.set('');

    this.http.post(`${environment.apiUrl}/register`, this.form).subscribe({
      next: () => {
        this.authService.login({ email: this.form.email, password: this.form.password }).subscribe({
          next: () => this.router.navigate(['/']),
          error: () => this.router.navigate(['/login']),
        });
      },
      error: () => {
        this.loading.set(false);
        this.error.set('Erreur lors de la création du compte.');
      },
    });
  }
}
