import { ApiFormErrors } from './api-form-errors.model';
import { HttpErrorResponse } from '@angular/common/http';

export class ApiError {
  public code: number;
  public message: string;
  public formErrors?: ApiFormErrors;
  public raw: HttpErrorResponse;
  public rawError?: any;

  constructor(rawResponse: HttpErrorResponse) {
    this.code = rawResponse.status;
    this.message = 'Server error';
    this.raw = rawResponse;
    this.rawError = rawResponse.error;

    try {
      let ret: any;

      if (typeof rawResponse.error === 'object') {
        ret = rawResponse.error;
      } else {
        ret = JSON.parse(rawResponse.error);
      }

      this.code = ret.code ? ret.code : this.code;
      this.message = ret.message ? ret.message : this.message;

      if (ret.errors) {
        this.formErrors = ApiFormErrors.deserialize(ret.errors);
      }
    } catch (e) {
      // Actually we do not have to catch anything,
      // as when JSON parse fails we have default parameters, which are quite ok in this case.
    }
  }
}
