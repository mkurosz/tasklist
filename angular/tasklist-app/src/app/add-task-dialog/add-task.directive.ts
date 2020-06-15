import {
  Directive,
  ElementRef,
  EventEmitter,
  HostListener,
  Input,
  OnInit,
  Output,
} from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { MatSnackBar } from '@angular/material/snack-bar';
import { Board, Task } from "../core/model";
import { AddTaskDialogComponent } from "./add-task-dialog.component";

@Directive({
  selector: '[addTask]',
  exportAs: 'addTask',
})
export class AddTaskDirective implements OnInit {

  @Output() public taskCreated: EventEmitter<Task> = new EventEmitter<Task>();

  @Input('addTask')
  public set board(value: Board) {
    this._board = value ? value : null;
  }

  private _board?: Board;

  constructor(
    private elementRef: ElementRef,
    private dialog: MatDialog,
    private snackBar: MatSnackBar,
  ) {
  }

  public ngOnInit(): void {}

  @HostListener('click')
  public onClick() {
    const dialogRef = this
      .dialog
      .open<AddTaskDialogComponent, Board, Task>(
        AddTaskDialogComponent,
        {
          data: this._board,
        },
      );

    dialogRef
      .afterClosed()
      .subscribe((task) => {
        if (!task) {
          return;
        }

        this.snackBar.open('New task created', 'Ok');
        this.taskCreated.emit(task);
      });
  }
}
