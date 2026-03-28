import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { environment } from '@env/environment';
import { User, ApiCollection } from '@core/models/product.model';
import { map } from 'rxjs';

@Component({
  selector: 'app-admin-users',
  standalone: true,
  imports: [RouterLink],
  template: `
    <div class="max-w-6xl mx-auto">
      <div class="mb-8">
        <a routerLink="/admin" class="text-orange-500 hover:text-orange-600 text-sm">← Admin</a>
        <h1 class="text-3xl font-bold">Gestion des utilisateurs</h1>
      </div>

      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left py-3 px-4">Nom</th>
              <th class="text-left py-3 px-4">Email</th>
              <th class="text-left py-3 px-4">Ville</th>
              <th class="text-left py-3 px-4">Rôles</th>
            </tr>
          </thead>
          <tbody>
            @for (user of users(); track user.id) {
              <tr class="border-t hover:bg-gray-50">
                <td class="py-3 px-4">{{ user.firstName }} {{ user.lastName }}</td>
                <td class="py-3 px-4">{{ user.email }}</td>
                <td class="py-3 px-4">{{ user.city }}</td>
                <td class="py-3 px-4">
                  @for (role of user.roles; track role) {
                    <span class="text-xs bg-gray-200 rounded px-2 py-1 mr-1">{{ role }}</span>
                  }
                </td>
              </tr>
            }
          </tbody>
        </table>
      </div>
    </div>
  `,
})
export class AdminUsersComponent implements OnInit {
  private http = inject(HttpClient);
  users = signal<User[]>([]);

  ngOnInit(): void {
    this.http
      .get<ApiCollection<User>>(`${environment.apiUrl}/users`)
      .pipe(map((res) => res['hydra:member']))
      .subscribe((users) => this.users.set(users));
  }
}
