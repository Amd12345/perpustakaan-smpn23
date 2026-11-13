<?php

use App\Http\Controllers\ConversionController;
use App\Http\Controllers\ImprovementTaskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:super_admin,admin,guru'])->group(function () {
    Route::get('conversions', [ConversionController::class, 'index'])->name('conversions.index');
    Route::get('conversions/create', [ConversionController::class, 'create'])->name('conversions.create');
    Route::post('conversions', [ConversionController::class, 'store'])->name('conversions.store');

    Route::get('improvements', [ImprovementTaskController::class, 'index'])->name('improvements.index');
    Route::get('improvements/create', [ImprovementTaskController::class, 'create'])->name('improvements.create');
    Route::post('improvements', [ImprovementTaskController::class, 'store'])->name('improvements.store');
    Route::get('improvements/{task}', [ImprovementTaskController::class, 'show'])->name('improvements.show');
    Route::post('improvements/{task}/submissions', [ImprovementTaskController::class, 'storeSubmission'])->name('improvements.submissions.store');
    Route::post('improvements/{task}/approve', [ImprovementTaskController::class, 'approve'])->name('improvements.approve');
    Route::post('improvements/{task}/reject', [ImprovementTaskController::class, 'reject'])->name('improvements.reject');
    Route::post('improvements/{task}/revision', [ImprovementTaskController::class, 'revision'])->name('improvements.revision');
});
