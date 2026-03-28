import { Component } from '@angular/core';

@Component({
  selector: 'app-footer',
  standalone: true,
  template: `
    <footer class="bg-gray-900 text-gray-400 py-8 mt-12">
      <div class="container mx-auto px-4 text-center">
        <p class="text-sm">
          &copy; {{ year }} La Nimes'Alerie - Projet de formation
        </p>
        <p class="text-xs mt-2">
          Angular 19 + Symfony 7.2 + API Platform
        </p>
      </div>
    </footer>
  `,
})
export class FooterComponent {
  year = new Date().getFullYear();
}
