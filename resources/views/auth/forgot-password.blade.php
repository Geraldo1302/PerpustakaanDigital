@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - Perpustakaan Digital')

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h2>Lupa Kata Sandi</h2>
        <p>Masukkan email Anda, kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

        <form method="POST" action="{{ route('password.email') }}" novalidate>
            @csrf

            <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
        </form>

        <div class="auth-links"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
    </div>
</section>
@endsection
