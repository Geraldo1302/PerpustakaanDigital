<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Membuat 3 role (admin, petugas, siswa) dan 16 permission
 * sesuai tabel pembagian hak akses pada soal nomor 4.
 */
class RolePermissionSeeder extends Seeder
{
    /** No. => nama permission */
    public const PERMISSIONS = [
        1  => 'akses-dashboard-admin',
        2  => 'akses-dashboard-petugas',
        3  => 'lihat-daftar-buku',
        4  => 'kelola-data-buku',
        5  => 'kelola-data-pengguna',
        6  => 'kelola-role-pengguna',
        7  => 'kelola-peminjaman',
        8  => 'kelola-pengembalian',
        9  => 'ajukan-peminjaman-buku',
        10 => 'proses-persetujuan-peminjaman',
        11 => 'catat-pengembalian-buku',
        12 => 'lihat-status-peminjaman-sendiri',
        13 => 'lihat-riwayat-peminjaman-sendiri',
        14 => 'lihat-laporan-perpustakaan',
        15 => 'lihat-profil-sendiri',
        16 => 'ubah-profil-sendiri',
    ];

    /** role => daftar nomor permission yang dimiliki (✓ pada tabel) */
    public const ROLE_MAP = [
        // Admin tidak meminjam buku: tanpa 9 (ajukan), 12 (status sendiri), 13 (riwayat sendiri)
        'admin'   => [1, 3, 4, 5, 6, 7, 8, 10, 11, 14, 15, 16],
        'petugas' => [2, 3, 4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
        'siswa'   => [3, 9, 12, 13, 15, 16],
    ];

    public function run(): void
    {
        // Bersihkan cache permission agar perubahan langsung terbaca
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $nama) {
            Permission::findOrCreate($nama, 'web');
        }

        foreach (self::ROLE_MAP as $namaRole => $nomorPermission) {
            $role = Role::findOrCreate($namaRole, 'web');

            $role->syncPermissions(
                array_map(fn (int $no) => self::PERMISSIONS[$no], $nomorPermission)
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
