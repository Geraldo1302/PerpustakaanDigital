<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Pengelolaan pengembalian oleh Admin/Petugas.
 * index : permission kelola-pengembalian
 * catat : permission catat-pengembalian-buku
 */
class PengembalianController extends Controller
{
    public function index(): View
    {
        return view('kelola.pengembalian', [
            'belumKembali' => Peminjaman::with(['anggota', 'buku'])
                ->where('status', Peminjaman::STATUS_DIPINJAM)
                ->orderBy('tanggal_pinjam')
                ->get(),
            'sudahKembali' => Peminjaman::with(['anggota', 'buku'])
                ->where('status', Peminjaman::STATUS_DIKEMBALIKAN)
                ->latest('tanggal_kembali')
                ->take(10)
                ->get(),
        ]);
    }

    public function catat(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIPINJAM) {
            return back()->with('error', 'Hanya buku berstatus "dipinjam" yang dapat dicatat pengembaliannya.');
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
            'tanggal_kembali' => now()->toDateString(),
        ]);

        return back()->with('success', 'Pengembalian buku berhasil dicatat.');
    }
}
