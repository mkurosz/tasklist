import { DragDropModule } from '@angular/cdk/drag-drop';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';
import { NoopAnimationsModule } from '@angular/platform-browser/animations';
import { TaskboardComponent } from './taskboard.component';

describe('TaskboardComponent', () => {
  let component: TaskboardComponent;
  let fixture: ComponentFixture<TaskboardComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [TaskboardComponent],
      imports: [
        NoopAnimationsModule,
        DragDropModule,
      ]
    }).compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TaskboardComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should compile', () => {
    expect(component).toBeTruthy();
  });
});
