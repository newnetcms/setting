<?php

use Newnet\Setting\Http\Controllers\Admin\SystemSettingController;

Route::prefix('setting')
    ->middleware('admin.acl')
    ->name('setting.admin.setting.')
    ->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::post('save', [SystemSettingController::class, 'save'])->name('save');
    });
