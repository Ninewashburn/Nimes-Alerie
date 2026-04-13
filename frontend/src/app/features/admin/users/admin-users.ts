import { Component, inject, OnInit, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { UserService } from '@core/services/user.service';
import { User } from '@core/models/product.model';

@Component({
  selector: 'app-admin-users',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './admin-users.html',
  styleUrl: './admin-users.scss',
})
export class AdminUsersComponent implements OnInit {
  private userService = inject(UserService);
  users = signal<User[]>([]);

  ngOnInit(): void {
    this.userService.getAll().subscribe((users) => this.users.set(users));
  }
}
