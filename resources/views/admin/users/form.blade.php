@extends('layouts.app')

@php($edit = $user->exists)

@section('title', ($edit ? 'Ubah' : 'Tambah').' Pengguna - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>ADMINISTRATOR</span>
        <h2>{{ $edit ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h2>
        <p>{{ $edit ? 'Perbarui data akun. Kosongkan kata sandi bila tidak ingin mengubahnya.' : 'Buat akun baru dan tetapkan role-nya.' }}</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">
        <div class="rb-card">

            <form method="POST" action="{{ $edit ? route('admin.users.update', $user) : route('admin.users.store') }}" novalidate>
                @csrf
                @if ($edit) @method('PUT') @endif

                <div class="rb-form-grid">
                    <div class="rb-field {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field {{ $errors->has('no_telepon') ? 'has-error' : '' }}">
                        <label for="no_telepon">Nomor Telepon</label>
                        <input id="no_telepon" type="text" name="no_telepon" maxlength="20" required
                               value="{{ old('no_telepon', $user->anggota->no_telepon ?? '') }}">
                        @error('no_telepon') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    @if (! $edit)
                        @can('kelola-role-pengguna')
                            <div class="rb-field {{ $errors->has('role') ? 'has-error' : '' }}">
                                <label for="role">Role</label>
                                <select id="role" name="role">
                                    @foreach ($roles as $namaRole)
                                        <option value="{{ $namaRole }}" @selected(old('role', 'siswa') === $namaRole)>{{ ucfirst($namaRole) }}</option>
                                    @endforeach
                                </select>
                                @error('role') <small class="rb-error">{{ $message }}</small> @enderror
                            </div>
                        @endcan
                    @endif

                    <div class="rb-field {{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password">Kata Sandi {{ $edit ? '(opsional)' : '' }}</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" {{ $edit ? '' : 'required' }}>
                        @error('password') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field">
                        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>

                <div class="rb-actions">
                    <button type="submit" class="btn btn-primary">{{ $edit ? 'Perbarui' : 'Simpan' }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>

        </div>
    </div>
</section>

@endsection
