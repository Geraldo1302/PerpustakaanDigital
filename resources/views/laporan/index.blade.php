@extends('layouts.app')

@section('title', 'Laporan Perpustakaan - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>LAPORAN</span>
        <h2>Laporan Perpustakaan</h2>
        <p>Ringkasan koleksi, anggota, dan aktivitas peminjaman.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-stats">
            <div class="rb-stat"><span>Total Buku</span><strong>{{ $totalBuku }}</strong></div>
            <div class="rb-stat"><span>Total Anggota</span><strong>{{ $totalAnggota }}</strong></div>
            <div class="rb-stat"><span>Total Peminjaman</span><strong>{{ $totalPeminjaman }}</strong></div>
            @foreach (['diajukan', 'dipinjam', 'dikembalikan', 'ditolak'] as $s)
                <div class="rb-stat"><span>{{ ucfirst($s) }}</span><strong>{{ $perStatus[$s] ?? 0 }}</strong></div>
            @endforeach
        </div>

        <div class="rb-card">
            <h3>Jumlah Buku per Kategori</h3>
            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead><tr><th>No</th><th>Kategori</th><th>Jumlah Buku</th></tr></thead>
                    <tbody>
                        @forelse ($bukuPerKategori as $k)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $k->nama }}</td><td>{{ $k->bukus_count }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="rb-empty">Belum ada kategori.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-card">
            <h3>Buku Paling Sering Dipinjam</h3>
            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead><tr><th>No</th><th>Judul</th><th>Penulis</th><th>Jumlah Peminjaman</th></tr></thead>
                    <tbody>
                        @forelse ($peminjamanPerBuku as $b)
                            <tr><td>{{ $loop->iteration }}</td><td>{{ $b->judul }}</td><td>{{ $b->penulis }}</td><td>{{ $b->peminjamans_count }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="rb-empty">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

@endsection
