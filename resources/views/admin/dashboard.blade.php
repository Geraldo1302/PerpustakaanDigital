@extends('layouts.app')

@section('title', 'Dashboard Admin - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>ADMINISTRATOR</span>
        <h2>Dashboard Admin</h2>
        <p>Selamat datang, {{ auth()->user()->name }}. Kelola seluruh kebutuhan administrasi perpustakaan.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-stats">
            <div class="rb-stat"><span>Total Buku</span><strong>{{ $totalBuku }}</strong></div>
            <div class="rb-stat"><span>Kategori</span><strong>{{ $totalKategori }}</strong></div>
            <div class="rb-stat"><span>Pengguna</span><strong>{{ $totalPengguna }}</strong></div>
            <div class="rb-stat"><span>Menunggu Persetujuan</span><strong>{{ $menunggu }}</strong></div>
            <div class="rb-stat"><span>Sedang Dipinjam</span><strong>{{ $dipinjam }}</strong></div>
            <div class="rb-stat"><span>Sudah Dikembalikan</span><strong>{{ $dikembalikan }}</strong></div>
        </div>

        <div class="rb-card">
            <h3>Pengguna per Role</h3>
            <div class="rb-links">
                @foreach ($penggunaPerRole as $role)
                    <span class="rb-badge rb-badge-{{ $role->name }}">{{ ucfirst($role->name) }}: {{ $role->users_count }}</span>
                @endforeach
            </div>
        </div>

        <div class="rb-card">
            <h3>Menu Administrasi</h3>
            <div class="rb-links">
                @can('kelola-data-buku')
                    <a href="{{ route('buku.index') }}" class="btn btn-primary">Kelola Data Buku</a>
                @endcan
                @can('kelola-data-pengguna')
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Kelola Pengguna &amp; Role</a>
                @endcan
                @can('kelola-peminjaman')
                    <a href="{{ route('kelola.peminjaman.index') }}" class="btn btn-secondary">Data Peminjaman</a>
                @endcan
                @can('kelola-pengembalian')
                    <a href="{{ route('kelola.pengembalian.index') }}" class="btn btn-secondary">Data Pengembalian</a>
                @endcan
                @can('lihat-laporan-perpustakaan')
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Laporan Perpustakaan</a>
                @endcan
            </div>
        </div>

    </div>
</section>

@endsection
