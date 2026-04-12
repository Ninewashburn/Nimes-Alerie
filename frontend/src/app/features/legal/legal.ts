import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

type LegalType = 'cgu' | 'cgv' | 'mentions';

@Component({
  selector: 'app-legal',
  standalone: true,
  imports: [],
  templateUrl: './legal.html',
})
export class LegalComponent implements OnInit {
  private route = inject(ActivatedRoute);
  type = signal<LegalType>('mentions');

  readonly titles: Record<LegalType, string> = {
    cgu: "Conditions Générales d'Utilisation",
    cgv: 'Conditions Générales de Vente',
    mentions: 'Mentions Légales',
  };

  ngOnInit() {
    this.type.set((this.route.snapshot.data['type'] as LegalType) ?? 'mentions');
  }
}
