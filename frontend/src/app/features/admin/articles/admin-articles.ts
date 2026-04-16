import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ArticleService } from '@core/services/article.service';
import { Article } from '@core/models/product.model';
import { DatePipe } from '@angular/common';

interface ArticleForm {
  name: string;
  content: string;
}

@Component({
  selector: 'app-admin-articles',
  standalone: true,
  imports: [RouterLink, FormsModule, DatePipe],
  templateUrl: './admin-articles.html',
  styleUrl: './admin-articles.scss',
})
export class AdminArticlesComponent implements OnInit {
  private articleService = inject(ArticleService);

  articles = signal<Article[]>([]);
  loading = signal(true);
  showModal = signal(false);
  submitting = signal(false);
  editingId: number | null = null;
  deleteConfirmId: number | null = null;

  form: ArticleForm = this.emptyForm();

  ngOnInit(): void {
    this.loadArticles();
  }

  private emptyForm(): ArticleForm {
    return { name: '', content: '' };
  }

  loadArticles(): void {
    this.loading.set(true);
    this.articleService.getAll().subscribe({
      next: (articles) => {
        this.articles.set(articles);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  openCreate(): void {
    this.editingId = null;
    this.form = this.emptyForm();
    this.showModal.set(true);
  }

  openEdit(article: Article): void {
    this.editingId = article.id;
    this.form = { name: article.name, content: article.content };
    this.showModal.set(true);
  }

  closeModal(): void {
    this.showModal.set(false);
    this.editingId = null;
    this.form = this.emptyForm();
  }

  submit(): void {
    if (!this.form.name.trim()) return;
    this.submitting.set(true);

    const request$ =
      this.editingId !== null
        ? this.articleService.update(this.editingId, this.form)
        : this.articleService.create(this.form);

    request$.subscribe({
      next: () => {
        this.closeModal();
        this.submitting.set(false);
        this.loadArticles();
      },
      error: () => this.submitting.set(false),
    });
  }

  confirmDelete(id: number): void {
    this.deleteConfirmId = id;
  }

  cancelDelete(): void {
    this.deleteConfirmId = null;
  }

  deleteArticle(id: number): void {
    this.articleService.delete(id).subscribe(() => {
      this.deleteConfirmId = null;
      this.articles.update((list) => list.filter((a) => a.id !== id));
    });
  }

  excerpt(content: string, max = 120): string {
    if (!content) return '';
    return content.length > max ? content.slice(0, max) + '…' : content;
  }
}
