<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Soal 1 & 2: registrasi, login, logout, hashing kata sandi,
 * middleware auth dan guest.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_halaman_login_dan_registrasi_dapat_dibuka_tamu(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_registrasi_membuat_akun_siswa_dengan_password_ter_hash(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.test',
            'no_telepon' => '08123456789',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $user = User::where('email', 'budi@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));

        // kata sandi tidak tersimpan sebagai teks biasa
        $this->assertNotSame('rahasia123', $user->password);
        $this->assertTrue(Hash::check('rahasia123', $user->password));

        // otomatis siswa + punya data anggota
        $this->assertTrue($user->hasRole('siswa'));
        $this->assertNotNull($user->anggota);
    }

    public function test_role_tidak_dapat_dipilih_lewat_formulir_registrasi(): void
    {
        $this->post('/register', [
            'name' => 'Licik',
            'email' => 'licik@example.test',
            'no_telepon' => '0811',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'admin',
            'roles' => ['admin'],
        ]);

        $user = User::where('email', 'licik@example.test')->firstOrFail();

        $this->assertTrue($user->hasRole('siswa'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_login_kredensial_valid_berhasil(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $user->assignRole('siswa');

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_kredensial_tidak_valid_ditolak(): void
    {
        $user = User::factory()->create();

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'salah-total'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_pengguna_yang_sudah_login_tidak_bisa_membuka_login_dan_registrasi(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $this->actingAs($user)->get('/login')->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/register')->assertRedirect(route('dashboard'));
    }

    public function test_logout_lalu_halaman_dilindungi_meminta_login_kembali(): void
    {
        $user = User::factory()->create();
        $user->assignRole('siswa');

        $this->actingAs($user)->get('/buku')->assertOk();

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();

        $this->get('/buku')->assertRedirect(route('login'));
    }
}
