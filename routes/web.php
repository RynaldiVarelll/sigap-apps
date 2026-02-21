<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\ReportController;

// Redirect otomatis ke login
Route::get('/', function () {
    return redirect()->route('login');
});


// =====================
// GUEST (belum login)
// =====================
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});


// =====================
// AUTH (sudah login)
// =====================
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/report/export/pdf', [ReportController::class,'exportPdf'])->name('report.export');

    // Dashboard admin
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Dashboard warga
    Route::get('/warga/dashboard', function () {
        return 'Halo Warga! Ini halaman kamu.';
    })->name('user.dashboard');

    // Laporan warga
    Route::get('/lapor', [ReportController::class, 'index'])
        ->name('user.lapor');

    Route::post('/lapor', [ReportController::class, 'store'])
        ->name('user.lapor.store');

    // Detail laporan
    Route::get('/report/{report}', [ReportController::class, 'show'])
        ->name('report.show');

    // Update status laporan
    Route::put('/report/{report}', [ReportController::class, 'update'])
        ->name('report.update');

    // =====================
    // TANGGAPAN ADMIN (FIX)
    // =====================
    Route::post('/response/store', [ResponseController::class, 'store'])
        ->name('response.store');
});
