import { Routes } from '@angular/router';
import { authGuard, adminGuard } from '@core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('@features/home/home.component').then((m) => m.HomeComponent),
  },
  {
    path: 'products',
    loadComponent: () =>
      import('@features/products/product-list.component').then(
        (m) => m.ProductListComponent,
      ),
  },
  {
    path: 'products/:id',
    loadComponent: () =>
      import('@features/products/product-detail.component').then(
        (m) => m.ProductDetailComponent,
      ),
  },
  {
    path: 'cart',
    loadComponent: () =>
      import('@features/cart/cart.component').then((m) => m.CartComponent),
  },
  {
    path: 'login',
    loadComponent: () =>
      import('@features/auth/login.component').then((m) => m.LoginComponent),
  },
  {
    path: 'register',
    loadComponent: () =>
      import('@features/auth/register.component').then(
        (m) => m.RegisterComponent,
      ),
  },
  {
    path: 'articles',
    loadComponent: () =>
      import('@features/articles/article-list.component').then(
        (m) => m.ArticleListComponent,
      ),
  },
  {
    path: 'articles/:id',
    loadComponent: () =>
      import('@features/articles/article-detail.component').then(
        (m) => m.ArticleDetailComponent,
      ),
  },
  {
    path: 'forum',
    loadComponent: () =>
      import('@features/forum/forum-category-list.component').then(
        (m) => m.ForumCategoryListComponent,
      ),
  },
  {
    path: 'forum/:id',
    loadComponent: () =>
      import('@features/forum/forum-subcategory.component').then(
        (m) => m.ForumSubcategoryComponent,
      ),
  },
  {
    path: 'forum/:id/threads',
    loadComponent: () =>
      import('@features/forum/forum-thread-list.component').then(
        (m) => m.ForumThreadListComponent,
      ),
  },
  {
    path: 'forum/thread/:id',
    loadComponent: () =>
      import('@features/forum/forum-thread.component').then(
        (m) => m.ForumThreadComponent,
      ),
  },
  {
    path: 'contact',
    loadComponent: () =>
      import('@features/contact/contact.component').then(
        (m) => m.ContactComponent,
      ),
  },
  {
    path: 'space',
    loadComponent: () =>
      import('@features/space/space-base.component').then(
        (m) => m.SpaceBaseComponent,
      ),
  },
  {
    path: 'admin',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/admin-dashboard.component').then(
        (m) => m.AdminDashboardComponent,
      ),
  },
  {
    path: 'admin/products',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/admin-products.component').then(
        (m) => m.AdminProductsComponent,
      ),
  },
  {
    path: 'admin/users',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/admin-users.component').then(
        (m) => m.AdminUsersComponent,
      ),
  },
  {
    path: '**',
    redirectTo: '',
  },
];
