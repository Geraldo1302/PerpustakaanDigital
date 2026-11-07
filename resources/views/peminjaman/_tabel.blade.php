<div class="rb-table-wrap">
    <table class="rb-table">
        <thead>
            <tr><th>No</th><th>Judul Buku</th><th>Kategori</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th></tr>
        </thead>
        <tbody>
            @forelse ($data as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->buku->judul ?? '-' }}</td>
                    <td>{{ $p->buku->kategori->nama ?? '-' }}</td>
                    <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
                    <td>{{ $p->tanggal_kembali?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="rb-badge rb-badge-{{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="rb-empty">{{ $kosong }}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
