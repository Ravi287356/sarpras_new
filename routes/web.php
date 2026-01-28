<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriSarprasController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SarprasAvailableController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', function () {
    return redirect()->route('login.form');
});

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// PROTECTED
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $role = auth()->user()?->role?->nama;

        return match ($role) {
            'admin'    => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'user'     => redirect()->route('user.dashboard'),
            default    => abort(403),
        };
    })->name('dashboard');

    // SARPRAS TERSEDIA (semua role)
    Route::get('/sarpras-tersedia', [SarprasAvailableController::class, 'index'])
        ->name('sarpras.available');

    // =========================
    // ADMIN
    // =========================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // Activity Logs (kalau mau dihiraukan, boleh hapus bagian ini)
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity_logs.index');

        // profil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // user
        Route::get('/manage_user', [UserController::class, 'index'])->name('users.index');
        Route::get('/create_user', [UserController::class, 'create'])->name('users.create');
        Route::post('/manage_user', [UserController::class, 'store'])->name('users.store');
        Route::get('/manage_user/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/manage_user/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/manage_user/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // kategori sarpras
        Route::get('/kategori_sarpras', [KategoriSarprasController::class, 'index'])->name('kategori_sarpras.index');
        Route::get('/kategori_sarpras/create', [KategoriSarprasController::class, 'create'])->name('kategori_sarpras.create');
        Route::post('/kategori_sarpras', [KategoriSarprasController::class, 'store'])->name('kategori_sarpras.store');
        Route::get('/kategori_sarpras/{id}/edit', [KategoriSarprasController::class, 'edit'])->name('kategori_sarpras.edit');
        Route::put('/kategori_sarpras/{id}', [KategoriSarprasController::class, 'update'])->name('kategori_sarpras.update');
        Route::delete('/kategori_sarpras/{id}', [KategoriSarprasController::class, 'destroy'])->name('kategori_sarpras.destroy');

        // sarpras
        Route::get('/sarpras', [SarprasController::class, 'index'])->name('sarpras.index');
        Route::get('/sarpras/create', [SarprasController::class, 'create'])->name('sarpras.create');
        Route::post('/sarpras', [SarprasController::class, 'store'])->name('sarpras.store');
        Route::get('/sarpras/{sarpras}/edit', [SarprasController::class, 'edit'])->name('sarpras.edit');
        Route::put('/sarpras/{sarpras}', [SarprasController::class, 'update'])->name('sarpras.update');
        Route::delete('/sarpras/{sarpras}', [SarprasController::class, 'destroy'])->name('sarpras.destroy');

        // lokasi
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
        Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
        Route::get('/lokasi/{lokasi}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
        Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
        Route::delete('/lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

        // =========================
        // PEMINJAMAN (ADMIN) ✅ sekali saja, tidak duplikat
        // =========================
        Route::get('/peminjaman-permintaan', [PeminjamanController::class, 'indexPermintaan'])->name('peminjaman.permintaan');
        Route::put('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::put('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');

        Route::get('/peminjaman-aktif', [PeminjamanController::class, 'indexAktif'])->name('peminjaman.aktif');
        Route::put('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    });

    // =========================
    // OPERATOR
    // =========================
    Route::middleware('role:operator')->prefix('operator')->name('operator.')->group(function () {

        Route::get('/', [DashboardController::class, 'operatorDashboard'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // =========================
        // PEMINJAMAN (OPERATOR) ✅ sekali saja, tidak duplikat
        // =========================
        Route::get('/peminjaman-permintaan', [PeminjamanController::class, 'indexPermintaan'])->name('peminjaman.permintaan');
        Route::put('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::put('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');

        Route::get('/peminjaman-aktif', [PeminjamanController::class, 'indexAktif'])->name('peminjaman.aktif');
        Route::put('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    });

    // =========================
    // USER
    // =========================
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {

        Route::get('/', [DashboardController::class, 'userDashboard'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // peminjaman (user)
        Route::get('/sarpras-bisa-dipinjam', [PeminjamanController::class, 'available'])->name('peminjaman.available');
        Route::get('/peminjaman/create/{sarpras_id}', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayatUser'])->name('peminjaman.riwayat');
    });
});
