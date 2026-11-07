<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('petugas.dashboard', [
            'totalBuku' => Buku::count(),
            'menunggu' => Peminjaman::where('status', Peminjaman::STATUS_DIAJUKAN)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
            'pengajuanTerbaru' => Peminjaman::with(['anggota', 'buku'])
                ->where('status', Peminjaman::STATUS_DIAJUKAN)
                ->latest('id')
                ->take(5)
                ->get(),
        ]);
    }
}
