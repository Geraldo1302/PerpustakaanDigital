<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Id dibuat tetap + updateOrCreate, sehingga seeder aman dijalankan berulang
     * (tidak menggandakan kategori) dan baris duplikat lama otomatis diganti namanya.
     * Id 1-3 dipakai oleh BukuSeeder (Fiksi, Sejarah, Pengembangan Diri).
     */
    public function run(): void
    {
        $kategoris = [
            1 => 'Fiksi',
            2 => 'Sejarah',
            3 => 'Pengembangan Diri',
            4 => 'Sains',
            5 => 'Teknologi',
            6 => 'Komik',
            7 => 'Biografi',
            8 => 'Pemrograman',
            9 => 'Bahasa',
        ];

        foreach ($kategoris as $id => $nama) {
            $kategori = Kategori::find($id) ?? new Kategori();
            $kategori->forceFill(['id' => $id, 'nama' => $nama])->save();
        }
    }
}
