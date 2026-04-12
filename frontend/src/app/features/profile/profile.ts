import { Component, inject, signal, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { AuthService } from '@core/services/auth.service';

@Component({
  selector: 'app-profile',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './profile.html',
})
export class ProfileComponent implements OnInit {
  authService = inject(AuthService);

  // Info form
  form = {
    firstName: '',
    lastName: '',
    telephone: '',
    gender: '',
    address: '',
    secondAddress: '',
    city: '',
    postalCode: '',
    country: '',
  };

  // Password form
  passwordForm = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
  };

  saving = signal(false);
  saveSuccess = signal(false);
  saveError = signal('');

  changingPassword = signal(false);
  passwordSuccess = signal(false);
  passwordError = signal('');

  ngOnInit() {
    const user = this.authService.user();
    if (user) {
      this.form.firstName = user.firstName ?? '';
      this.form.lastName = user.lastName ?? '';
      this.form.telephone = user.telephone ?? '';
      this.form.gender = user.gender ?? '';
      this.form.address = user.address ?? '';
      this.form.secondAddress = user.secondAddress ?? '';
      this.form.city = user.city ?? '';
      this.form.postalCode = user.postalCode ?? '';
      this.form.country = user.country ?? '';
    }
  }

  saveProfile() {
    this.saving.set(true);
    this.saveSuccess.set(false);
    this.saveError.set('');

    this.authService.updateProfile(this.form).subscribe({
      next: () => {
        this.saving.set(false);
        this.saveSuccess.set(true);
        setTimeout(() => this.saveSuccess.set(false), 3000);
      },
      error: () => {
        this.saving.set(false);
        this.saveError.set('Une erreur est survenue. Veuillez réessayer.');
      },
    });
  }

  changePassword() {
    this.passwordError.set('');
    if (this.passwordForm.newPassword !== this.passwordForm.confirmPassword) {
      this.passwordError.set('Les nouveaux mots de passe ne correspondent pas.');
      return;
    }
    if (this.passwordForm.newPassword.length < 6) {
      this.passwordError.set('Le mot de passe doit contenir au moins 6 caractères.');
      return;
    }

    this.changingPassword.set(true);
    this.authService
      .updateProfile({
        currentPassword: this.passwordForm.currentPassword,
        newPassword: this.passwordForm.newPassword,
      })
      .subscribe({
        next: () => {
          this.changingPassword.set(false);
          this.passwordSuccess.set(true);
          this.passwordForm = { currentPassword: '', newPassword: '', confirmPassword: '' };
          setTimeout(() => this.passwordSuccess.set(false), 3000);
        },
        error: (err) => {
          this.changingPassword.set(false);
          this.passwordError.set(err?.error?.error ?? 'Mot de passe actuel incorrect.');
        },
      });
  }
}
