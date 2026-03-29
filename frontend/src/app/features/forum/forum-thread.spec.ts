import { ComponentFixture, TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { ActivatedRoute } from '@angular/router';
import { ForumThreadComponent } from './forum-thread';

describe('ForumThreadComponent', () => {
  let component: ForumThreadComponent;
  let fixture: ComponentFixture<ForumThreadComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ForumThreadComponent],
      providers: [
        provideRouter([]),
        provideHttpClient(),
        provideHttpClientTesting(),
        { provide: ActivatedRoute, useValue: { snapshot: { paramMap: { get: () => '1' } } } }
      ]
    }).compileComponents();
    fixture = TestBed.createComponent(ForumThreadComponent);
    component = fixture.componentInstance;
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
