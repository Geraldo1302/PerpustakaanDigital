@extends('layouts.app')

@section('title', 'Profil Saya - Perpustakaan Digital')

@section('content')

<section class="page-header">
    <div class="container">
        <span>AKUN SAYA</span>
        <h2>Profil Saya</h2>
        <p>Lihat dan perbarui data profil milik Anda sendiri.</p>
    </div>
</section>

<section class="rb-section">
    <div class="container">

        <div class="rb-card">
            <h3>Informasi Profil</h3>

            {{-- Hanya yang punya permission "ubah-profil-sendiri" melihat formulir yang bisa diedit --}}
            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div class="rb-form-grid">
                    <div class="rb-field {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required @cannot('ubah-profil-sendiri') readonly @endcannot>
                        @error('name') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required @cannot('ubah-profil-sendiri') readonly @endcannot>
                        @error('email') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field {{ $errors->has('no_telepon') ? 'has-error' : '' }}">
                        <label for="no_telepon">Nomor Telepon</label>
                        <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon', $anggota->no_telepon) }}" required maxlength="20" @cannot('ubah-profil-sendiri') readonly @endcannot>
                        @error('no_telepon') <small class="rb-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="rb-field">
                        <label>Role</label>
                        <input type="text" value="{{ $user->getRoleNames()->implode(', ') }}" readonly>
                        <small class="rb-muted">Role tidak dapat diubah sendiri.</small>
                    </div>
                </div>

                @can('ubah-profil-sendiri')
                    <div class="rb-actions">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                @endcan
            </form>
        </div>

        @can('ubah-profil-sendiri')
            <div class="rb-card">
                <h3>Ubah Kata Sandi</h3>

                <form method="POST" action="{{ route('password.update') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="rb-form-grid">
                        <div class="rb-field {{ $errors->has('current_password') ? 'has-error' : '' }}">
                            <label for="current_password">Kata Sandi Saat Ini</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                            @error('current_password') <small class="rb-error">{{ $message }}</small> @enderror
                        </div>

                        <div></div>

                        <div class="rb-field {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label for="password">Kata Sandi Baru</label>
                            <input id="password" type="password" name="password" autocomplete="new-password">
                            @error('password') <small class="rb-error">{{ $message }}</small> @enderror
                        </div>

                        <div class="rb-field">
                            <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="rb-actions">
                        <button type="submit" class="btn btn-primary">Perbarui Kata Sandi</button>
                    </div>
                </form>
            </div>
        @endcan

    </div>
</section>

@endsection
