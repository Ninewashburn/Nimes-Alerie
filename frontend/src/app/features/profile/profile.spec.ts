import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { ProfileComponent } from './profile';

describe('ProfileComponent', () => {
  let component: ProfileComponent;
  let fixture: ComponentFixture<ProfileComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ProfileComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(ProfileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should show password error when passwords do not match', () => {
    component.passwordForm.newPassword = 'Password1!';
    component.passwordForm.confirmPassword = 'Different1!';
    component.changePassword();
    expect(component.passwordError()).toContain('correspondent pas');
  });

  it('should show error when new password is too short', () => {
    component.passwordForm.newPassword = '123';
    component.passwordForm.confirmPassword = '123';
    component.changePassword();
    expect(component.passwordError()).toContain('6 caractères');
  });
});
