import { Component, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [FormsModule, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.scss',
})
export class RegisterComponent {
  private authService = inject(AuthService);
  private router = inject(Router);

  form = {
    email: '',
    password: '',
    firstName: '',
    lastName: '',
    birthAt: '',
    address: '',
    city: '',
    telephone: '',
  };
  error = signal('');
  loading = signal(false);
  registered = signal(false);

  onSubmit(): void {
    this.loading.set(true);
    this.error.set('');

    this.authService.register(this.form).subscribe({
      next: () => {
        this.loading.set(false);
        this.registered.set(true);
      },
      error: (err) => {
        this.loading.set(false);
        const detail = err?.error?.detail ?? err?.error?.['hydra:description'];
        if (detail?.includes('email')) {
          this.error.set('Cette adresse email est déjà utilisée.');
        } else {
          this.error.set(detail ?? 'Erreur lors de la création du compte.');
        }
      },
    });
  }
}
