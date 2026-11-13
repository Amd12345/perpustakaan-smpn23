<?php

use App\Http\Controllers\MonthlyReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('monthly-reports', [MonthlyReportController::class, 'index'])
        ->name('monthly-reports.index')
        ->middleware('role:super_admin,admin,kepala_sekolah');

    Route::get('monthly-reports/{report}', [MonthlyReportController::class, 'show'])
        ->name('monthly-reports.show')
        ->middleware('role:super_admin,admin,kepala_sekolah');

    Route::get('monthly-reports/{report}/pdf', [MonthlyReportController::class, 'downloadPdf'])
        ->name('monthly-reports.pdf')
        ->middleware('role:super_admin,admin,kepala_sekolah');

    Route::get('monthly-reports/{report}/excel', [MonthlyReportController::class, 'downloadExcel'])
        ->name('monthly-reports.excel')
        ->middleware('role:super_admin,admin,kepala_sekolah');

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('monthly-reports/{report}/revise', [MonthlyReportController::class, 'editRevise'])
            ->name('monthly-reports.revise');
        Route::post('monthly-reports/{report}/revise', [MonthlyReportController::class, 'revise'])
            ->name('monthly-reports.revise.store');
        Route::post('monthly-reports/{report}/finalize', [MonthlyReportController::class, 'finalize'])
            ->name('monthly-reports.finalize');
        Route::post('monthly-reports/generate', [MonthlyReportController::class, 'generate'])
            ->name('monthly-reports.generate');
    });
});
