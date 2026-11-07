@extends('layouts.app')

@section('title', ($buku->exists ? 'Ubah Buku' : 'Tambah Buku').' - Perpustakaan Digital')

@section('content')

<style>
    .panel { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:32px; margin-bottom:32px; box-shadow:0 1px 3px rgba(15,23,42,.06); max-width:640px; }
    .form-group { margin-bottom:18px; }
    .form-group label { display:block; margin-bottom:8px; font-size:13px; font-weight:700; color:#475569; }
    .form-group input, .form-group select { width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font:inherit; font-size:14px; background:#fff; }
    .field-error { display:block; margin-top:6px; font-size:12px; color:#dc2626; }
    .form-actions { display:flex; gap:12px; margin-top:8px; }
</style>

<section class="page-header">
    <div class="container">
        <span>KOLEKSI PERPUSTAKAAN</span>
        <h2>{{ $buku->exists ? 'Ubah Buku' : 'Tambah Buku' }}</h2>
        <p>{{ $buku->exists ? 'Perbarui data buku yang dipilih.' : 'Tambahkan buku baru ke koleksi perpustakaan.' }}</p>
    </div>
</section>

<section class="book-section">
    <div class="container">
        <div class="panel">
            <form method="POST" action="{{ $buku->exists ? route('buku.update', $buku->id) : route('buku.store') }}">
                @csrf
                @if ($buku->exists)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="kategori_id">Kategori</label>
                    <select id="kategori_id" name="kategori_id">
                        <option value="">-- Pilih kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" @selected(old('kategori_id', $buku->kategori_id) == $kategori->id)>{{ $kategori->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id') <small class="field-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" maxlength="150" value="{{ old('judul', $buku->judul) }}">
                    @error('judul') <small class="field-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="penulis">Penulis</label>
                    <input type="text" id="penulis" name="penulis" maxlength="100" value="{{ old('penulis', $buku->penulis) }}">
                    @error('penulis') <small class="field-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="tahun_terbit">Tahun Terbit</label>
                    <input type="number" id="tahun_terbit" name="tahun_terbit" min="1900" max="{{ date('Y') }}" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}">
                    @error('tahun_terbit') <small class="field-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ $buku->exists ? 'Perbarui' : 'Simpan' }}</button>
                    <a href="{{ route('buku.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
