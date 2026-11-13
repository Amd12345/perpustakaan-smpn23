<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {

    // ---------------- PELANGGARAN ----------------
    Route::get('violations', [ViolationController::class, 'index'])
        ->name('violations.index')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    Route::get('violations/create', [ViolationController::class, 'create'])
        ->name('violations.create')
        ->middleware('role:super_admin,admin,guru');

    Route::post('violations', [ViolationController::class, 'store'])
        ->name('violations.store')
        ->middleware('role:super_admin,admin,guru');

    Route::get('violations/{violation}/edit', [ViolationController::class, 'edit'])
        ->name('violations.edit')
        ->middleware('role:super_admin,admin,guru');

    Route::put('violations/{violation}', [ViolationController::class, 'update'])
        ->name('violations.update')
        ->middleware('role:super_admin,admin,guru');

    Route::post('violations/{violation}/cancel', [ViolationController::class, 'cancel'])
        ->name('violations.cancel')
        ->middleware('role:super_admin,admin,guru');

    Route::delete('violations/{violation}', [ViolationController::class, 'destroy'])
        ->name('violations.destroy')
        ->middleware('role:super_admin,admin');

    Route::get('violations/{violation}', [ViolationController::class, 'show'])
        ->name('violations.show')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    // ---------------- PRESTASI ----------------
    Route::get('achievements', [AchievementController::class, 'index'])
        ->name('achievements.index')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    Route::get('achievements/create', [AchievementController::class, 'create'])
        ->name('achievements.create')
        ->middleware('role:super_admin,admin,guru');

    Route::post('achievements', [AchievementController::class, 'store'])
        ->name('achievements.store')
        ->middleware('role:super_admin,admin,guru');

    Route::get('achievements/{achievement}/edit', [AchievementController::class, 'edit'])
        ->name('achievements.edit')
        ->middleware('role:super_admin,admin,guru');

    Route::put('achievements/{achievement}', [AchievementController::class, 'update'])
        ->name('achievements.update')
        ->middleware('role:super_admin,admin,guru');

    Route::delete('achievements/{achievement}', [AchievementController::class, 'destroy'])
        ->name('achievements.destroy')
        ->middleware('role:super_admin,admin');

    Route::get('achievements/{achievement}', [AchievementController::class, 'show'])
        ->name('achievements.show')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');
});
