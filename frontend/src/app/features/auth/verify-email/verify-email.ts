import { Component, inject, signal, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';

@Component({
  selector: 'app-verify-email',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './verify-email.html',
  styleUrl: './verify-email.scss',
})
export class VerifyEmailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private http = inject(HttpClient);

  status = signal<'loading' | 'success' | 'error'>('loading');
  message = signal('');

  ngOnInit(): void {
    const token = this.route.snapshot.queryParamMap.get('token');
    if (!token) {
      this.status.set('error');
      this.message.set('Lien de vérification invalide.');
      return;
    }
    this.http.get<void>(`${environment.apiUrl}/verify-email?token=${token}`).subscribe({
      next: () => {
        this.status.set('success');
        this.message.set('Votre adresse email a été vérifiée avec succès !');
      },
      error: (err) => {
        this.status.set('error');
        this.message.set(err?.error?.message ?? 'Lien invalide ou expiré.');
      },
    });
  }
}
