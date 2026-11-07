<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{

    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);

        // [Soal 1 - A1] Daftar judul yang dirender server (Blade) pada halaman daftar buku
        $bukus = Buku::with('kategori')->latest('id')->limit(10)->get();

        return view('buku.index', compact('kategoris', 'bukus'));
    }

    public function show($id)
    {
        $detailBuku = Buku::with('kategori')->findOrFail($id);

        return view('buku.show', compact('detailBuku'));
    }

    /**
     * [Soal 2b] Pencarian judul memakai query dengan binding (placeholder ?),
     * sehingga nilai "judul" selalu diperlakukan sebagai data, bukan potongan SQL.
     */
    public function cari(Request $request)
    {
        $judul = $request->query('judul');

        $hasil = DB::select('SELECT * FROM bukus WHERE judul = ?', [$judul]);

        return response()->json($hasil);
    }

    /**
     * [Soal 2c] Hapus buku lewat route DELETE (dilindungi token CSRF).
     */
    public function destroy(Buku $buku)
    {
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', "Buku \"{$judul}\" berhasil dihapus.");
    }
}