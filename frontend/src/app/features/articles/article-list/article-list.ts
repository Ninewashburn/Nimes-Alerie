import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ArticleService } from '@core/services/article.service';
import { AuthService } from '@core/services/auth.service';
import { Article } from '@core/models/product.model';

@Component({
  selector: 'app-article-list',
  standalone: true,
  imports: [RouterLink, FormsModule],
  templateUrl: './article-list.html',
  styleUrl: './article-list.scss',
})
export class ArticleListComponent implements OnInit {
  private articleService = inject(ArticleService);
  authService = inject(AuthService);

  articles = signal<Article[]>([]);
  loading = signal(true);
  searchQuery = signal('');

  filteredArticles = computed(() => {
    const q = this.searchQuery().toLowerCase().trim();
    if (!q) return this.articles();
    return this.articles().filter(
      (a) => a.name?.toLowerCase().includes(q) || a.content?.toLowerCase().includes(q),
    );
  });

  /* Create form */
  showForm = signal(false);
  submitting = signal(false);
  form = { name: '', content: '' };

  /* Edit modal */
  editingArticle = signal<Article | null>(null);
  editForm = { name: '', content: '' };
  savingEdit = signal(false);

  ngOnInit(): void {
    this.loadArticles();
  }

  loadArticles(): void {
    this.articleService.getAll().subscribe({
      next: (articles) => {
        this.articles.set(articles);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  /* ---- Create ---- */
  toggleForm(): void {
    this.showForm.set(!this.showForm());
    if (!this.showForm()) this.form = { name: '', content: '' };
  }

  submitArticle(): void {
    if (!this.form.name.trim() || !this.form.content.trim()) return;
    this.submitting.set(true);
    this.articleService.create({ name: this.form.name, content: this.form.content }).subscribe({
      next: (article) => {
        this.articles.set([article, ...this.articles()]);
        this.form = { name: '', content: '' };
        this.showForm.set(false);
        this.submitting.set(false);
      },
      error: () => this.submitting.set(false),
    });
  }

  /* ---- Edit ---- */
  openEdit(article: Article, event: Event): void {
    event.preventDefault();
    event.stopPropagation();
    this.editingArticle.set(article);
    this.editForm = { name: article.name, content: article.content };
  }

  closeEdit(): void {
    this.editingArticle.set(null);
    this.editForm = { name: '', content: '' };
  }

  saveEdit(): void {
    const article = this.editingArticle();
    if (!article || !this.editForm.name.trim()) return;
    this.savingEdit.set(true);
    this.articleService.update(article.id, this.editForm).subscribe({
      next: (updated) => {
        this.articles.set(this.articles().map((a) => (a.id === article.id ? updated : a)));
        this.closeEdit();
        this.savingEdit.set(false);
      },
      error: () => this.savingEdit.set(false),
    });
  }

  /* ---- Delete ---- */
  deleteArticle(id: number, event: Event): void {
    event.preventDefault();
    event.stopPropagation();
    if (confirm('Supprimer cet article définitivement ?')) {
      this.articleService.delete(id).subscribe(() => {
        this.articles.set(this.articles().filter((a) => a.id !== id));
      });
    }
  }
}
