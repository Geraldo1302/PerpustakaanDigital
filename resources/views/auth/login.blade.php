@extends('layouts.app')

@section('title', 'Masuk - Perpustakaan Digital')

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h2>Masuk</h2>
        <p>Silakan login untuk mengakses Perpustakaan Digital.</p>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                @error('email') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="password">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field">
                <label class="rb-check" for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1"> Ingat saya
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <div class="auth-links">
            <a href="{{ route('password.request') }}">Lupa kata sandi?</a><br>
            Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai siswa</a>
        </div>

        @if (app()->environment('local'))
            <div class="auth-demo">
                <strong>Akun contoh (hasil seeder, kata sandi: password)</strong><br>
                admin@perpustakaan.test &middot; petugas@perpustakaan.test &middot;
                siswa@perpustakaan.test &middot; siswa2@perpustakaan.test
            </div>
        @endif
    </div>
</section>
@endsection
