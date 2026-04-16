import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { ResetPasswordComponent } from './reset-password';

describe('ResetPasswordComponent', () => {
  let component: ResetPasswordComponent;
  let fixture: ComponentFixture<ResetPasswordComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ResetPasswordComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(ResetPasswordComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should show error if passwords do not match', () => {
    component.token = 'valid-token';
    component.password = 'Password1!';
    component.confirmPassword = 'Different1!';
    component.onSubmit();
    expect(component.error()).toContain('correspondent pas');
    expect(component.loading()).toBeFalse();
  });

  it('should show error if password is too short', () => {
    component.token = 'valid-token';
    component.password = 'short';
    component.confirmPassword = 'short';
    component.onSubmit();
    expect(component.error()).toContain('8 caractères');
  });
});
