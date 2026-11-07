<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun contoh untuk pengujian. Role ditetapkan oleh seeder (mekanisme berwenang),
 * bukan lewat formulir registrasi. Kata sandi akun siswa & petugas: "password".
 * [Soal 5c] Kata sandi admin TIDAK lagi "password": diambil dari SEED_ADMIN_PASSWORD di .env.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        // [Soal 5c] Kata sandi admin bawaan diganti (bukan lagi "password").
        $passwordAdmin = env('SEED_ADMIN_PASSWORD') ?: 'Adm1n!250441100040#Perpus';

        $akun = [
            ['name' => 'Admin Perpustakaan',  'email' => 'admin@perpustakaan.test',   'role' => 'admin',   'telp' => '081200000001'],
            ['name' => 'Petugas Perpustakaan', 'email' => 'petugas@perpustakaan.test', 'role' => 'petugas', 'telp' => '081200000002'],
            ['name' => 'Siswa Satu',          'email' => 'siswa@perpustakaan.test',   'role' => 'siswa',   'telp' => '081200000003'],
            ['name' => 'Siswa Dua',           'email' => 'siswa2@perpustakaan.test',  'role' => 'siswa',   'telp' => '081200000004'],
        ];

        foreach ($akun as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $user->email_verified_at ??= now();

            // Terapkan kata sandi baru juga pada akun admin yang sudah terlanjur dibuat sebelumnya
            if ($data['role'] === 'admin') {
                $user->password = Hash::make($passwordAdmin);
            }

            $user->save();

            $user->syncRoles([$data['role']]);
            // Admin tidak meminjam buku, jadi tidak perlu data anggota
            if ($data['role'] !== 'admin') {
                $user->pastikanAnggota($data['telp']);
            }
        }

        // Contoh riwayat peminjaman milik kedua siswa (untuk uji pembatasan data pribadi)
        $buku = Buku::orderBy('id')->take(3)->get();

        if ($buku->count() >= 3) {
            $siswa1 = User::where('email', 'siswa@perpustakaan.test')->first()->anggota;
            $siswa2 = User::where('email', 'siswa2@perpustakaan.test')->first()->anggota;

            Peminjaman::firstOrCreate(
                ['anggota_id' => $siswa1->id, 'buku_id' => $buku[0]->id],
                ['tanggal_pinjam' => now()->subDays(10)->toDateString(), 'tanggal_kembali' => now()->subDays(3)->toDateString(), 'status' => 'dikembalikan']
            );
            Peminjaman::firstOrCreate(
                ['anggota_id' => $siswa1->id, 'buku_id' => $buku[1]->id],
                ['tanggal_pinjam' => now()->subDays(2)->toDateString(), 'status' => 'diajukan']
            );
            Peminjaman::firstOrCreate(
                ['anggota_id' => $siswa2->id, 'buku_id' => $buku[2]->id],
                ['tanggal_pinjam' => now()->subDays(5)->toDateString(), 'status' => 'dipinjam']
            );
        }
    }
}
