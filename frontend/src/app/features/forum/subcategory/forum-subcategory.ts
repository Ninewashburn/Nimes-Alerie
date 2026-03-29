import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ForumService } from '@core/services/forum.service';
import { ForumType, SubType } from '@core/models/product.model';

@Component({
  selector: 'app-forum-subcategory',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './forum-subcategory.html',
  styleUrl: './forum-subcategory.scss',
})
export class ForumSubcategoryComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private forumService = inject(ForumService);
  type = signal<ForumType | null>(null);
  subTypes = signal<SubType[]>([]);
  loading = signal(true);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.forumService.getTypeById(id).subscribe((t) => this.type.set(t));
    this.forumService.getSubTypes(id).subscribe({
      next: (s) => {
        this.subTypes.set(s);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }
}
