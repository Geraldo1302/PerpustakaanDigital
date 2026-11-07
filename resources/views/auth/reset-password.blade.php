@extends('layouts.app')

@section('title', 'Reset Kata Sandi - Perpustakaan Digital')

@section('content')
<section class="auth-wrap">
    <div class="auth-card">
        <h2>Reset Kata Sandi</h2>
        <p>Masukkan kata sandi baru untuk akun Anda.</p>

        <form method="POST" action="{{ route('password.store') }}" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="rb-field {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                @error('email') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="password">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                @error('password') <small class="rb-error">{{ $message }}</small> @enderror
            </div>

            <div class="rb-field">
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">Reset Kata Sandi</button>
        </form>
    </div>
</section>
@endsection
