import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { HeaderComponent } from './header';

describe('HeaderComponent', () => {
  let component: HeaderComponent;
  let fixture: ComponentFixture<HeaderComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HeaderComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(HeaderComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should start with mobile menu closed', () => {
    expect(component.mobileMenuOpen()).toBeFalse();
  });

  it('should toggle mobile menu open and closed', () => {
    component.toggleMobileMenu();
    expect(component.mobileMenuOpen()).toBeTrue();

    component.toggleMobileMenu();
    expect(component.mobileMenuOpen()).toBeFalse();
  });

  it('should close mobile menu explicitly', () => {
    component.toggleMobileMenu();
    expect(component.mobileMenuOpen()).toBeTrue();

    component.closeMobileMenu();
    expect(component.mobileMenuOpen()).toBeFalse();
  });

  it('should toggle dark mode', () => {
    const initial = component.isDarkMode();
    component.toggleTheme();
    expect(component.isDarkMode()).toBe(!initial);
  });
});
