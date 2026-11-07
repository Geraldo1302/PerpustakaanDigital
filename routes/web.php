<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\BukuController as ApiBukuController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kelola\PeminjamanController as KelolaPeminjamanController;
use App\Http\Controllers\Kelola\PengembalianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Halaman yang dilindungi (wajib login)  ->  middleware "auth"
|--------------------------------------------------------------------------
| Di dalamnya setiap route/grup dibatasi lagi dengan middleware Spatie:
|   permission:<nama-permission>   atau   role:<nama-role>
*/
Route::middleware('auth')->group(function () {

    // Pengarah dashboard sesuai role
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ---- Daftar & detail buku (admin, petugas, siswa) ----
    Route::middleware('permission:lihat-daftar-buku')->group(function () {
        Route::get('/buku', [BukuController::class, 'index'])
            ->name('buku.index');

        Route::get('/buku/{id}', [BukuController::class, 'show'])
            ->name('buku.show');
    });

    // ---- Profil SENDIRI (tanpa parameter ID pengguna) ----
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->middleware('permission:lihat-profil-sendiri')
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->middleware('permission:ubah-profil-sendiri')
        ->name('profile.update');

    // ---- Peminjaman dari sisi pengguna ----
    Route::post('/peminjaman/ajukan', [PeminjamanController::class, 'ajukan'])
        ->middleware('permission:ajukan-peminjaman-buku')
        ->name('peminjaman.ajukan');

    Route::get('/peminjaman-saya', [PeminjamanController::class, 'saya'])
        ->middleware('permission:lihat-status-peminjaman-sendiri|lihat-riwayat-peminjaman-sendiri')
        ->name('peminjaman.saya');

    // Riwayat milik pengguna tertentu: pemilik -> boleh; pengguna lain -> 403 (dicek di controller)
    Route::get('/riwayat-peminjaman/{user}', [PeminjamanController::class, 'riwayat'])
        ->middleware('permission:lihat-riwayat-peminjaman-sendiri|kelola-peminjaman')
        ->name('peminjaman.riwayat');

    // ---- Dashboard & manajemen khusus ADMIN ----
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware(['role:admin', 'permission:akses-dashboard-admin'])
            ->name('dashboard');

        // Kelola akun pengguna
        Route::resource('users', UserController::class)
            ->except('show')
            ->middleware('permission:kelola-data-pengguna');

        // Tetapkan / ubah role pengguna
        Route::patch('users/{user}/role', [UserController::class, 'updateRole'])
            ->middleware(['permission:kelola-data-pengguna', 'permission:kelola-role-pengguna'])
            ->name('users.role');
    });

    // ---- Dashboard khusus PETUGAS ----
    Route::get('/petugas/dashboard', [PetugasDashboardController::class, 'index'])
        ->middleware(['role:petugas', 'permission:akses-dashboard-petugas'])
        ->name('petugas.dashboard');

    // ---- Operasional perpustakaan (admin & petugas) ----
    Route::prefix('kelola')->name('kelola.')->group(function () {
        Route::get('/peminjaman', [KelolaPeminjamanController::class, 'index'])
            ->middleware('permission:kelola-peminjaman')
            ->name('peminjaman.index');

        Route::post('/peminjaman/{peminjaman}/setujui', [KelolaPeminjamanController::class, 'setujui'])
            ->middleware('permission:proses-persetujuan-peminjaman')
            ->name('peminjaman.setujui');

        Route::post('/peminjaman/{peminjaman}/tolak', [KelolaPeminjamanController::class, 'tolak'])
            ->middleware('permission:proses-persetujuan-peminjaman')
            ->name('peminjaman.tolak');

        Route::get('/pengembalian', [PengembalianController::class, 'index'])
            ->middleware('permission:kelola-pengembalian')
            ->name('pengembalian.index');

        Route::post('/pengembalian/{peminjaman}/catat', [PengembalianController::class, 'catat'])
            ->middleware('permission:catat-pengembalian-buku')
            ->name('pengembalian.catat');
    });

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('permission:lihat-laporan-perpustakaan')
        ->name('laporan.index');

    // ---- [Soal 1] Route RENTAN (sengaja, untuk audit keamanan) ----
    // A2: SQL Injection
    Route::get('/buku-cari', [BukuController::class, 'cari'])->name('buku.cari');
    // A3: CSRF (penghapusan lewat GET)
    Route::get('/buku/{buku}/hapus', [ApiBukuController::class, 'destroy'])->name('buku.hapus');
    // A4: IDOR
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');
});

// Route autentikasi (register, login, logout, lupa/reset password)
require __DIR__.'/auth.php';
