<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'active'])->group(function () {

    // ---------------- SISWA ----------------
    Route::get('students', [StudentController::class, 'index'])
        ->name('students.index')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::get('students/import', [StudentController::class, 'importForm'])->name('students.import');
        Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import.store');
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    Route::get('students/{student}', [StudentController::class, 'show'])
        ->name('students.show')
        ->middleware('role:super_admin,admin,kepala_sekolah,guru');

    // ---------------- KELAS ----------------
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('classes', [ClassController::class, 'store'])->name('classes.store');
        Route::get('classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('classes/{class}', [ClassController::class, 'update'])->name('classes.update');
        Route::delete('classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    });

    // ---------------- GURU ----------------
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('teachers/pending', [TeacherController::class, 'pending'])->name('teachers.pending');
        Route::get('teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::post('teachers/{teacher}/approve', [TeacherController::class, 'approve'])->name('teachers.approve');
        Route::post('teachers/{teacher}/toggle-active', [TeacherController::class, 'toggleActive'])->name('teachers.toggle-active');
        Route::delete('teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });

    // ---------------- ORANG TUA ----------------
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('parents', [OrangTuaController::class, 'index'])->name('parents.index');
        Route::get('parents/create', [OrangTuaController::class, 'create'])->name('parents.create');
        Route::post('parents', [OrangTuaController::class, 'store'])->name('parents.store');
        Route::get('parents/{parent}/edit', [OrangTuaController::class, 'edit'])->name('parents.edit');
        Route::put('parents/{parent}', [OrangTuaController::class, 'update'])->name('parents.update');
        Route::delete('parents/{parent}', [OrangTuaController::class, 'destroy'])->name('parents.destroy');
    });

    // ---------------- TAHUN AJARAN ----------------
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
        Route::get('academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
        Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::get('academic-years/{academic_year}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
        Route::put('academic-years/{academic_year}', [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::post('academic-years/{academic_year}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
        Route::delete('academic-years/{academic_year}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    });
});
