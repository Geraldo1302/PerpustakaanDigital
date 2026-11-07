@extends('layouts.app')

@section('title', '403 - Akses Ditolak')

@section('content')
<section class="err-wrap">
    <div class="container">
        <div class="err-code">403</div>
        <h2>Akses Ditolak</h2>
        <p>Anda tidak memiliki hak akses untuk membuka halaman atau menjalankan operasi ini.</p>

        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Kembali ke Beranda Saya</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
        @endauth
    </div>
</section>
@endsection
