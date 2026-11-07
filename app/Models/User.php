<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// Kolom "role" sengaja TIDAK ada di $fillable: role hanya boleh ditetapkan
// lewat Spatie (assignRole / syncRoles) oleh mekanisme yang berwenang.
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Data anggota perpustakaan milik akun ini.
     */
    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class, 'user_id');
    }

    /**
     * Ambil data anggota milik akun ini; buat jika belum ada
     * (mis. akun admin/petugas yang baru pertama kali meminjam buku).
     */
    public function pastikanAnggota(?string $noTelepon = null): Anggota
    {
        $anggota = $this->anggota()->first();

        if ($anggota) {
            return $anggota;
        }

        return $this->anggota()->create([
            'nama' => $this->name,
            'email' => $this->email,
            'no_telepon' => $noTelepon ?: '-',
        ]);
    }
}
