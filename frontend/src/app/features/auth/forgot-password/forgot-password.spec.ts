import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { ForgotPasswordComponent } from './forgot-password';

describe('ForgotPasswordComponent', () => {
  let component: ForgotPasswordComponent;
  let fixture: ComponentFixture<ForgotPasswordComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ForgotPasswordComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(ForgotPasswordComponent);
    component = fixture.componentInstance;
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should not submit if email is empty', () => {
    component.email = '';
    component.onSubmit();
    expect(component.loading()).toBeFalse();
    expect(component.sent()).toBeFalse();
  });

  it('should set sent to true on success', () => {
    const httpMock = TestBed.inject(HttpTestingController);
    component.email = 'test@test.com';
    component.onSubmit();
    expect(component.loading()).toBeTrue();
    const req = httpMock.expectOne((r) => r.url.includes('reset-password/request'));
    req.flush(null);
    expect(component.sent()).toBeTrue();
    expect(component.loading()).toBeFalse();
    httpMock.verify();
  });
});
