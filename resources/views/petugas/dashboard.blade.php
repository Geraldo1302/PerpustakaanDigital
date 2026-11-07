@extends('layouts.app')

@section('title', 'Dashboard Petugas - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>PETUGAS</span>
        <h2>Dashboard Petugas</h2>
        <p>Selamat datang, {{ auth()->user()->name }}. Jalankan operasional peminjaman dan pengembalian buku.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-stats">
            <div class="rb-stat"><span>Total Buku</span><strong>{{ $totalBuku }}</strong></div>
            <div class="rb-stat"><span>Menunggu Persetujuan</span><strong>{{ $menunggu }}</strong></div>
            <div class="rb-stat"><span>Sedang Dipinjam</span><strong>{{ $dipinjam }}</strong></div>
        </div>

        <div class="rb-card">
            <div class="rb-card-head">
                <h3>Pengajuan Terbaru</h3>
                @can('kelola-peminjaman')
                    <a href="{{ route('kelola.peminjaman.index', ['status' => 'diajukan']) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
                @endcan
            </div>

            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead>
                        <tr><th>Anggota</th><th>Buku</th><th>Tanggal</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($pengajuanTerbaru as $p)
                            <tr>
                                <td>{{ $p->anggota->nama ?? '-' }}</td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td class="aksi">
                                    @can('proses-persetujuan-peminjaman')
                                        <form method="POST" action="{{ route('kelola.peminjaman.setujui', $p) }}">@csrf
                                            <button class="btn btn-success btn-sm" type="submit">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('kelola.peminjaman.tolak', $p) }}">@csrf
                                            <button class="btn btn-danger btn-sm" type="submit">Tolak</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="rb-empty">Tidak ada pengajuan yang menunggu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-card">
            <h3>Menu Operasional</h3>
            <div class="rb-links">
                @can('kelola-data-buku')
                    <a href="{{ route('buku.index') }}" class="btn btn-primary">Kelola Data Buku</a>
                @endcan
                @can('kelola-peminjaman')
                    <a href="{{ route('kelola.peminjaman.index') }}" class="btn btn-primary">Proses Peminjaman</a>
                @endcan
                @can('kelola-pengembalian')
                    <a href="{{ route('kelola.pengembalian.index') }}" class="btn btn-primary">Proses Pengembalian</a>
                @endcan
                @can('lihat-laporan-perpustakaan')
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Laporan Perpustakaan</a>
                @endcan
            </div>
        </div>

    </div>
</section>

@endsection
