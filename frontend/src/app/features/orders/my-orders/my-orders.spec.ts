import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { MyOrdersComponent } from './my-orders';

describe('MyOrdersComponent', () => {
  let component: MyOrdersComponent;
  let fixture: ComponentFixture<MyOrdersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MyOrdersComponent],
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    }).compileComponents();
    fixture = TestBed.createComponent(MyOrdersComponent);
    component = fixture.componentInstance;
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should return correct status label', () => {
    expect(component.statusLabel('pending')).toBe('En attente');
    expect(component.statusLabel('shipped')).toBe('Expédiée');
    expect(component.statusLabel('refunded')).toBe('Remboursée');
    expect(component.statusLabel('preparing')).toBe('En préparation');
  });

  it('should start with loading true and empty orders', () => {
    expect(component.loading()).toBeTrue();
    expect(component.orders()).toEqual([]);
  });
});
