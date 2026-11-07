@extends('layouts.app')

@section('title', 'Peminjaman - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>{{ $sendiri ? 'PEMINJAMAN SAYA' : 'RIWAYAT ANGGOTA' }}</span>
        <h2>{{ $sendiri ? 'Peminjaman Saya' : 'Riwayat '.$pemilik->name }}</h2>
        <p>{{ $sendiri ? 'Status dan riwayat peminjaman buku milik Anda.' : 'Riwayat peminjaman (hanya dapat dilihat oleh staf perpustakaan).' }}</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        @if ($sendiri)
            @can('lihat-status-peminjaman-sendiri')
                <div class="rb-card">
                    <div class="rb-card-head">
                        <h3>Status Peminjaman</h3>
                        @can('lihat-daftar-buku')
                            <a href="{{ route('buku.index') }}" class="btn btn-primary btn-sm">Pinjam Buku Lain</a>
                        @endcan
                    </div>

                    @include('peminjaman._tabel', ['data' => $aktif, 'kosong' => 'Tidak ada pengajuan atau peminjaman yang sedang berjalan.'])
                </div>
            @endcan

            @can('lihat-riwayat-peminjaman-sendiri')
                <div class="rb-card">
                    <h3>Riwayat Peminjaman</h3>
                    @include('peminjaman._tabel', ['data' => $riwayat, 'kosong' => 'Belum ada riwayat peminjaman.'])
                </div>
            @endcan
        @else
            <div class="rb-card">
                <h3>Peminjaman Berjalan</h3>
                @include('peminjaman._tabel', ['data' => $aktif, 'kosong' => 'Tidak ada peminjaman berjalan.'])
            </div>

            <div class="rb-card">
                <h3>Riwayat Peminjaman</h3>
                @include('peminjaman._tabel', ['data' => $riwayat, 'kosong' => 'Belum ada riwayat peminjaman.'])
            </div>
        @endif

    </div>
</section>

@endsection
