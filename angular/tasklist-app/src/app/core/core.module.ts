import { NgModule, Optional, SkipSelf } from '@angular/core';
import { CommonModule } from '@angular/common';
import { throwIfAlreadyLoaded } from './module-import-guard';
import { ApiService } from './api.service';
import { AuthService } from './auth.service';
import { BoardService } from './board.service';

@NgModule({
  imports: [
    CommonModule,
  ],
  exports: [],
  declarations: [],
  providers: [
    ApiService,
    AuthService,
    BoardService,
  ],
})
export class CoreModule {
  constructor(@Optional() @SkipSelf() parentModule: CoreModule) {
    throwIfAlreadyLoaded(parentModule, 'CoreModule');
  }
}
