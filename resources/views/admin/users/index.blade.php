@extends('layouts.app')

@section('title', 'Kelola Pengguna - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>ADMINISTRATOR</span>
        <h2>Kelola Pengguna &amp; Role</h2>
        <p>Tambah, ubah, hapus akun pengguna serta tetapkan role (admin, petugas, siswa).</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-card">
            <div class="rb-card-head">
                <h3>Daftar Pengguna</h3>

                <div class="rb-links">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="rb-inline-form">
                        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama / email"
                               style="padding:9px 12px;border:1px solid #cbd5e1;border-radius:8px;font:inherit;font-size:13px">
                        <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
                    </form>

                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">+ Tambah Pengguna</a>
                </div>
            </div>

            <div class="rb-table-wrap">
                <table class="rb-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            <tr>
                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @foreach ($u->roles as $r)
                                        <span class="rb-badge rb-badge-{{ $r->name }}">{{ ucfirst($r->name) }}</span>
                                    @endforeach

                                    @can('kelola-role-pengguna')
                                        <form method="POST" action="{{ route('admin.users.role', $u) }}" class="rb-inline-form" style="margin-left:8px">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role" aria-label="Role untuk {{ $u->name }}" @disabled($u->is(auth()->user()))>
                                                @foreach ($roles as $namaRole)
                                                    <option value="{{ $namaRole }}" @selected($u->hasRole($namaRole))>{{ ucfirst($namaRole) }}</option>
                                                @endforeach
                                            </select>
                                            @unless ($u->is(auth()->user()))
                                                <button type="submit" class="btn btn-secondary btn-sm">Set</button>
                                            @endunless
                                        </form>
                                    @endcan
                                </td>
                                <td class="aksi">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-primary btn-sm">Ubah</a>

                                    @unless ($u->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                              onsubmit="return confirm('Hapus akun {{ e($u->name) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="rb-empty">Tidak ada pengguna ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rb-pagination">{{ $users->links('partials.pagination') }}</div>
        </div>

    </div>
</section>

@endsection
