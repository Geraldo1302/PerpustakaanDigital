@extends('layouts.app')

@section('title', 'Data Pengembalian - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>OPERASIONAL</span>
        <h2>Data Pengembalian</h2>
        <p>Catat pengembalian buku yang sedang dipinjam.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-card">
            <h3>Belum Dikembalikan</h3>

            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead>
                        <tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($belumKembali as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->anggota->nama ?? '-' }}</td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td class="aksi">
                                    @can('catat-pengembalian-buku')
                                        <form method="POST" action="{{ route('kelola.pengembalian.catat', $p) }}"
                                              onsubmit="return confirm('Catat pengembalian buku ini?')">
                                            @csrf
                                            <button class="btn btn-success btn-sm" type="submit">Catat Pengembalian</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="rb-empty">Tidak ada buku yang sedang dipinjam.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rb-card">
            <h3>Pengembalian Terakhir</h3>

            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead>
                        <tr><th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($sudahKembali as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->anggota->nama ?? '-' }}</td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td>{{ $p->tanggal_kembali?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="rb-empty">Belum ada pengembalian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

@endsection
