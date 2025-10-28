<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\DivisiController;
use App\Http\Controllers\Master\KantorController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\UserController; // ✅ tambahin ini
use App\Http\Controllers\History\HistoryController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Master\RoutineTaskController;

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

Route::prefix('dashboard')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    
    Route::resource('tugasku', TaskController::class)->except(['show'])->names('tugasku');

    Route::prefix('tugasku')->group(function () {
        Route::resource('routine', RoutineTaskController::class)
            ->names('routine');

        Route::get('history', [HistoryController::class, 'index'])->name('history.index');
    });
});

// ----------------- MASTER -----------------
// Gunakan prefix kosong agar URL jadi /kantor, /divisi, dsb
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kantor', KantorController::class);
    Route::resource('divisi', DivisiController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class); // ✅ tambahin route user
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
