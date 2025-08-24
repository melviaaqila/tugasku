<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Master\KantorController;
use App\Http\Controllers\Master\DivisiController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\UserController; // ✅ tambahin ini
use App\Http\Controllers\Auth\LogoutController;

// Halaman utama
// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');
Route::get('/', function () {
    return Inertia::render('auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ----------------- MASTER -----------------
// Gunakan prefix kosong agar URL jadi /kantor, /divisi, dsb
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kantor', KantorController::class)->middleware(['role:admin']);
    Route::resource('divisi', DivisiController::class)->middleware(['role:admin']);
    Route::resource('roles', RoleController::class)->middleware(['role:admin']);
    Route::resource('users', UserController::class)->middleware(['role:admin']); // ✅ tambahin route user
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
