import { catchError, map } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Board, Task } from './model';

@Injectable()
export class TaskService {

  constructor(
    private api: ApiService
  ) {
  }

  public postTask(board: Board, task: Task): Observable<Task> {
    return this
      .api
      .post('boards/' + board.id + '/tasks', task.toJSON())
      .pipe(
        map((result: any) => {
          return Task.deserialize(result.body);
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }

  public deleteTask(board: Board, task: Task): Observable<Task> {
    return this
      .api
      .delete('boards/' + board.id + '/tasks/' + task.id)
      .pipe(
        map((result: any) => {
          return result;
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }
}
