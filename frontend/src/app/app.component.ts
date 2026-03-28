import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HeaderComponent } from '@shared/components/header/header.component';
import { FooterComponent } from '@shared/components/footer/footer.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [RouterOutlet, HeaderComponent, FooterComponent],
  template: `
    <app-header />
    <main class="container mx-auto px-4 py-6 min-h-screen">
      <router-outlet />
    </main>
    <app-footer />
  `,
})
export class AppComponent {
  title = 'La Nimes\'Alerie';
}
