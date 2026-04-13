import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { ProductService } from '@core/services/product.service';
import { Product } from '@core/models/product.model';
import { CurrencyPipe } from '@angular/common';

interface ProductForm {
  title: string;
  description: string;
  priceHT: string;
  priceTTC: string;
  quantity: number;
  isActive: boolean;
}

@Component({
  selector: 'app-admin-products',
  standalone: true,
  imports: [RouterLink, CurrencyPipe, FormsModule],
  templateUrl: './admin-products.html',
  styleUrl: './admin-products.scss',
})
export class AdminProductsComponent implements OnInit {
  private productService = inject(ProductService);

  products = signal<Product[]>([]);
  showModal = signal(false);
  submitting = signal(false);
  editingId: number | null = null;

  form: ProductForm = this.emptyForm();

  ngOnInit(): void {
    this.loadProducts();
  }

  private emptyForm(): ProductForm {
    return { title: '', description: '', priceHT: '', priceTTC: '', quantity: 0, isActive: true };
  }

  private loadProducts(): void {
    this.productService.getAll(1, 100).subscribe(({ items }) => this.products.set(items));
  }

  openCreate(): void {
    this.editingId = null;
    this.form = this.emptyForm();
    this.showModal.set(true);
  }

  openEdit(product: Product): void {
    this.editingId = product.id;
    this.form = {
      title: product.title,
      description: product.description,
      priceHT: product.priceHT,
      priceTTC: product.priceTTC,
      quantity: product.quantity,
      isActive: product.isActive,
    };
    this.showModal.set(true);
  }

  closeModal(): void {
    this.showModal.set(false);
    this.editingId = null;
    this.form = this.emptyForm();
  }

  submit(): void {
    if (!this.form.title.trim()) return;
    this.submitting.set(true);

    const payload: Partial<Product> = { ...this.form };

    const request$ =
      this.editingId !== null
        ? this.productService.update(this.editingId, payload)
        : this.productService.create(payload);

    request$.subscribe({
      next: () => {
        this.closeModal();
        this.submitting.set(false);
        this.loadProducts();
      },
      error: () => this.submitting.set(false),
    });
  }

  deleteProduct(id: number): void {
    if (confirm('Supprimer ce produit définitivement ?')) {
      this.productService.delete(id).subscribe(() => this.loadProducts());
    }
  }

  uploadingImageId = signal<number | null>(null);

  onImageSelected(product: Product, event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;
    this.uploadingImageId.set(product.id);
    this.productService.uploadImage(product.id, file).subscribe({
      next: (updated) => {
        this.products.set(this.products().map((p) => (p.id === updated.id ? updated : p)));
        this.uploadingImageId.set(null);
      },
      error: () => this.uploadingImageId.set(null),
    });
  }
}
