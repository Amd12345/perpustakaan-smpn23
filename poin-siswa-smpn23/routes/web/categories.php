<?php

use App\Http\Controllers\AchievementCategoryController;
use App\Http\Controllers\PointRuleController;
use App\Http\Controllers\ViolationCategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active', 'role:super_admin,admin'])->group(function () {
    Route::resource('violation-categories', ViolationCategoryController::class)
        ->except(['show'])
        ->parameters(['violation-categories' => 'violationCategory']);

    Route::resource('achievement-categories', AchievementCategoryController::class)
        ->except(['show'])
        ->parameters(['achievement-categories' => 'achievementCategory']);

    Route::get('point-rules', [PointRuleController::class, 'index'])->name('point-rules.index');
    Route::post('point-rules', [PointRuleController::class, 'store'])->name('point-rules.store');
    Route::put('point-rules/{point_rule}', [PointRuleController::class, 'update'])->name('point-rules.update');
    Route::delete('point-rules/{point_rule}', [PointRuleController::class, 'destroy'])->name('point-rules.destroy');
});
