# Pengujian RBAC — Sistem Perpustakaan Digital

Jalankan dulu `php artisan migrate --seed` dan `php artisan serve`. Kata sandi akun siswa & petugas: `password`; kata sandi admin: nilai `SEED_ADMIN_PASSWORD` di `.env` (Soal 5c).

Kolom **Hasil Aktual** sengaja dikosongkan: isi dengan hasil yang Anda amati (atau jalankan `php artisan test`, nama tes ada di kolom terakhir).

| No | Role | Skenario Pengujian | Cara uji manual | Hasil yang Diharapkan | Hasil Aktual | Tes otomatis |
|----|------|--------------------|-----------------|-----------------------|--------------|--------------|
| 1 | Belum login | Membuka halaman dashboard admin | Buka `/admin/dashboard` tanpa login | Dialihkan ke halaman login | _isi_ | `test_01_...` |
| 2 | Admin | Membuka dashboard admin | Login `admin@perpustakaan.test` → `/admin/dashboard` | Dashboard admin dapat diakses | _isi_ | `test_02_...` |
| 3 | Admin | Menambah, mengubah, dan menghapus data buku | `/buku` → isi formulir → Simpan / Ubah / Hapus | Operasi berhasil | _isi_ | `test_03_...` |
| 4 | Admin | Mengelola akun dan role pengguna | `/admin/users` → Tambah / Ubah / pilih role → Set / Hapus | Operasi berhasil | _isi_ | `test_04_...` |
| 5 | Admin | Mengakses data peminjaman, pengembalian, dan laporan | `/kelola/peminjaman`, `/kelola/pengembalian`, `/laporan` | Data dapat diakses sesuai hak akses | _isi_ | `test_05_...` |
| 6 | Petugas | Membuka dashboard petugas | Login `petugas@perpustakaan.test` → `/petugas/dashboard` | Dashboard petugas dapat diakses | _isi_ | `test_06_...` |
| 7 | Petugas | Mengelola data buku | `/buku` → tambah / ubah / hapus | Operasi berhasil | _isi_ | `test_07_...` |
| 8 | Petugas | Memproses peminjaman dan pengembalian | `/kelola/peminjaman` → Setujui; `/kelola/pengembalian` → Catat Pengembalian | Operasi berhasil | _isi_ | `test_08_...` |
| 9 | Petugas | Membuka halaman pengelolaan akun atau role pengguna | Buka `/admin/users` | Akses ditolak (403) | _isi_ | `test_09_...` |
| 10 | Siswa | Melihat daftar dan detail buku | Login `siswa@perpustakaan.test` → `/buku` → Detail | Data buku dapat dilihat | _isi_ | `test_10_...` |
| 11 | Siswa | Melihat dan mengubah profil sendiri | `/profile` → ubah nama/telepon → Simpan | Data profil milik sendiri dapat diakses dan diperbarui | _isi_ | `test_11_...` |
| 12 | Siswa | Melihat riwayat peminjaman sendiri | `/peminjaman-saya` | Riwayat milik sendiri dapat dilihat | _isi_ | `test_12_...` |
| 13 | Siswa | Membuka dashboard admin atau halaman pengelolaan buku | Buka `/admin/dashboard`; panggil `POST /api/buku` | Akses ditolak (403) | _isi_ | `test_13_...` |
| 14 | Siswa | Mencoba melihat riwayat peminjaman siswa lain | Login sebagai siswa 1, buka `/riwayat-peminjaman/{id-siswa-2}` | Akses ditolak (403) | _isi_ | `test_14_...` |
| 15 | Admin/Petugas/Siswa | Logout lalu membuka halaman yang dilindungi | Klik Keluar → buka `/buku` | Pengguna harus login kembali | _isi_ | `test_15_...` |

Pengujian tambahan: registrasi tidak bisa memilih role (`test_role_tidak_dapat_dipilih_lewat_formulir_registrasi`), login gagal untuk kredensial salah, kata sandi tersimpan sebagai hash, `/login` & `/register` dialihkan bila sudah login, API `/api/buku` tanpa login → 401.

> Untuk skenario 14 cari ID siswa 2 di `/admin/users` (login admin) atau lewat tabel `users`.
