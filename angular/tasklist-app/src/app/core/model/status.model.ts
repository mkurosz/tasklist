import { staticImplements } from '../static-implements';
import { ApiModel } from './api-model.interface';

@staticImplements<ApiModel<Status>>()
export class Status {
  /**
   * Status to do.
   */
  public static readonly TODO: number = 1;

  /**
   * Status in progress.
   */
  public static readonly IN_PROGRESS: number = 2;

  /**
   * Status done.
   */
  public static readonly DONE: number = 3;

  /**
   * Status name to do.
   */
  public static readonly TODO_NAME: string = 'To do';

  /**
   * Status name in progress.
   */
  public static readonly IN_PROGRESS_NAME: string = 'In progress';

  /**
   * Status name done.
   */
  public static readonly DONE_NAME: string = 'Done';

  public static deserialize(input: any): Status {
    if (!input.id || !input.name) {
      throw new Error('Invalid input for Status model');
    }

    return new Status(
      input.id,
      input.name
    );
  }

  public static deserializeArray(input: any[]): Status[] {
    const ret = [];

    for (const item of input) {
      ret.push(Status.deserialize(item));
    }

    return ret;
  }

  public static getById(statusId: number): Status {
    switch (statusId) {
      case Status.DONE:
        return new Status(Status.DONE, Status.DONE_NAME);
      case Status.IN_PROGRESS:
        return new Status(Status.IN_PROGRESS, Status.IN_PROGRESS_NAME);
      default:
        return new Status(Status.TODO, Status.TODO_NAME);
    }
  }

  public static getAll(): Status[] {
      return [
        new Status(Status.TODO, Status.TODO_NAME),
        new Status(Status.IN_PROGRESS, Status.IN_PROGRESS_NAME),
        new Status(Status.DONE, Status.DONE_NAME),
      ];
  }

  constructor(
    public id: number,
    public name: string
  ) {
  }

  public toJSON(): object {
    return {
      id: this.id,
      name: this.name
    };
  }
}
