import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { AdminContactsComponent } from './admin-contacts';

describe('AdminContactsComponent', () => {
  let component: AdminContactsComponent;
  let fixture: ComponentFixture<AdminContactsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [AdminContactsComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(AdminContactsComponent);
    component = fixture.componentInstance;
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should start with loading true and empty messages', () => {
    expect(component.loading()).toBeTrue();
    expect(component.messages()).toEqual([]);
  });

  it('should count unread messages correctly', () => {
    component.messages.set([
      { id: 1, email: 'a@a.com', subject: 'Test', message: 'msg', createdAt: '', isRead: false },
      { id: 2, email: 'b@b.com', subject: 'Test2', message: 'msg2', createdAt: '', isRead: true },
      { id: 3, email: 'c@c.com', subject: 'Test3', message: 'msg3', createdAt: '', isRead: false },
    ]);
    expect(component.unreadCount()).toBe(2);
  });

  it('should compute correct totalPages', () => {
    component.totalItems.set(60);
    expect(component.totalPages()).toBe(3);
  });
});
