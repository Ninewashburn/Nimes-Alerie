import { Component, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-contact',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './contact.html',
  styleUrl: './contact.scss',
})
export class ContactComponent {
  form = { email: '', subject: 'general', message: '' };
  sent = signal(false);
  sending = signal(false);

  onSubmit(): void {
    this.sending.set(true);
    setTimeout(() => {
      this.sending.set(false);
      this.sent.set(true);
      this.form = { email: '', subject: 'general', message: '' };
    }, 800);
  }
}
