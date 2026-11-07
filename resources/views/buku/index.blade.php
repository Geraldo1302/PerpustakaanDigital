@extends('layouts.app')

@section('title', 'Daftar Buku - Perpustakaan Digital')

@section('content')

<style>
    [hidden] { display: none !important; }

    /* ---------- Panel ---------- */
    .panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, .06);
    }
    .panel > h3, .table-head h3 { font-size: 20px; margin-bottom: 20px; }

    /* ---------- Formulir ---------- */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font: inherit;
        font-size: 14px;
        background: #fff;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px #dbeafe;
    }
    .form-group .is-invalid { border-color: #dc2626; }
    .field-error {
        display: block;
        min-height: 18px;
        margin-top: 6px;
        font-size: 12px;
        color: #dc2626;
    }
    .form-actions { display: flex; gap: 12px; margin-top: 16px; }
    .form-actions .btn { cursor: pointer; border: 0; padding: 12px 28px; }
    .form-actions .btn-secondary { border: 1px solid #cbd5e1; }
    .form-actions .btn:disabled { opacity: .6; cursor: not-allowed; }

    /* ---------- Pesan ---------- */
    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 24px;
        font-size: 14px;
        font-weight: 600;
    }
    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

    /* ---------- Tabel ---------- */
    .table-head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 4px;
    }
    .table-head p { color: #64748b; font-size: 14px; margin-bottom: 20px; }

    .table-wrap {
        overflow-x: auto;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        min-width: 720px;
    }
    .table th,
    .table td {
        padding: 14px 18px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #e2e8f0;
    }
    .table thead th {
        background: #172033;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .6px;
        text-transform: uppercase;
        border-color: #172033;
        white-space: nowrap;
    }
    .table tbody tr:nth-child(even) { background: #f8fafc; }
    .table tbody tr:hover { background: #eff6ff; }
    .table th:first-child, .table td:first-child { width: 60px; text-align: center; }
    .table th:nth-child(5), .table td:nth-child(5) { width: 130px; text-align: center; }
    .table th:last-child,  .table td:last-child  { width: 170px; text-align: center; }
    .table td:nth-child(3) { font-weight: 600; }

    .table-empty { text-align: center !important; color: #64748b; padding: 32px !important; }
    .table-error { color: #dc2626; }

    .table td.aksi { white-space: nowrap; }
    .btn-aksi {
        border: 0;
        border-radius: 8px;
        padding: 8px 16px;
        margin: 0 3px;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        color: #fff;
        transition: .2s ease;
    }
    .btn-ubah  { background: #2563eb; }
    .btn-hapus { background: #dc2626; }
    .btn-aksi:hover { opacity: .85; transform: translateY(-1px); }

    /* ---------- Pagination ---------- */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 16px;
        margin-top: 24px;
        font-size: 14px;
        color: #475569;
    }
    .pagination .btn { padding: 10px 18px; cursor: pointer; }
    .pagination .btn:disabled { opacity: .5; cursor: not-allowed; }

    @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 560px) { .form-grid { grid-template-columns: 1fr; } .panel { padding: 20px; } }
</style>


<section class="page-header">
    <div class="container">
        <span>KOLEKSI PERPUSTAKAAN</span>
        <h2>Daftar Buku</h2>
        <p>@can('kelola-data-buku')Kelola koleksi buku perpustakaan digital (data diambil melalui RESTful API).@else Jelajahi koleksi buku perpustakaan digital.@endcan</p>
    </div>
</section>

<section class="book-section">
    <div class="container">

        {{-- Area pesan (berhasil / gagal) --}}
        <div id="alert" class="alert" role="alert" hidden></div>

        @can('kelola-data-buku')
        {{-- FORMULIR TAMBAH / UBAH --}}
        <div class="panel">
            <h3 id="form-title">Tambah Buku</h3>

            <form id="form-buku" novalidate>
                <input type="hidden" id="buku-id">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="kategori_id">Kategori</label>
                        <select id="kategori_id" name="kategori_id">
                            <option value="">-- Pilih kategori --</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                            @endforeach
                        </select>
                        <small class="field-error" data-error-for="kategori_id"></small>
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul</label>
                        <input type="text" id="judul" name="judul" maxlength="150">
                        <small class="field-error" data-error-for="judul"></small>
                    </div>

                    <div class="form-group">
                        <label for="penulis">Penulis</label>
                        <input type="text" id="penulis" name="penulis" maxlength="100">
                        <small class="field-error" data-error-for="penulis"></small>
                    </div>

                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" min="1900" max="{{ date('Y') }}">
                        <small class="field-error" data-error-for="tahun_terbit"></small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" id="btn-simpan" class="btn btn-primary">Simpan</button>
                    <button type="button" id="btn-batal" class="btn btn-secondary" hidden>Batal</button>
                </div>
            </form>
        </div>

        @endcan

        {{-- TABEL BUKU --}}
        <div class="panel">
            <div class="table-head">
                <h3>Semua Koleksi</h3>
                <p id="info-total">Memuat data...</p>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun Terbit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabel-buku">
                        <tr><td colspan="6" class="table-empty">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination" id="pagination" hidden>
                <button type="button" class="btn btn-secondary" id="btn-prev">&larr; Sebelumnya</button>
                <span id="info-halaman"></span>
                <button type="button" class="btn btn-secondary" id="btn-next">Berikutnya &rarr;</button>
            </div>
        </div>

        {{-- [Soal 2a] Judul ditampilkan dengan escaping Blade {{ }} (sebelumnya {!! !!} -> XSS) --}}
        <div class="panel">
            <div class="table-head">
                <h3>Judul Buku Terbaru</h3>
                <p>10 buku terakhir (dirender oleh server)</p>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun Terbit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bukus as $buku)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $buku->kategori->nama ?? '-' }}</td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->penulis }}</td>
                                <td>{{ $buku->tahun_terbit }}</td>
                                <td class="aksi">
                                    <a href="{{ route('buku.show', $buku->id) }}" class="btn-aksi btn-detail">Detail</a>

                                    {{-- [Soal 2c] Hapus lewat form DELETE + token CSRF (bukan tautan GET) --}}
                                    @can('kelola-data-buku')
                                        <form method="POST" action="{{ route('buku.destroy', $buku->id) }}" style="display:inline"
                                              onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-aksi btn-hapus">Hapus</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="table-empty">Belum ada buku yang tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<script>
    const API_URL    = "{{ url('/api/buku') }}";
    const BUKU_URL   = "{{ url('/buku') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

    // Hanya admin & petugas (permission kelola-data-buku) yang boleh mengubah data.
    // Ini hanya untuk tampilan; penegakan sebenarnya ada di server (middleware permission pada API).
    const BISA_KELOLA = @json(auth()->user()->can('kelola-data-buku'));

    const el = {
        alert:   document.getElementById('alert'),
        form:    document.getElementById('form-buku'),
        title:   document.getElementById('form-title'),
        id:      document.getElementById('buku-id'),
        kategori:document.getElementById('kategori_id'),
        judul:   document.getElementById('judul'),
        penulis: document.getElementById('penulis'),
        tahun:   document.getElementById('tahun_terbit'),
        simpan:  document.getElementById('btn-simpan'),
        batal:   document.getElementById('btn-batal'),
        tbody:   document.getElementById('tabel-buku'),
        total:   document.getElementById('info-total'),
        pagi:    document.getElementById('pagination'),
        prev:    document.getElementById('btn-prev'),
        next:    document.getElementById('btn-next'),
        halaman: document.getElementById('info-halaman'),
    };

    let halamanSaatIni = 1;
    let dataHalaman = [];     

    function tampilPesan(teks, tipe = 'success') {
        el.alert.textContent = teks;
        el.alert.className = 'alert alert-' + tipe;
        el.alert.hidden = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function sembunyiPesan() { el.alert.hidden = true; }

    function bersihkanErrorField() {
        document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
        document.querySelectorAll('.form-group input, .form-group select')
            .forEach(e => e.classList.remove('is-invalid'));
    }

    function tampilErrorField(errors) {
        for (const [field, pesan] of Object.entries(errors)) {
            const target = document.querySelector(`[data-error-for="${field}"]`);
            if (target) target.textContent = pesan[0];
            const input = document.getElementById(field);
            if (input) input.classList.add('is-invalid');
        }
    }


    async function kirim(url, options = {}) {
        let response;

        try {
            response = await fetch(url, {
                ...options,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    ...(options.headers || {}),
                },
            });
        } catch (err) {
            const e = new Error('API tidak dapat diakses. Periksa koneksi atau pastikan server berjalan.');
            e.jaringan = true;
            throw e;
        }

        let body = null;
        try { body = await response.json(); } catch (_) { /* respons bukan JSON */ }

        if (!response.ok) {
            const e = new Error(
                response.status === 422 ? 'Data yang dikirim belum valid. Periksa isian formulir.'
                : response.status === 401 ? 'Sesi Anda berakhir. Silakan login kembali.'
                : response.status === 403 ? 'Anda tidak memiliki hak akses untuk operasi ini.'
                : response.status === 404 ? ((body && body.message) || 'Data tidak ditemukan.')
                : response.status >= 500  ? 'Terjadi kesalahan pada server. Coba lagi nanti.'
                : ((body && body.message) || `Permintaan gagal (HTTP ${response.status}).`)
            );
            e.status = response.status;
            e.errors = body && body.errors ? body.errors : null;
            throw e;
        }

        return body;
    }

    // ---------- 1. READ: GET /api/buku ----------
    async function muatBuku(halaman = 1) {
        try {
            const hasil = await kirim(`${API_URL}?page=${halaman}`);

            // Jika halaman kosong setelah penghapusan, mundur satu halaman
            if (hasil.data.length === 0 && halaman > 1) {
                return muatBuku(halaman - 1);
            }

            halamanSaatIni = hasil.meta.current_page;
            dataHalaman = hasil.data;
            renderTabel(hasil.data, hasil.meta);
        } catch (err) {
            el.tbody.innerHTML = '';
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 6;
            td.className = 'table-empty table-error';
            td.textContent = 'Gagal memuat data buku. ' + err.message;
            tr.appendChild(td);
            el.tbody.appendChild(tr);
            el.total.textContent = 'Data tidak tersedia';
            el.pagi.hidden = true;
            tampilPesan('Gagal memuat data buku. ' + err.message, 'error');
        }
    }

    function sel(teks) {
        const td = document.createElement('td');
        td.textContent = teks;
        return td;
    }

    function tombol(label, kelas, handler) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'btn-aksi ' + kelas;
        b.textContent = label;
        b.addEventListener('click', handler);
        return b;
    }

    function renderTabel(data, meta) {
        el.tbody.innerHTML = '';

        if (data.length === 0) {
            el.tbody.innerHTML = '<tr><td colspan="6" class="table-empty">Belum ada buku yang tersedia.</td></tr>';
        }

        data.forEach((buku, i) => {
            const tr = document.createElement('tr');

            // Atribut yang dibaca = atribut hasil pemetaan BukuResource
            tr.appendChild(sel(meta.from + i));
            tr.appendChild(sel(buku.kategori));
            tr.appendChild(sel(buku.judul));
            tr.appendChild(sel(buku.penulis));
            tr.appendChild(sel(buku.tahun));

            const aksi = document.createElement('td');
            aksi.className = 'aksi';

            const detail = document.createElement('a');
            detail.href = `${BUKU_URL}/${buku.id}`;
            detail.className = 'btn-aksi btn-detail';
            detail.textContent = 'Detail';
            aksi.appendChild(detail);

            if (BISA_KELOLA) {
                aksi.appendChild(tombol('Ubah',  'btn-ubah',  () => mulaiUbah(buku)));
                aksi.appendChild(tombol('Hapus', 'btn-hapus', () => hapusBuku(buku)));
            }
            tr.appendChild(aksi);

            el.tbody.appendChild(tr);
        });

        el.total.textContent = `Menampilkan ${meta.total} buku`;
        el.pagi.hidden = meta.last_page <= 1;
        el.halaman.textContent = `Halaman ${meta.current_page} dari ${meta.last_page}`;
        el.prev.disabled = meta.current_page <= 1;
        el.next.disabled = meta.current_page >= meta.last_page;
    }

    el.prev.addEventListener('click', () => muatBuku(halamanSaatIni - 1));
    el.next.addEventListener('click', () => muatBuku(halamanSaatIni + 1));

    // ---------- 2 & 3. CREATE (POST) dan UPDATE (PUT) ----------
    if (el.form) el.form.addEventListener('submit', async (event) => {
        event.preventDefault();
        sembunyiPesan();
        bersihkanErrorField();

        const idEdit = el.id.value;
        const sedangUbah = idEdit !== '';

        const payload = {
            kategori_id:  el.kategori.value,
            judul:        el.judul.value.trim(),
            penulis:      el.penulis.value.trim(),
            tahun_terbit: el.tahun.value,
        };

        el.simpan.disabled = true;
        el.simpan.textContent = 'Menyimpan...';

        try {
            const hasil = await kirim(
                sedangUbah ? `${API_URL}/${idEdit}` : API_URL,
                {
                    method: sedangUbah ? 'PUT' : 'POST',
                    body: JSON.stringify(payload),
                }
            );

            // Pesan sukses hanya muncul jika response.ok = true
            resetForm();
            tampilPesan(hasil.message, 'success');
            await muatBuku(sedangUbah ? halamanSaatIni : 1);   // perbarui tabel tanpa reload
        } catch (err) {
            if (err.errors) tampilErrorField(err.errors);
            tampilPesan(
                (sedangUbah ? 'Gagal memperbarui buku. ' : 'Gagal menambahkan buku. ') + err.message,
                'error'
            );
        } finally {
            el.simpan.disabled = false;
            el.simpan.textContent = el.id.value ? 'Perbarui' : 'Simpan';
        }
    });

    function mulaiUbah(buku) {
        sembunyiPesan();
        bersihkanErrorField();

        el.id.value       = buku.id;
        el.kategori.value = buku.kategori_id;
        el.judul.value    = buku.judul;
        el.penulis.value  = buku.penulis;
        el.tahun.value    = buku.tahun;

        el.title.textContent  = `Ubah Buku #${buku.id}`;
        el.simpan.textContent = 'Perbarui';
        el.batal.hidden = false;
        el.form.scrollIntoView({ behavior: 'smooth', block: 'center' });
        el.judul.focus();
    }

    function resetForm() {
        el.form.reset();
        el.id.value = '';
        el.title.textContent  = 'Tambah Buku';
        el.simpan.textContent = 'Simpan';
        el.batal.hidden = true;
        bersihkanErrorField();
    }

    if (el.batal) el.batal.addEventListener('click', () => { resetForm(); sembunyiPesan(); });

    // ---------- 4. DELETE: DELETE /api/buku/{id} ----------
    async function hapusBuku(buku) {
        // Konfirmasi dulu; jika dibatalkan, tidak ada request yang dikirim
        if (!confirm(`Yakin ingin menghapus buku "${buku.judul}"?`)) return;

        sembunyiPesan();

        try {
            const hasil = await kirim(`${API_URL}/${buku.id}`, { method: 'DELETE' });

            // Jika buku yang dihapus sedang diedit, kosongkan formulir
            if (el.id.value === String(buku.id)) resetForm();

            tampilPesan(hasil.message, 'success');
            await muatBuku(halamanSaatIni);
        } catch (err) {
            tampilPesan('Gagal menghapus buku. ' + err.message, 'error');
        }
    }

    // Muat data saat halaman pertama kali dibuka
    muatBuku(1);
</script>

@endsection