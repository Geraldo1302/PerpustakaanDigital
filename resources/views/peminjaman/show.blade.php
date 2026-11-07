@extends('layouts.app')

@section('title', 'Detail Peminjaman - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>DETAIL PEMINJAMAN</span>
        <h2>Peminjaman #{{ $peminjaman->id }}</h2>
        <p>Rincian satu transaksi peminjaman buku.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">
        <div class="rb-card">
            <h3>Data Peminjam</h3>
            <div class="rb-table-wrap">
                <table class="rb-table">
                    <tbody>
                        <tr><th>Nama</th><td>{{ $peminjaman->anggota->nama ?? '-' }}</td></tr>
                        <tr><th>Email</th><td>{{ $peminjaman->anggota->email ?? '-' }}</td></tr>
                        <tr><th>No. Telepon</th><td>{{ $peminjaman->anggota->no_telepon ?? '-' }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-card">
            <h3>Data Peminjaman</h3>
            <div class="rb-table-wrap">
                <table class="rb-table">
                    <tbody>
                        <tr><th>Judul Buku</th><td>{{ $peminjaman->buku->judul ?? '-' }}</td></tr>
                        <tr><th>Kategori</th><td>{{ $peminjaman->buku->kategori->nama ?? '-' }}</td></tr>
                        <tr><th>Tanggal Pinjam</th><td>{{ $peminjaman->tanggal_pinjam?->format('d/m/Y') }}</td></tr>
                        <tr><th>Tanggal Kembali</th><td>{{ $peminjaman->tanggal_kembali?->format('d/m/Y') ?? '-' }}</td></tr>
                        <tr><th>Status</th><td><span class="rb-badge rb-badge-{{ $peminjaman->status }}">{{ ucfirst($peminjaman->status) }}</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
    </div>
</section>

@endsection
