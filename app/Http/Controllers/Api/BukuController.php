<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    /**
     * Aturan validasi dipakai bersama oleh store() dan update().
     */
    private function rules(): array
    {
        $tahunSaatIni = date('Y');

        return [
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'judul' => ['required', 'string', 'min:3', 'max:150'],
            'penulis' => ['required', 'string', 'min:3', 'max:100'],
            'tahun_terbit' => ['required', 'integer', "between:1900,{$tahunSaatIni}"],
        ];
    }

    private function messages(): array
    {
        $tahun = date('Y');

        return [
            'kategori_id.required' => 'Kategori wajib diisi.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.min' => 'Judul buku minimal :min karakter.',
            'judul.max' => 'Judul buku maksimal :max karakter.',
            'penulis.required' => 'Penulis wajib diisi.',
            'penulis.min' => 'Penulis minimal :min karakter.',
            'penulis.max' => 'Penulis maksimal :max karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa bilangan bulat.',
            'tahun_terbit.between' => "Tahun terbit harus antara 1900 sampai {$tahun}.",
        ];
    }

    public function index(Request $request)
    {
        $query = Buku::with('kategori')->withCount('peminjamans');

        // Pencarian (Judul / Penulis)
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('judul', 'like', "%{$cari}%")
                    ->orWhere('penulis', 'like', "%{$cari}%");
            });
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Filter Tahun
        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        $bukus = $query->latest('id')->paginate(10)->withQueryString();

        // Struktur respons diatur oleh API Resource (data + links + meta)
        return BukuResource::collection($bukus);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        $buku = Buku::create($validated);
        $buku->load('kategori');

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => new BukuResource($buku),
        ], 201);
    }

    public function show($id)
    {
        $buku = Buku::with('kategori')->withCount('peminjamans')->find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json(['data' => new BukuResource($buku)], 200);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $validated = $request->validate($this->rules(), $this->messages());

        $buku->update($validated);
        $buku->load('kategori');

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => new BukuResource($buku),
        ], 200);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus'], 200);
    }
}