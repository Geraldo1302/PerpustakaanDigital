@extends('layouts.app')

@section('title', '401 - Perlu Login')

@section('content')
<section class="err-wrap">
    <div class="container">
        <div class="err-code">401</div>
        <h2>Anda Belum Login</h2>
        <p>Silakan login terlebih dahulu untuk mengakses halaman ini.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">Masuk</a>
    </div>
</section>
@endsection
