<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

// Guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Auth (sudah login)
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/warga/dashboard', function () {
        return 'Halo Warga! Ini halaman kamu.';
    })->name('user.dashboard');

    Route::get('/lapor', [ReportController::class, 'index'])
        ->name('user.lapor');

    Route::post('/lapor', [ReportController::class, 'store'])
        ->name('user.lapor.store');
});
