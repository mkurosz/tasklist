import { from, Observable, ObservableInput, throwError } from 'rxjs';
import { catchError, map, switchAll } from 'rxjs/operators';
import { HttpClient, HttpErrorResponse, HttpHeaders, HttpParams } from '@angular/common/http';
import { isArray, isNullOrUndefined } from 'util';
import { ConstraintViolation } from './model';
import { Injectable } from "@angular/core";
import { AuthService } from "./auth.service";

@Injectable()
export class ApiService {
  /**
   * API base url.
   */
  private static readonly API_BASE_URL: string = '/api/';

  constructor(
    private httpClient: HttpClient,
    private authService: AuthService
  ) {
  }

  public get(url: string, options?: HttpOptions): Observable<any> {
    return this
      .httpClient
      .get(ApiService.API_BASE_URL + url, this.prepareOptions(options))
      .pipe(
        catchError((err) => ApiService.catchApiError(err))
      );
  }

  public post(url: string, body: any, options?: HttpOptions): Observable<any> {
    return this
      .httpClient
      .post(ApiService.API_BASE_URL + url, body, this.prepareOptions(options))
      .pipe(
        catchError((err) => ApiService.catchApiError(err)),
      );
  }

  public put(url: string, body: any, options?: HttpOptions): Observable<any> {
    return this
      .httpClient
      .put(ApiService.API_BASE_URL + url, body, this.prepareOptions(options))
      .pipe(
        catchError((err) => ApiService.catchApiError(err)),
      );
  }

  public delete(url: string, options?: HttpOptions): Observable<any> {
    return this
      .httpClient
      .delete(ApiService.API_BASE_URL + url, this.prepareOptions(options))
      .pipe(
        catchError((err) => ApiService.catchApiError(err)),
      );
  }

  public patch(url: string, body: any, options?: HttpOptions): Observable<any> {
    return this
      .httpClient
      .patch(ApiService.API_BASE_URL + url, body, this.prepareOptions(options))
      .pipe(
        catchError((err) => ApiService.catchApiError(err)),
      );
  }

  /**
   * Prepare errors for API calls using Symfony validation flow.
   *
   * @param error API error object.
   */
  public prepareFormErrors(error: any): Observable<never> {
    if (error.data && isArray(error.data)) {
      for (const field in error.data) {
        if (!error.data.hasOwnProperty(field)) {
          continue;
        }

        error.data[field] = ConstraintViolation.deserialize(error.data[field]);
      }
    }

    return throwError(error);
  }

  private static catchApiError(error: HttpErrorResponse): ObservableInput<any> {
    if (error && error.status && error.status === 401) {
      window.location.href = '/login';
    }

    try {
      return throwError({
        code: error.status,
        data: error.error.message,
        rawData: error,
      });
    } catch (e) {
      return throwError({
        code: error.status,
        data: 'Server error',
        rawData: error,
      });
    }
  }

  private prepareOptions(options?: HttpOptions): HttpOptions {
    if (isNullOrUndefined(options)) {
      options = {
        observe: 'response',
        responseType: 'json',
      };
    }
    options.headers = new HttpHeaders({
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + this.authService.getJwtToken()
    });

    return options;
  }
}

/**
 * This is utility class to help with passing options to http client
 */
interface HttpOptions {
  headers?: HttpHeaders;
  observe?: any;
  params?: HttpParams;
  body?: any;
  reportProgress?: boolean;
  responseType?: any;
  withCredentials?: boolean;
}
