# Pengujian Modul 6 — Keamanan Web dan Otorisasi

NIM: 250441100040. Jalankan `php artisan migrate --seed` (atau `php artisan db:seed --class=UserSeeder` bila DB sudah ada) lalu `php artisan serve`.
Akun: admin `admin@perpustakaan.test` (password = `SEED_ADMIN_PASSWORD` di `.env`), petugas `petugas@perpustakaan.test`, siswa `siswa@perpustakaan.test` / `siswa2@perpustakaan.test` (password `password`).

Kolom **Hasil Aktual** diisi setelah Anda menjalankan sendiri skenario dan mengambil tangkapan layar.

| No | Role | Skenario | Hasil yang Diharapkan | Hasil Aktual |
|----|------|----------|-----------------------|--------------|
| 1 | Belum login | Membuka `/peminjaman/1` | Dialihkan ke halaman login | _isi_ |
| 2 | Admin | Menambah buku berjudul `<script>alert(1)</script>` | Judul tampil sebagai teks, tanpa popup | _isi_ |
| 3 | Admin | Membuka `/buku-cari?judul=' OR '1'='1` | Hasil kosong `[]` | _isi_ |
| 4 | Admin | DELETE `/buku/{id}` via Postman tanpa token | 419 Page Expired | _isi_ |
| 5 | Admin | Menghapus buku lewat tombol Hapus | Buku terhapus | _isi_ |
| 6 | Admin | Membuka tautan `jebakan.html` saat login | Buku tidak terhapus (404/405) | _isi_ |
| 7 | Siswa | Membuka `/buku/create` langsung | 403 Forbidden, tombol kelola tidak tampil | _isi_ |
| 8 | Siswa | Membuka peminjaman milik sendiri | 200, data tampil | _isi_ |
| 9 | Siswa | Membuka peminjaman milik siswa lain | 403 Forbidden | _isi_ |
| 10 | Petugas | Membuka peminjaman milik siswa mana pun | 200, data tampil | _isi_ |
| 11 | Semua | Membuka halaman error dengan `APP_DEBUG=false` | Pesan error umum tanpa jejak kode | _isi_ |
| 12 | Semua | Membuka `/.env` dan login admin dengan `password` | 404/403; login ditolak | _isi_ |
