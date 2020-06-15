import { staticImplements } from '../static-implements';
import { ApiModel } from './api-model.interface';
import { Task } from './task.model';
import { Status } from './status.model';
import * as moment from 'moment';
import { Moment } from "moment";

@staticImplements<ApiModel<Board>>()
export class Board {
  public static deserialize(input: any): Board {
    if (!input) {
      return Board.createNewBoard();
    }

    if (!input.id || !input.date || !input.tasks) {
      throw new Error('Invalid input for Board model');
    }

    return new Board(
      input.id,
      moment.utc(input.date),
      Task.deserializeArray(
        input.tasks.filter(
          task => task.status.id === Status.TODO
        ).sort(function (a, b) {
          return a.position - b.position;
        })
      ),
      Task.deserializeArray(
        input.tasks.filter(
          task => task.status.id === Status.IN_PROGRESS
        ).sort(function (a, b) {
          return a.position - b.position;
        })
      ),
      Task.deserializeArray(
        input.tasks.filter(
          task => task.status.id === Status.DONE
        ).sort(function (a, b) {
          return a.position - b.position;
        })
      )
    );
  }

  public static deserializeArray(input: any[]): Board[] {
    const ret = [];

    for (const item of input) {
      ret.push(Board.deserialize(item));
    }

    return ret;
  }

  public static createNewBoard(): Board {
    return new Board(null, null, [], [], []);
  }

  constructor(
    public id?: number,
    public boardDate?: Moment,
    public todo?: Task[],
    public inProgress?: Task[],
    public done?: Task[]
  ) {
  }

  public toJSON(): object {
    let tasks = this.todo;
    tasks = tasks.concat(this.inProgress);
    tasks = tasks.concat(this.done);

    return {
      id: this.id,
      date: this.boardDate.format('YYYY-MM-DD'),
      tasks: tasks
    };
  }

  public addTask(task: Task): void {
    switch (task.status.id) {
      case Status.DONE:
        this.done.push(task);
        return;
      case Status.IN_PROGRESS:
        this.inProgress.push(task);
        return;
      default:
        this.todo.push(task);
        return;
    }
  }

  public deleteTask(task: Task): void {
    switch (task.status.id) {
      case Status.DONE:
        this.done = this.removeTaskFromList(this.done, task);
        return;
      case Status.IN_PROGRESS:
        this.inProgress = this.removeTaskFromList(this.inProgress, task);
        return;
      default:
        this.todo = this.removeTaskFromList(this.todo, task);
        return;
    }
  }

  private removeTaskFromList(
    taskList: Task[],
    taskToRemove: Task
  ): Task[] {
    return taskList
      .filter(
        (task: Task) => {
          return task.id !== taskToRemove.id;
        }
      );
  }
}
