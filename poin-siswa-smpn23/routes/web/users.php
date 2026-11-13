<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index')
        ->middleware('role:super_admin,admin');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('users/{role}', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{role}/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users/{role}', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{role}/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{role}/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{role}/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{role}/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
    });
});
