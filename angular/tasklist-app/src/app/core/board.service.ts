import { catchError, map } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from './api.service';
import { Board } from './model';
import { Moment } from 'moment';

@Injectable()
export class BoardService {

  constructor(
    private api: ApiService
  ) {
  }

  public getBoards(): Observable<Board[]> {
    return this
      .api
      .get('boards').pipe(
        map((result: any) => {
          return Board.deserializeArray(result.data);
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }

  public getBoard(boardDate: Moment): Observable<Board> {
    return this
      .api
      .get('boards/' + boardDate.format('YYYY-MM-DD'))
      .pipe(
        map((result: any) => {
          return Board.deserialize(result.body);
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }

  public postBoard(boardDate: Moment): Observable<Board> {
    return this
      .api
      .post('boards', {date: boardDate.format('YYYY-MM-DD')})
      .pipe(
        map((result: any) => {
          return Board.deserialize(result.body);
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }

  public patchBoard(board: Board): Observable<Board> {
    return this
      .api
      .patch('boards/' + board.id, board.toJSON())
      .pipe(
        map((result: any) => {
          return Board.deserialize(result.body);
        }),
        catchError((error: any) => this.api.prepareFormErrors(error))
      );
  }
}
