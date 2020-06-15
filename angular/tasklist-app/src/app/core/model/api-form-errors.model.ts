import { ApiModel } from './api-model.interface';
import { staticImplements } from '../static-implements';

@staticImplements<ApiModel<ApiFormErrors>>()
export class ApiFormErrors {
  public static deserialize(x: any): ApiFormErrors {
    const ret = new ApiFormErrors();

    if (x.errors) {
      ret.errors = x.errors;
    }

    if (x.children) {
      ret.children = {};

      for (const child in x.children) {
        if (!x.children.hasOwnProperty(child)) {
          continue;
        }

        ret.children[child] = ApiFormErrors.deserialize(x.children[child]);
      }
    }

    return ret;
  }

  public static deserializeArray(input: any[]): ApiFormErrors[] {
    const ret = [];

    for (const item of input) {
      ret.push(ApiFormErrors.deserialize(item));
    }

    return ret;
  }

  public errors: string[] = [];
  public children?: { [k: string]: ApiFormErrors };
}
