import { staticImplements } from '../static-implements';
import { ApiModel } from './api-model.interface';
import { Status } from './status.model';

@staticImplements<ApiModel<Task>>()
export class Task {
  public static deserialize(input: any): Task {
    if (!input.id || !input.name || !input.status) {
      throw new Error('Invalid input for Task model');
    }

    return new Task(
      input.id,
      input.name,
      Status.deserialize(input.status),
      1
    );
  }

  public static deserializeArray(input: any[]): Task[] {
    const ret = [];

    for (const item of input) {
      ret.push(Task.deserialize(item));
    }

    return ret;
  }

  public static createNewTask(): Task {
    return new Task(null, null, Status.getById(Status.TODO), 0);
  }

  constructor(
    public id: number,
    public name: string,
    public status: Status,
    public position: number
  ) {
  }

  public toJSON(): object {
    return {
      id: this.id,
      name: this.name,
      status: this.status,
      position: this.position
    };
  }
}
