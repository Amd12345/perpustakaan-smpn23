<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PointTransactionController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index')
        ->middleware('role:super_admin,admin');

    Route::post('notifications/{notification}/retry', [NotificationController::class, 'retry'])
        ->name('notifications.retry')
        ->middleware('role:super_admin,admin');

    Route::get('point-transactions', [PointTransactionController::class, 'index'])
        ->name('point-transactions.index')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
