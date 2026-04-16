import { Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-forgot-password',
  standalone: true,
  imports: [FormsModule, RouterLink],
  templateUrl: './forgot-password.html',
  styleUrl: './forgot-password.scss',
})
export class ForgotPasswordComponent {
  private authService = inject(AuthService);

  email = '';
  loading = signal(false);
  sent = signal(false);
  error = signal('');

  onSubmit(): void {
    if (!this.email.trim()) return;
    this.loading.set(true);
    this.error.set('');
    this.authService.forgotPassword(this.email).subscribe({
      next: () => {
        this.loading.set(false);
        this.sent.set(true);
      },
      error: () => {
        this.loading.set(false);
        this.sent.set(true); // always show success to avoid email enumeration
      },
    });
  }
}
