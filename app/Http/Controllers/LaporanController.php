<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\View\View;

/** Laporan perpustakaan (permission: lihat-laporan-perpustakaan). */
class LaporanController extends Controller
{
    public function index(): View
    {
        return view('laporan.index', [
            'totalBuku' => Buku::count(),
            'totalAnggota' => Anggota::count(),
            'totalPeminjaman' => Peminjaman::count(),
            'perStatus' => Peminjaman::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'bukuPerKategori' => Kategori::withCount('bukus')->orderBy('nama')->get(),
            'peminjamanPerBuku' => Buku::withCount('peminjamans')
                ->orderByDesc('peminjamans_count')
                ->orderBy('judul')
                ->take(10)
                ->get(),
        ]);
    }
}
