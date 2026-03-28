import { Component, inject, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ArticleService } from '@core/services/article.service';
import { Article } from '@core/models/product.model';

@Component({
  selector: 'app-article-detail',
  standalone: true,
  imports: [RouterLink],
  template: `
    @if (article(); as a) {
      <div class="max-w-3xl mx-auto">
        <a routerLink="/articles" class="text-orange-500 hover:text-orange-600 mb-4 inline-block">← Retour aux articles</a>
        <div class="bg-white rounded-xl shadow-md p-8">
          <h1 class="text-3xl font-bold mb-4">{{ a.name }}</h1>
          <div class="prose text-gray-700 whitespace-pre-wrap">{{ a.content }}</div>
        </div>
      </div>
    }
  `,
})
export class ArticleDetailComponent implements OnInit {
  private route = inject(ActivatedRoute);
  private articleService = inject(ArticleService);
  article = signal<Article | null>(null);

  ngOnInit(): void {
    const id = Number(this.route.snapshot.paramMap.get('id'));
    this.articleService.getById(id).subscribe((a) => this.article.set(a));
  }
}
