import { Component, OnInit } from '@angular/core';
import { CdkDragDrop, moveItemInArray, transferArrayItem } from '@angular/cdk/drag-drop';
import { Board, BoardService, Status, Task, TaskService } from '../core';
import * as moment from 'moment';

@Component({
  selector: 'app-taskboard',
  templateUrl: './taskboard.component.html',
  styleUrls: ['./taskboard.component.scss'],
})
export class TaskboardComponent implements OnInit {
  public board: Board = Board.createNewBoard();

  constructor(
    private boardService: BoardService,
    private taskService: TaskService,
  ) {
  }

  public ngOnInit(): void {
    this.initBoard();

  }

  public drop(event: CdkDragDrop<Task[]>): void {
    if (event.previousContainer === event.container) {
      moveItemInArray(
        event.container.data,
        event.previousIndex,
        event.currentIndex
      );
    } else {
      transferArrayItem(
        event.previousContainer.data,
        event.container.data,
        event.previousIndex,
        event.currentIndex
      );
    }

    this.refreshTasks();
    this
      .boardService
      .patchBoard(this.board)
      .subscribe(
        (board: Board) => {
          this.board = board;
        }
      );
  }

  public deleteTask(task: Task): void {
    this
      .taskService
      .deleteTask(this.board, task)
      .subscribe(
        () => {
          this.board.deleteTask(task);
        },
      );
  }

  public onTaskCreated(task: Task): void {
    this.board.addTask(task);
  }

  private refreshTasks(): void {
    this.board.todo = this.board.todo.map(
      (task: Task, index: number) => {
        task.position = ++index;
        task.status = Status.getById(Status.TODO);

        return task;
      },
    );
    this.board.inProgress = this.board.inProgress.map(
      (task: Task, index: number) => {
        task.position = ++index;
        task.status = Status.getById(Status.IN_PROGRESS);

        return task;
      },
    );
    this.board.done = this.board.done.map(
      (task: Task, index: number) => {
        task.position = ++index;
        task.status = Status.getById(Status.DONE);

        return task;
      },
    );
  }

  private initBoard(): void {
    this
      .boardService
      .getBoard(moment())
      .subscribe(
        (board: Board) => {
          if (board.id !== null) {
            this.board = board;

            return;
          }

          this
            .boardService
            .postBoard(moment())
            .subscribe(
              (board: Board) => {
                this.board = board;
              }
            )
        }
      );
  }
}
