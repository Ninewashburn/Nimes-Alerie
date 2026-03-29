import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-space-base',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './space-base.html',
  styleUrl: './space-base.scss',
})
export class SpaceBaseComponent {}
