import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { ForumType } from '@core/models/product.model';

@Component({
  selector: 'app-forum-category-list',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './forum-category-list.html',
  styleUrl: './forum-category-list.scss',
})
export class ForumCategoryListComponent implements OnInit {
  private forumService = inject(ForumService);
  types = signal<ForumType[]>([]);

  ngOnInit(): void {
    this.forumService.getTypes().subscribe((types) => this.types.set(types));
  }
}
