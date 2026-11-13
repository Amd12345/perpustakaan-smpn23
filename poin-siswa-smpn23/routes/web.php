<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicDashboardController::class, 'index'])->name('public.dashboard');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register/guru', [RegisterController::class, 'showRegistrationForm'])->name('register.guru');
Route::post('/register/guru', [RegisterController::class, 'register']);

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'internal'])->name('dashboard');
});

foreach (glob(__DIR__.'/web/*.php') ?: [] as $routeFile) {
    require $routeFile;
}
