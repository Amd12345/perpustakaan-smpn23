<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/books/export', [BookController::class, 'export'])->name('books.export');
    Route::post('/books/import', [BookController::class, 'import'])->name('books.import');
    Route::resource('books', BookController::class)->except(['show']);

    Route::get('/loans/export', [LoanController::class, 'export'])->name('loans.export');
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']);

    Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [AccountController::class, 'update'])->name('account.update');
});
