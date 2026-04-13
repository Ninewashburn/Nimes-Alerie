import { Component, signal, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ContactService } from '@core/services/contact.service';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './contact.html',
  styleUrl: './contact.scss',
})
export class ContactComponent {
  private contactService = inject(ContactService);

  form = { email: '', subject: 'general', message: '' };
  sent = signal(false);
  sending = signal(false);
  error = signal<string | null>(null);

  onSubmit(): void {
    this.sending.set(true);
    this.error.set(null);

    this.contactService.send(this.form).subscribe({
      next: () => {
        this.sending.set(false);
        this.sent.set(true);
        this.form = { email: '', subject: 'general', message: '' };
      },
      error: (err) => {
        this.sending.set(false);
        const msg = err?.error?.error ?? 'Une erreur est survenue. Veuillez réessayer.';
        this.error.set(msg);
      },
    });
  }
}
