import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { AuthService } from '@core/services/auth.service';
import { Post, Thread } from '@core/models/product.model';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-forum-thread',
  standalone: true,
  imports: [RouterLink, DatePipe, FormsModule],
  template: `
    <div class="max-w-4xl mx-auto">
      <a routerLink="/forum" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Retour au forum</a>

      @if (thread(); as t) {
        <h1 class="text-2xl font-bold mb-6">{{ t.subject }}</h1>
      }

      @for (post of posts(); track post.id) {
        <div class="bg-white rounded-xl shadow-md p-4 mb-3">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-semibold text-gray-700">{{ post.user?.email }}</p>
              <p class="text-xs text-gray-400">{{ post.createdAt | date: 'dd/MM/yyyy HH:mm' }}</p>
            </div>
            <div class="flex gap-2 text-sm">
              <span class="text-green-600">▲ {{ post.upVote }}</span>
              <span class="text-red-600">▼ {{ post.downVote }}</span>
            </div>
          </div>
          <p class="mt-3 text-gray-700 whitespace-pre-wrap">{{ post.content }}</p>
        </div>
      } @empty {
        <p class="text-gray-500 text-center py-8">Aucun message dans cette discussion.</p>
      }

      @if (authService.loggedIn()) {
        <div class="bg-white rounded-xl shadow-md p-4 mt-4">
          <h3 class="font-semibold mb-2">Répondre</h3>
          <textarea [(ngModel)]="newPostContent" rows="3" placeholder="Votre message..."
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500 mb-2"></textarea>
          <button (click)="submitPost()" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition">
            Publier
          </button>
        </div>
      }
    </div>
  `,
})
export class ForumThreadComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private forumService = inject(ForumService);
  authService = inject(AuthService);

  thread = signal<Thread | null>(null);
  posts = signal<Post[]>([]);
  newPostContent = '';

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getThread(id).subscribe((t) => this.thread.set(t));
    this.forumService.getPosts(id).subscribe((p) => this.posts.set(p));
  }

  submitPost(): void {
    if (!this.newPostContent.trim()) return;
    const threadId = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService
      .createPost({ content: this.newPostContent, thread: `/api/threads/${threadId}` as any })
      .subscribe((post) => {
        this.posts.set([...this.posts(), post]);
        this.newPostContent = '';
      });
  }
}
