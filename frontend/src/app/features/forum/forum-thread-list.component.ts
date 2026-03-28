import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { Thread } from '@core/models/product.model';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'app-forum-thread-list',
  standalone: true,
  imports: [RouterLink, DatePipe],
  template: `
    <div class="max-w-4xl mx-auto">
      <a routerLink="/forum" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Retour au forum</a>
      <h1 class="text-3xl font-bold mb-8">Discussions</h1>

      @for (thread of threads(); track thread.id) {
        <div class="bg-white rounded-xl shadow-md p-4 mb-3 hover:shadow-lg transition flex justify-between items-center">
          <div>
            <a [routerLink]="['/forum/thread', thread.id]" class="text-lg font-semibold hover:text-orange-500">
              {{ thread.subject }}
            </a>
            <p class="text-sm text-gray-500">{{ thread.createdAt | date: 'dd/MM/yyyy HH:mm' }}</p>
          </div>
        </div>
      } @empty {
        <p class="text-gray-500 text-center py-8">Aucune discussion.</p>
      }
    </div>
  `,
})
export class ForumThreadListComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private forumService = inject(ForumService);
  threads = signal<Thread[]>([]);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getThreads(id).subscribe((t) => this.threads.set(t));
  }
}
