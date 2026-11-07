# Modul 5 — Autentikasi & RBAC (Sistem Perpustakaan Digital)

Proyek ini melanjutkan proyek Modul sebelumnya (fitur buku, kategori, anggota, peminjaman, API, dan Resource tetap dipertahankan).

## 1. Cara menjalankan

> Wajib dijalankan sekali karena ada 2 paket baru (`spatie/laravel-permission` dan `laravel/breeze`).

```bash
composer update spatie/laravel-permission laravel/breeze   # pasang paket + perbarui composer.lock
php artisan migrate --seed                                  # tabel baru + role, permission, akun contoh
php artisan optimize:clear
php artisan serve
```

* Database lama dipertahankan. Bila ingin mengulang dari nol: `php artisan migrate:fresh --seed`.
* Jika `migrate` sudah pernah dijalankan dan Anda hanya ingin mengisi role/akun:
  `php artisan db:seed --class=RolePermissionSeeder` lalu `php artisan db:seed --class=UserSeeder`.
* **Tidak perlu `npm run build`**: gaya tambahan ada di `public/css/rbac.css` (dimuat langsung).
* Jangan menjalankan `php artisan breeze:install` / `vendor:publish` Spatie lagi, karena akan menimpa/menggandakan file yang sudah disiapkan.

### Akun contoh (hasil seeder — kata sandi semua: `password`)

| Role    | Email                       |
|---------|-----------------------------|
| Admin   | admin@perpustakaan.test     |
| Petugas | petugas@perpustakaan.test   |
| Siswa   | siswa@perpustakaan.test     |
| Siswa 2 | siswa2@perpustakaan.test    |

Pendaftar baru lewat `/register` selalu menjadi **siswa**.

## 2. Pemetaan soal → implementasi

| Soal | Implementasi | File utama |
|------|--------------|------------|
| 1. Breeze: register, login, logout, hashing | Struktur Breeze (controller, request, route, view) disesuaikan dengan tabel `users` + `anggotas`. Kata sandi di-hash (`Hash::make` + cast `hashed`). Kredensial salah ditolak. | `routes/auth.php`, `app/Http/Controllers/Auth/*`, `app/Http/Requests/Auth/LoginRequest.php`, `resources/views/auth/*` |
| 2. Session & middleware | `auth` melindungi halaman, `guest` membatasi login/registrasi. Session di-regenerate saat login, di-invalidate saat logout. | `routes/web.php`, `routes/auth.php`, `bootstrap/app.php` |
| 3. RBAC 3 role | Admin / Petugas / Siswa. Role ditetapkan seeder atau Admin (halaman Kelola Pengguna), **bukan** dari formulir registrasi; kolom role tidak ada di `$fillable`. | `RegisteredUserController`, `Admin\UserController`, `User.php` |
| 4. Spatie Permission | 3 role + 16 permission (tabel soal) disimpan di database lewat seeder. Data pribadi dicek berdasarkan identitas pengguna login, bukan hanya permission. | `config/permission.php`, migrasi `create_permission_tables`, `RolePermissionSeeder`, `PeminjamanController::riwayat` |
| 5. Proteksi route & Blade | Middleware `auth` + `role:` / `permission:` pada route; `@role`, `@can`, `@canany` pada menu & tombol. | `routes/web.php`, `routes/api.php`, `layouts/app.blade.php`, semua view |

### Tabel permission

> Perubahan: **Admin tidak meminjam buku** (permission 9, 12, 13 dicabut dari admin). Admin tetap bisa melihat riwayat anggota lewat halaman Peminjaman.

| No | Permission | Admin | Petugas | Siswa |
|----|-----------|:-----:|:-------:|:-----:|
| 1 | akses-dashboard-admin | ✓ | — | — |
| 2 | akses-dashboard-petugas | — | ✓ | — |
| 3 | lihat-daftar-buku | ✓ | ✓ | ✓ |
| 4 | kelola-data-buku | ✓ | ✓ | — |
| 5 | kelola-data-pengguna | ✓ | — | — |
| 6 | kelola-role-pengguna | ✓ | — | — |
| 7 | kelola-peminjaman | ✓ | ✓ | — |
| 8 | kelola-pengembalian | ✓ | ✓ | — |
| 9 | ajukan-peminjaman-buku | — | ✓ | ✓ |
| 10 | proses-persetujuan-peminjaman | ✓ | ✓ | — |
| 11 | catat-pengembalian-buku | ✓ | ✓ | — |
| 12 | lihat-status-peminjaman-sendiri | — | ✓ | ✓ |
| 13 | lihat-riwayat-peminjaman-sendiri | — | ✓ | ✓ |
| 14 | lihat-laporan-perpustakaan | ✓ | ✓ | — |
| 15 | lihat-profil-sendiri | ✓ | ✓ | ✓ |
| 16 | ubah-profil-sendiri | ✓ | ✓ | ✓ |

## 3. Perubahan pada kode lama (seminimal mungkin)

| File | Perubahan | Alasan |
|------|-----------|--------|
| `routes/web.php` | `/buku` & `/buku/{id}` dipindah ke grup `auth` + `permission:lihat-daftar-buku` (nama route tetap). Beranda `/` tetap publik. | Halaman buku harus dilindungi. |
| `routes/api.php` | Endpoint `/api/buku` & `/api/rekap/*` dibungkus `web` + `auth` + `permission`. | Tanpa ini, siswa/tamu bisa menambah/menghapus buku lewat URL API langsung. **Akibat:** API kini memakai session login (bukan terbuka); uji lewat Postman perlu login/cookie. |
| `bootstrap/app.php` | Alias middleware Spatie + tujuan redirect `guest`/`auth`. | Wajib untuk RBAC. |
| `app/Models/User.php` | Trait `HasRoles`, relasi `anggota()`. | Wajib. |
| `app/Models/Anggota.php`, `Peminjaman.php` | Kolom baru di `$fillable`, relasi, konstanta status. | Menghubungkan akun ↔ anggota ↔ peminjaman. |
| `database/factories/PeminjamanFactory.php` | Menambah kolom `status`. | Selaras dengan kolom baru. |
| `database/seeders/DatabaseSeeder.php` | Memanggil `RolePermissionSeeder` & `UserSeeder`. | Pengisian role/akun. |
| `resources/views/layouts/app.blade.php` | Menu per role, tombol Keluar, meta CSRF, pesan flash, `rbac.css`. | Navigasi sesuai hak akses. |
| `resources/views/buku/index.blade.php` | Formulir & tombol Ubah/Hapus hanya untuk `kelola-data-buku`; header `X-CSRF-TOKEN` pada fetch; tombol Detail. | Siswa hanya boleh melihat. |
| `resources/views/buku/show.blade.php` | `kategori`/`tahun`/`deskripsi` diperbaiki agar membaca atribut model; tombol Ajukan Peminjaman. | Halaman lama membaca array, padahal controller mengirim model (kategori tampil sebagai JSON). |
| `composer.json` | Menambah `spatie/laravel-permission` & `laravel/breeze`. | Paket baru. |

Migrasi baru (tidak mengubah migrasi lama): `create_permission_tables`, `add_user_id_to_anggotas_table`, `add_status_to_peminjamans_table`.

## 4. Pengujian

* Otomatis: `php artisan test` (`tests/Feature/AuthenticationTest.php`, `tests/Feature/RbacTest.php`) — setiap skenario 1–15 pada soal punya satu tes.
* Manual: lihat `PENGUJIAN.md` (kolom *Hasil Aktual* diisi setelah Anda menjalankan).
