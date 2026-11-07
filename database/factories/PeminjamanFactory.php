<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeminjamanFactory extends Factory
{
    public function definition(): array
    {
        $tanggalKembali = fake()->optional()->date();

        return [
            'anggota_id' => Anggota::inRandomOrder()->first()->id ?? Anggota::factory(),
            'buku_id' => Buku::inRandomOrder()->first()->id ?? Buku::factory(),
            'tanggal_pinjam' => fake()->date(),
            'tanggal_kembali' => $tanggalKembali,
            'status' => $tanggalKembali ? 'dikembalikan' : 'dipinjam',
        ];
    }
}