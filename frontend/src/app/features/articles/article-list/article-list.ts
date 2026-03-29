import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { ArticleService } from '@core/services/article.service';
import { AuthService } from '@core/services/auth.service';
import { Article } from '@core/models/product.model';

@Component({
  selector: 'app-article-list',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './article-list.html',
  styleUrl: './article-list.scss',
})
export class ArticleListComponent implements OnInit {
  private articleService = inject(ArticleService);
  authService = inject(AuthService);

  articles = signal<Article[]>([]);

  ngOnInit(): void {
    this.articleService.getAll().subscribe((articles) => this.articles.set(articles));
  }
}
