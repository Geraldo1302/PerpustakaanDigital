@extends('layouts.app')

@section('title', 'Daftar - Perpustakaan Digital')

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h2>Daftar Akun</h2>
        <p>Buat akun anggota perpustakaan. Akun baru otomatis berperan sebagai <strong>Siswa</strong>.</p>

        {{-- Sengaja TIDAK ada pilihan role: role hanya ditetapkan oleh Admin --}}
        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="rb-field {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                @error('name') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                @error('email') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field {{ $errors->has('no_telepon') ? 'has-error' : '' }}">
                <label for="no_telepon">Nomor Telepon</label>
                <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required maxlength="20">
                @error('no_telepon') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="password">Kata Sandi (min. 8 karakter)</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>

        <div class="auth-links">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </div>
    </div>
</section>
@endsection
