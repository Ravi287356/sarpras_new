<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriSarprasController;
use App\Http\Controllers\SarprasController;
use App\Http\Controllers\SarprasItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SarprasAvailableController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\MaintenanceAlatController;
use App\Http\Controllers\LaporanController;

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
            'admin' => redirect()->route('admin.dashboard'),
            'operator' => redirect()->route('operator.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => abort(403),
        };
    })->name('dashboard');

    // SARPRAS TERSEDIA (semua role)
    Route::get('/sarpras-tersedia', [SarprasAvailableController::class, 'index'])
        ->name('sarpras.available');

    // =========================
    // PENGEMBALIAN (ADMIN & OPERATOR)
    // =========================
    Route::middleware('role:admin|operator')->group(function () {
        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::post('/pengembalian/search', [PengembalianController::class, 'searchPeminjaman'])->name('pengembalian.search');
        Route::get('/pengembalian/riwayat', [PengembalianController::class, 'riwayat'])->name('pengembalian.riwayat');
        Route::get('/pengembalian/{pengembalian}/detail', [PengembalianController::class, 'detail'])->name('pengembalian.detail');
        Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');
        Route::post('/pengembalian/store', [PengembalianController::class, 'store'])->name('pengembalian.store');


        // LAPORAN (shared between admin & operator)
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.export');
    });

    // =========================
    // ADMIN
    // =========================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // profil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');

        // user
        Route::get('/manage_user', [UserController::class, 'index'])->name('users.index');
        Route::get('/create_user', [UserController::class, 'create'])->name('users.create');
        Route::post('/manage_user', [UserController::class, 'store'])->name('users.store');
        Route::get('/manage_user/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/manage_user/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/manage_user/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/manage_user/trashed', [UserController::class, 'trashed'])->name('users.trashed');
        Route::post('/manage_user/{id}/restore', [UserController::class, 'restore'])->name('users.restore');

        // kategori sarpras
        Route::get('/kategori_sarpras', [KategoriSarprasController::class, 'index'])->name('kategori_sarpras.index');
        Route::get('/kategori_sarpras/create', [KategoriSarprasController::class, 'create'])->name('kategori_sarpras.create');
        Route::post('/kategori_sarpras', [KategoriSarprasController::class, 'store'])->name('kategori_sarpras.store');
        Route::get('/kategori_sarpras/{id}/edit', [KategoriSarprasController::class, 'edit'])->name('kategori_sarpras.edit');
        Route::put('/kategori_sarpras/{id}', [KategoriSarprasController::class, 'update'])->name('kategori_sarpras.update');
        Route::delete('/kategori_sarpras/{id}', [KategoriSarprasController::class, 'destroy'])->name('kategori_sarpras.destroy');
        Route::get('/kategori_sarpras/trashed', [KategoriSarprasController::class, 'trashed'])->name('kategori_sarpras.trashed');
        Route::post('/kategori_sarpras/{id}/restore', [KategoriSarprasController::class, 'restore'])->name('kategori_sarpras.restore');

        // sarpras
        Route::get('/sarpras', [SarprasController::class, 'index'])->name('sarpras.index');
        Route::get('/sarpras/create', [SarprasController::class, 'create'])->name('sarpras.create');
        Route::post('/sarpras', [SarprasController::class, 'store'])->name('sarpras.store');
        Route::get('/sarpras/{sarpras}/edit', [SarprasController::class, 'edit'])->name('sarpras.edit');
        Route::put('/sarpras/{sarpras}', [SarprasController::class, 'update'])->name('sarpras.update');
        Route::delete('/sarpras/{sarpras}', [SarprasController::class, 'destroy'])->name('sarpras.destroy');
        Route::get('/sarpras/{sarpras}/items', [SarprasController::class, 'items'])->name('sarpras.items');

        // sarpras items
        Route::get('/sarpras-item/trashed', [SarprasItemController::class, 'trashed'])->name('sarpras_item.trashed');
        Route::post('/sarpras-item/{id}/restore', [SarprasItemController::class, 'restore'])->name('sarpras_item.restore');
        Route::get('/sarpras/{sarpras}/items/create', [SarprasItemController::class, 'create'])->name('sarpras_item.create');
        Route::post('/sarpras/{sarpras}/items', [SarprasItemController::class, 'store'])->name('sarpras_item.store');
        Route::get('/sarpras-item/{sarprasItem}/edit', [SarprasItemController::class, 'edit'])->name('sarpras_item.edit');
        Route::put('/sarpras-item/{sarprasItem}', [SarprasItemController::class, 'update'])->name('sarpras_item.update');
        Route::delete('/sarpras-item/{sarprasItem}', [SarprasItemController::class, 'destroy'])->name('sarpras_item.destroy');

        // lokasi
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
        Route::get('/lokasi/create', [LokasiController::class, 'create'])->name('lokasi.create');
        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
        Route::get('/lokasi/{lokasi}/edit', [LokasiController::class, 'edit'])->name('lokasi.edit');
        Route::put('/lokasi/{lokasi}', [LokasiController::class, 'update'])->name('lokasi.update');
        Route::delete('/lokasi/{lokasi}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
        Route::get('/lokasi/trashed', [LokasiController::class, 'trashed'])->name('lokasi.trashed');
        Route::post('/lokasi/{id}/restore', [LokasiController::class, 'restore'])->name('lokasi.restore');

        // =========================
        // PEMINJAMAN (ADMIN) ✅ sekali saja, tidak duplikat
        // =========================
        Route::get('/peminjaman-permintaan', [PeminjamanController::class, 'indexPermintaan'])->name('peminjaman.permintaan');
        Route::put('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
        Route::put('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');

        Route::get('/peminjaman-aktif', [PeminjamanController::class, 'indexAktif'])->name('peminjaman.aktif');
        Route::put('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::get('/peminjaman/{id}/struk', [PeminjamanController::class, 'struk'])->name('peminjaman.struk');
        Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');

        // PENGADUAN
        Route::get('/pengaduan', [PengaduanController::class, 'index'])
            ->name('pengaduan.index'); // list semua pengaduan
        Route::get('/pengaduan/export', [PengaduanController::class, 'exportCSV'])
            ->name('pengaduan.export');

        Route::put('/pengaduan/{pengaduan}', [PengaduanController::class, 'updateStatus'])
            ->name('pengaduan.updateStatus'); // update status

        Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show'])
            ->name('pengaduan.show'); // detail pengaduan (opsional)

        Route::get('/pengaduan/{pengaduan}/respond', [PengaduanController::class, 'respond'])
            ->name('pengaduan.respond'); // halaman respond

        // MAINTENANCE ALAT
        Route::get('/maintenance', [MaintenanceAlatController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/{item}/start', [MaintenanceAlatController::class, 'startMaintenance'])->name('maintenance.start');
        Route::post('/maintenance/{item}/finish', [MaintenanceAlatController::class, 'finishMaintenance'])->name('maintenance.finish');
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
        Route::get('/peminjaman/{id}/struk', [PeminjamanController::class, 'struk'])->name('peminjaman.struk');
        Route::get('/peminjaman/export', [PeminjamanController::class, 'exportCSV'])->name('peminjaman.export');
        Route::get('/riwayat-peminjaman', [PeminjamanController::class, 'riwayat'])->name('peminjaman.riwayat');

        // PENGADUAN
        Route::get('/pengaduan', [PengaduanController::class, 'index'])
            ->name('pengaduan.index'); // list semua pengaduan
        Route::get('/pengaduan/export', [PengaduanController::class, 'exportCSV'])
            ->name('pengaduan.export');
        Route::get('/pengaduan/{pengaduan}/respond', [PengaduanController::class, 'respond'])
            ->name('pengaduan.respond'); // halaman respond
        Route::put('/pengaduan/{pengaduan}', [PengaduanController::class, 'updateStatus'])
            ->name('pengaduan.updateStatus'); // update status

        Route::get('/pengaduan/{pengaduan}', [PengaduanController::class, 'show'])
            ->name('pengaduan.show'); // detail pengaduan (opsional)

        // Data Sarpras (read-only untuk operator)
        Route::get('/sarpras', [SarprasController::class, 'index'])->name('sarpras.index');
        Route::get('/sarpras/{sarpras}/items', [SarprasController::class, 'items'])->name('sarpras.items');

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
        Route::get('/peminjaman/{id}/struk', [PeminjamanController::class, 'struk'])->name('peminjaman.struk');

        // =========================
        // PENGADUAN (USER)
        // =========================
        Route::get('/pengaduan/create', [PengaduanController::class, 'create'])
            ->name('pengaduan.create');

        Route::post('/pengaduan', [PengaduanController::class, 'store'])
            ->name('pengaduan.store');

        Route::get('/pengaduan/riwayat', [PengaduanController::class, 'riwayatUser'])
            ->name('pengaduan.riwayat');

        // Data Sarpras (read-only untuk user)
        Route::get('/sarpras', [SarprasController::class, 'index'])->name('sarpras.index');
        Route::get('/sarpras/{sarpras}/items', [SarprasController::class, 'items'])->name('sarpras.items');

    });
});
