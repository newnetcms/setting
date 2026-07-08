<?php

use Newnet\Setting\Http\Controllers\Admin\SystemSettingController;
use Newnet\Setting\Http\Controllers\Admin\UpgradeController;

Route::prefix('setting')
    ->middleware('admin.acl')
    ->name('setting.admin.setting.')
    ->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::post('save', [SystemSettingController::class, 'save'])->name('save');

        Route::get('upgrade', [UpgradeController::class, 'index'])->name('upgrade.index');
        Route::post('upgrade/run', [UpgradeController::class, 'run'])->name('upgrade.run');
    });
