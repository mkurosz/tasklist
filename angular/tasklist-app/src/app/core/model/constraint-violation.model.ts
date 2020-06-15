import { staticImplements } from '../static-implements';
import { ApiModel } from './api-model.interface';
import { isNullOrUndefined } from 'util';

@staticImplements<ApiModel<ConstraintViolation>>()
export class ConstraintViolation {
  public static deserialize(input: any): ConstraintViolation {
    if (!input.message || !input.code || !input.property_path || isNullOrUndefined(input.parameters)) {
      throw new Error('Invalid input for ConstraintViolation');
    }

    return new ConstraintViolation(
      input.message,
      input.code,
      input.property_path,
      input.parameters,
    );
  }

  public static deserializeArray(input: any[]): ConstraintViolation[] {
    const ret = [];

    for (const item of input) {
      ret.push(ConstraintViolation.deserialize(item));
    }

    return ret;
  }

  constructor(
    public message: string,
    public code: string,
    public propertyPath: string,
    public parameters: string[]
  ) {

  }
}
