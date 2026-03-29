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
  templateUrl: './register.html',
  styleUrl: './register.scss',
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
