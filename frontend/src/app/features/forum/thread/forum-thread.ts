import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { AuthService } from '@core/services/auth.service';
import { Post, Thread } from '@core/models/product.model';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-forum-thread',
  standalone: true,
  imports: [RouterLink, DatePipe, FormsModule],
  templateUrl: './forum-thread.html',
  styleUrl: './forum-thread.scss',
})
export class ForumThreadComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private router = inject(Router);
  private forumService = inject(ForumService);
  authService = inject(AuthService);

  thread = signal<Thread | null>(null);
  posts = signal<Post[]>([]);
  newPostContent = '';

  editingPostId = signal<number | null>(null);
  editContent = '';
  savingEdit = signal(false);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getThread(id).subscribe((t) => this.thread.set(t));
    this.forumService.getPosts(id).subscribe((p) => this.posts.set(p));
  }

  canEditPost(post: Post): boolean {
    const me = this.authService.user();
    return !!me && (this.authService.isAdmin() || post.user?.id === me.id);
  }

  /* ---- New post ---- */
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

  /* ---- Edit post ---- */
  startEdit(post: Post): void {
    this.editingPostId.set(post.id);
    this.editContent = post.content;
  }

  cancelEdit(): void {
    this.editingPostId.set(null);
    this.editContent = '';
  }

  saveEdit(): void {
    const id = this.editingPostId();
    if (!id || !this.editContent.trim()) return;
    this.savingEdit.set(true);
    this.forumService.updatePost(id, this.editContent).subscribe({
      next: (updated) => {
        this.posts.set(this.posts().map((p) => (p.id === id ? updated : p)));
        this.cancelEdit();
        this.savingEdit.set(false);
      },
      error: () => this.savingEdit.set(false),
    });
  }

  /* ---- Delete post ---- */
  deletePost(id: number): void {
    if (!confirm('Supprimer ce message ?')) return;
    this.forumService.deletePost(id).subscribe(() => {
      this.posts.set(this.posts().filter((p) => p.id !== id));
    });
  }

  /* ---- Delete thread ---- */
  deleteThread(): void {
    const t = this.thread();
    if (!t || !confirm('Supprimer tout ce canal de transmission ?')) return;
    this.forumService.deleteThread(t.id).subscribe(() => {
      this.router.navigate(['/forum']);
    });
  }
}
