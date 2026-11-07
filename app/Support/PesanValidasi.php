<?php

namespace App\Support;

/**
 * Pesan validasi berbahasa Indonesia yang dipakai bersama
 * oleh form autentikasi, profil, dan manajemen pengguna.
 */
class PesanValidasi
{
    public static function pesan(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'email' => 'Format :attribute tidak valid.',
            'lowercase' => ':attribute harus menggunakan huruf kecil.',
            'unique' => ':attribute sudah terdaftar.',
            'confirmed' => 'Konfirmasi :attribute tidak cocok.',
            'min.string' => ':attribute minimal :min karakter.',
            'max.string' => ':attribute maksimal :max karakter.',
            'in' => ':attribute yang dipilih tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'current_password' => 'Kata sandi saat ini salah.',
        ];
    }

    public static function atribut(): array
    {
        return [
            'name' => 'Nama',
            'email' => 'Email',
            'password' => 'Kata sandi',
            'current_password' => 'Kata sandi saat ini',
            'no_telepon' => 'Nomor telepon',
            'role' => 'Role',
            'buku_id' => 'Buku',
            'token' => 'Token',
        ];
    }
}
