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
  templateUrl: './admin-users.html',
  styleUrl: './admin-users.scss',
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
