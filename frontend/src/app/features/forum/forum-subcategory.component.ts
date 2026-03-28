import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { ForumType, SubType } from '@core/models/product.model';

@Component({
  selector: 'app-forum-subcategory',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="max-w-4xl mx-auto">
      <a routerLink="/forum" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Retour au forum</a>

      @if (type(); as t) {
        <h1 class="text-3xl font-bold mb-2">{{ t.name }}</h1>
        <p class="text-gray-600 mb-8">{{ t.description }}</p>
      }

      @for (sub of subTypes(); track sub.id) {
        <div class="bg-white rounded-xl shadow-md p-6 mb-4 hover:shadow-lg transition">
          <h2 class="text-lg font-semibold mb-1">{{ sub.name }}</h2>
          <p class="text-gray-600 text-sm mb-3">{{ sub.description }}</p>
          <a [routerLink]="['/forum', sub.id, 'threads']" class="text-orange-500 hover:text-orange-600 text-sm">
            Voir les discussions →
          </a>
        </div>
      } @empty {
        <p class="text-gray-500 text-center py-8">Aucune sous-catégorie.</p>
      }
    </div>
  `,
})
export class ForumSubcategoryComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private forumService = inject(ForumService);
  type = signal<ForumType | null>(null);
  subTypes = signal<SubType[]>([]);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getTypeById(id).subscribe((t) => this.type.set(t));
    this.forumService.getSubTypes(id).subscribe((s) => this.subTypes.set(s));
  }
}
