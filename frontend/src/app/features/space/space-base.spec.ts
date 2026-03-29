import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { SpaceBaseComponent } from './space-base';

describe('SpaceBaseComponent', () => {
  let component: SpaceBaseComponent;
  let fixture: ComponentFixture<SpaceBaseComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [SpaceBaseComponent],
      providers: [provideRouter([])]
    }).compileComponents();
    fixture = TestBed.createComponent(SpaceBaseComponent);
    component = fixture.componentInstance;
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
