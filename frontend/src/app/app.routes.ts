import { Routes } from '@angular/router';
import { authGuard, adminGuard } from '@core/guards/auth.guard';

export const routes: Routes = [
  {
    path: '',
    loadComponent: () =>
      import('@features/home/home').then((m) => m.HomeComponent),
  },
  {
    path: 'products',
    loadComponent: () =>
      import('@features/products/product-list/product-list').then(
        (m) => m.ProductListComponent,
      ),
  },
  {
    path: 'products/:id',
    loadComponent: () =>
      import('@features/products/product-detail/product-detail').then(
        (m) => m.ProductDetailComponent,
      ),
  },
  {
    path: 'cart',
    loadComponent: () =>
      import('@features/cart/cart').then((m) => m.CartComponent),
  },
  {
    path: 'login',
    loadComponent: () =>
      import('@features/auth/login/login').then((m) => m.LoginComponent),
  },
  {
    path: 'register',
    loadComponent: () =>
      import('@features/auth/register/register').then(
        (m) => m.RegisterComponent,
      ),
  },
  {
    path: 'articles',
    loadComponent: () =>
      import('@features/articles/article-list/article-list').then(
        (m) => m.ArticleListComponent,
      ),
  },
  {
    path: 'articles/:id',
    loadComponent: () =>
      import('@features/articles/article-detail/article-detail').then(
        (m) => m.ArticleDetailComponent,
      ),
  },
  {
    path: 'forum',
    loadComponent: () =>
      import('@features/forum/category-list/forum-category-list').then(
        (m) => m.ForumCategoryListComponent,
      ),
  },
  {
    path: 'forum/:id',
    loadComponent: () =>
      import('@features/forum/subcategory/forum-subcategory').then(
        (m) => m.ForumSubcategoryComponent,
      ),
  },
  {
    path: 'forum/:id/threads',
    loadComponent: () =>
      import('@features/forum/thread-list/forum-thread-list').then(
        (m) => m.ForumThreadListComponent,
      ),
  },
  {
    path: 'forum/thread/:id',
    loadComponent: () =>
      import('@features/forum/thread/forum-thread').then(
        (m) => m.ForumThreadComponent,
      ),
  },
  {
    path: 'contact',
    loadComponent: () =>
      import('@features/contact/contact').then(
        (m) => m.ContactComponent,
      ),
  },
  {
    path: 'space',
    loadComponent: () =>
      import('@features/space/space-base').then(
        (m) => m.SpaceBaseComponent,
      ),
  },
  {
    path: 'admin',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/dashboard/admin-dashboard').then(
        (m) => m.AdminDashboardComponent,
      ),
  },
  {
    path: 'admin/products',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/products/admin-products').then(
        (m) => m.AdminProductsComponent,
      ),
  },
  {
    path: 'admin/users',
    canActivate: [adminGuard],
    loadComponent: () =>
      import('@features/admin/users/admin-users').then(
        (m) => m.AdminUsersComponent,
      ),
  },
  {
    path: '**',
    redirectTo: '',
  },
];
