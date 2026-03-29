import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { Thread } from '@core/models/product.model';
import { DatePipe } from '@angular/common';

@Component({
  selector: 'app-forum-thread-list',
  standalone: true,
  imports: [RouterLink, DatePipe],
  templateUrl: './forum-thread-list.html',
  styleUrl: './forum-thread-list.scss',
})
export class ForumThreadListComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private forumService = inject(ForumService);
  threads = signal<Thread[]>([]);
  loading = signal(true);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getThreads(id).subscribe({
      next: (t) => {
        this.threads.set(t);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }
}
