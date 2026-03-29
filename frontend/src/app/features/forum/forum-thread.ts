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
  templateUrl: './forum-thread.html',
  styleUrl: './forum-thread.scss',
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
