@extends('layouts.app')

@section('title', 'Data Peminjaman - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>OPERASIONAL</span>
        <h2>Data Peminjaman</h2>
        <p>Pantau seluruh peminjaman dan proses persetujuan pengajuan dari anggota.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">
        <div class="rb-card">

            <div class="rb-card-head">
                <h3>Semua Peminjaman</h3>
                <div class="rb-links">
                    <a href="{{ route('kelola.peminjaman.index') }}" class="btn btn-sm {{ $status ? 'btn-secondary' : 'btn-primary' }}">Semua</a>
                    @foreach (['diajukan', 'dipinjam', 'dikembalikan', 'ditolak'] as $s)
                        <a href="{{ route('kelola.peminjaman.index', ['status' => $s]) }}"
                           class="btn btn-sm {{ $status === $s ? 'btn-primary' : 'btn-secondary' }}">{{ ucfirst($s) }}</a>
                    @endforeach
                </div>
            </div>

            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead>
                        <tr>
                            <th>No</th><th>Anggota</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamans as $p)
                            <tr>
                                <td>{{ $peminjamans->firstItem() + $loop->index }}</td>
                                <td>
                                    {{ $p->anggota->nama ?? '-' }}
                                    @if ($p->anggota?->user_id)
                                        <br><a href="{{ route('peminjaman.riwayat', $p->anggota->user_id) }}" class="rb-muted">lihat riwayat</a>
                                    @endif
                                </td>
                                <td>{{ $p->buku->judul ?? '-' }}</td>
                                <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
                                <td>{{ $p->tanggal_kembali?->format('d/m/Y') ?? '-' }}</td>
                                <td><span class="rb-badge rb-badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                                <td class="aksi">
                                    @if ($p->status === 'diajukan')
                                        @can('proses-persetujuan-peminjaman')
                                            <form method="POST" action="{{ route('kelola.peminjaman.setujui', $p) }}">@csrf
                                                <button class="btn btn-success btn-sm" type="submit">Setujui</button>
                                            </form>
                                            <form method="POST" action="{{ route('kelola.peminjaman.tolak', $p) }}">@csrf
                                                <button class="btn btn-danger btn-sm" type="submit">Tolak</button>
                                            </form>
                                        @endcan
                                    @else
                                        <span class="rb-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="rb-empty">Belum ada data peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rb-pagination">{{ $peminjamans->links('partials.pagination') }}</div>
        </div>
    </div>
</section>

@endsection
