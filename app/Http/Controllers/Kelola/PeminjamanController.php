<?php

namespace App\Http\Controllers\Kelola;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pengelolaan peminjaman oleh Admin/Petugas.
 * index          : permission kelola-peminjaman
 * setujui/tolak  : permission proses-persetujuan-peminjaman
 */
class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $peminjamans = Peminjaman::with(['anggota', 'buku'])
            ->when(
                in_array($status, [
                    Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM,
                    Peminjaman::STATUS_DIKEMBALIKAN, Peminjaman::STATUS_DITOLAK,
                ], true),
                fn ($q) => $q->where('status', $status)
            )
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('kelola.peminjaman', compact('peminjamans', 'status'));
    }

    public function setujui(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan berstatus "diajukan" yang dapat disetujui.');
        }

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIPINJAM,
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function tolak(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== Peminjaman::STATUS_DIAJUKAN) {
            return back()->with('error', 'Hanya pengajuan berstatus "diajukan" yang dapat ditolak.');
        }

        $peminjaman->update(['status' => Peminjaman::STATUS_DITOLAK]);

        return back()->with('success', 'Pengajuan peminjaman ditolak.');
    }
}
