<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;

class BukuController extends Controller
{

    public function index()
    {
        $kategoris = Kategori::orderBy('nama')->get(['id', 'nama']);

        return view('buku.index', compact('kategoris'));
    }

    public function show($id)
    {
        $detailBuku = Buku::with('kategori')->findOrFail($id);

        return view('buku.show', compact('detailBuku'));
    }
}