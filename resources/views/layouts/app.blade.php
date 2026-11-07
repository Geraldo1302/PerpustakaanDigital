<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Perpustakaan Digital')
    </title>

    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/rbac.css') }}">
</head>

<body>

    <!-- HEADER + NAVBAR -->
    <header class="header">
        <div class="container navbar">

            <a href="{{ route('home') }}" class="brand">
                <div class="brand-icon">
                    📚
                </div>

                <div>
                    <h1>Perpustakaan</h1>
                    <span>Digital Library</span>
                </div>
            </a>

            <nav class="nav-menu">

                <a
                    href="{{ route('home') }}"
                    class="{{ request()->routeIs('home') ? 'active' : '' }}"
                >
                    Beranda
                </a>

                @auth

                    @can('lihat-daftar-buku')
                        <a
                            href="{{ route('buku.index') }}"
                            class="{{ request()->routeIs('buku.*') ? 'active' : '' }}"
                        >
                            Daftar Buku
                        </a>
                    @endcan

                    {{-- Menu Admin --}}
                    @role('admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard Admin</a>
                    @endrole

                    {{-- Menu Petugas --}}
                    @role('petugas')
                        <a href="{{ route('petugas.dashboard') }}" class="{{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">Dashboard Petugas</a>
                    @endrole

                    @can('kelola-data-pengguna')
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Pengguna</a>
                    @endcan

                    @can('kelola-peminjaman')
                        <a href="{{ route('kelola.peminjaman.index') }}" class="{{ request()->routeIs('kelola.peminjaman.*') ? 'active' : '' }}">Peminjaman</a>
                    @endcan

                    @can('kelola-pengembalian')
                        <a href="{{ route('kelola.pengembalian.index') }}" class="{{ request()->routeIs('kelola.pengembalian.*') ? 'active' : '' }}">Pengembalian</a>
                    @endcan

                    @can('lihat-laporan-perpustakaan')
                        <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
                    @endcan

                    @canany(['lihat-status-peminjaman-sendiri', 'lihat-riwayat-peminjaman-sendiri'])
                        <a href="{{ route('peminjaman.saya') }}" class="{{ request()->routeIs('peminjaman.saya', 'peminjaman.riwayat') ? 'active' : '' }}">Peminjaman Saya</a>
                    @endcanany

                    @can('lihat-profil-sendiri')
                        <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profil</a>
                    @endcan

                    <span class="nav-user">
                        {{ auth()->user()->name }}
                        @foreach (auth()->user()->getRoleNames() as $namaRole)
                            <span class="rb-badge rb-badge-{{ $namaRole }}">{{ ucfirst($namaRole) }}</span>
                        @endforeach
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link">Keluar</button>
                    </form>

                @endauth

                @guest
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">Daftar</a>
                @endguest

            </nav>

        </div>
    </header>


    <!-- KONTEN -->
    <main>
        @include('partials.flash')

        @yield('content')
    </main>


    <!-- FOOTER -->
    <footer class="footer">

        <div class="container footer-content">

            <div>
                <h3>Perpustakaan Digital</h3>
                <p>
                    Temukan berbagai buku dan pengetahuan
                    dalam satu tempat.
                </p>
            </div>

            <div class="footer-right">
                <p>&copy; {{ date('Y') }} Perpustakaan Digital</p>
                <p>Laravel 13 • PBWL Modul 1</p>
            </div>

        </div>

    </footer>

</body>
</html>