<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login.form');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected
Route::middleware('auth')->group(function () {

    // route dashboard umum (biar route('dashboard') aman)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $role = $user?->role?->nama; // relasi role->nama

        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'user'     => redirect()->route('user.dashboard'),
            default    => abort(403),
        };
    })->name('dashboard');

    // =========================
    // ADMIN
    // =========================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // Manajemen User (ADMIN ONLY)
        Route::get('/manage_user', [UserController::class, 'index'])->name('users.index');
        Route::get('/create_user', [UserController::class, 'create'])->name('users.create');
        Route::post('/manage_user', [UserController::class, 'store'])->name('users.store');
        Route::get('/manage_user/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/manage_user/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/manage_user/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Kategori Sarpras
        Route::get('/kategori_sarpras', [SarprasController::class, 'index'])->name('kategori_sarpras.index');
        Route::get('/kategori_sarpras/create', [SarprasController::class, 'create'])->name('kategori_sarpras.create');
        Route::post('/kategori_sarpras', [SarprasController::class, 'store'])->name('kategori_sarpras.store');
        Route::get('/kategori_sarpras/{id}/edit', [SarprasController::class, 'edit'])->name('kategori_sarpras.edit');
        Route::put('/kategori_sarpras/{id}', [SarprasController::class, 'update'])->name('kategori_sarpras.update');
        Route::delete('/kategori_sarpras/{id}', [SarprasController::class, 'destroy'])->name('kategori_sarpras.destroy');

    });

    // =========================
    // OPERATOR
    // =========================
    Route::middleware('role:operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('/', [DashboardController::class, 'operatorDashboard'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    });

    // =========================
    // USER
    // =========================
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/', [DashboardController::class, 'userDashboard'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    });
});
