import { Observable, of as observableOf } from 'rxjs';
import { Injectable } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

@Injectable()
export class AuthService {
  /**
   * Jwt token cookie name.
   */
  private static readonly JWT_TOKEN_COOKIE: string = 'jwtTokenCookie';

  constructor(
    private cookieService: CookieService
  ) {
  }

  public isAuthenticated(): Observable<boolean> {
    return observableOf(this.getJwtToken() === null);
  }

  public getJwtToken(): string|null {
    return this.cookieService.get(AuthService.JWT_TOKEN_COOKIE);
  }

  /**
   * Deauthenticate.
   */
  public deauthenticate() {
    this.cookieService.delete(AuthService.JWT_TOKEN_COOKIE);
  }
}
