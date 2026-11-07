<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use App\Support\PesanValidasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Peminjaman dari sisi pengguna: mengajukan peminjaman serta melihat
 * status & riwayat. Data pribadi diperiksa berdasarkan IDENTITAS pengguna
 * yang sedang login, bukan hanya berdasarkan permission.
 */
class PeminjamanController extends Controller
{
    /** Ajukan peminjaman buku (permission: ajukan-peminjaman-buku). */
    public function ajukan(Request $request): RedirectResponse
    {
        $request->validate([
            'buku_id' => ['required', 'exists:bukus,id'],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        $anggota = $request->user()->pastikanAnggota();
        $buku = Buku::findOrFail($request->buku_id);

        // Satu buku tidak boleh diajukan ganda selama masih diajukan / dipinjam
        $sudahAda = Peminjaman::where('anggota_id', $anggota->id)
            ->where('buku_id', $buku->id)
            ->whereIn('status', [Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', "Buku \"{$buku->judul}\" sudah Anda ajukan atau sedang Anda pinjam.");
        }

        Peminjaman::create([
            'anggota_id' => $anggota->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'status' => Peminjaman::STATUS_DIAJUKAN,
        ]);

        return redirect()->route('peminjaman.saya')
            ->with('success', "Pengajuan peminjaman \"{$buku->judul}\" berhasil dikirim. Menunggu persetujuan petugas.");
    }

    /** Status & riwayat peminjaman milik akun yang sedang login. */
    public function saya(Request $request): View
    {
        return $this->tampilkan($request->user());
    }

    /**
     * Riwayat peminjaman milik pengguna tertentu.
     * - Pemilik data  : boleh.
     * - Pengguna lain : DITOLAK (403), kecuali staf yang punya permission "kelola-peminjaman".
     */
    public function riwayat(Request $request, User $user): View
    {
        $pemilik = $request->user()->is($user);

        abort_unless(
            $pemilik || $request->user()->can('kelola-peminjaman'),
            403,
            'Anda tidak berhak melihat riwayat peminjaman pengguna lain.'
        );

        return $this->tampilkan($user);
    }

    /**
     * [Soal 4] Detail satu peminjaman. Kepemilikan diperiksa oleh PeminjamanPolicy@view:
     * admin/petugas boleh semua, siswa hanya miliknya sendiri (selain itu 403).
     */
    public function show($id): View
    {
        $peminjaman = Peminjaman::with(['buku.kategori', 'anggota'])->findOrFail($id);

        Gate::authorize('view', $peminjaman);

        return view('peminjaman.show', compact('peminjaman'));
    }

    private function tampilkan(User $pemilik): View
    {
        $semua = Peminjaman::with('buku.kategori')
            ->whereHas('anggota', fn ($q) => $q->where('user_id', $pemilik->id))
            ->latest('id')
            ->get();

        return view('peminjaman.saya', [
            'pemilik' => $pemilik,
            'sendiri' => auth()->id() === $pemilik->id,
            'aktif' => $semua->whereIn('status', [Peminjaman::STATUS_DIAJUKAN, Peminjaman::STATUS_DIPINJAM]),
            'riwayat' => $semua->whereIn('status', [Peminjaman::STATUS_DIKEMBALIKAN, Peminjaman::STATUS_DITOLAK]),
        ]);
    }
}
