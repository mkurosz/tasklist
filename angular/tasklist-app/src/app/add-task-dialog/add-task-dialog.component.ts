import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { ApiError, Board, Task, Status } from '../core/model';
import { TaskService } from "../core";

@Component({
  styleUrls: ['add-task-dialog.component.scss'],
  templateUrl: 'add-task-dialog.component.html',
})

export class AddTaskDialogComponent implements OnInit {

  public statuses: Status[] = Status.getAll();
  public task: Task = Task.createNewTask();
  public inProgress: boolean = false;
  public createError?: string = null;

  constructor(
    public dialogRef: MatDialogRef<AddTaskDialogComponent, Task>,
    private taskService: TaskService,
    @Inject(MAT_DIALOG_DATA) public board: Board
  ) {
    // noop
  }

  public ngOnInit(): void {}

  public createTask() {
    if (!this.task) {
      return;
    }

    this.inProgress = true;
    this.createError = null;
    this
      .taskService
      .postTask(this.board, this.task)
      .subscribe(
        (data) => {
          this.inProgress = false;
          this.dialogRef.close(data);
        },
        (data: ApiError) => {
          this.inProgress = false;
          this.createError = data.message;
        },
      );
  }
}
