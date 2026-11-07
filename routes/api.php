<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\RekapController;

/*
|--------------------------------------------------------------------------
| API Buku & Rekap
|--------------------------------------------------------------------------
| Endpoint ini dipanggil oleh halaman /buku (fetch dari browser), sehingga
| dilindungi memakai session login ("web" + "auth") lalu dibatasi permission.
| Tanpa login        -> 401 (JSON)
| Login tanpa izin   -> 403 (JSON)
*/
Route::middleware(['web', 'auth'])->group(function () {

    // CRUD API Buku (nama route diberi prefix "api." supaya tidak bentrok dengan route web)
    // Baca data buku: admin, petugas, siswa
    Route::apiResource('buku', BukuController::class)
        ->only(['index', 'show'])
        ->middleware('permission:lihat-daftar-buku')
        ->names('api.buku');

    // Tambah / ubah / hapus data buku: admin & petugas
    // [Soal 3] sebelumnya permission:kelola-data-buku, kini memakai Gate kelola-buku
    Route::apiResource('buku', BukuController::class)
        ->only(['store', 'update', 'destroy'])
        ->middleware('can:kelola-buku')
        ->names('api.buku');

    // Rekap Agregasi Data (laporan): admin & petugas
    Route::middleware('permission:lihat-laporan-perpustakaan')->group(function () {
        Route::get('rekap/buku-per-kategori', [RekapController::class, 'bukuPerKategori']);
        Route::get('rekap/peminjaman-per-buku', [RekapController::class, 'peminjamanPerBuku']);
    });
});
