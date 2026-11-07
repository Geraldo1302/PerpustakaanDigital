<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Soal 3, 4, 5: RBAC dengan Spatie Laravel Permission.
 * Nomor "Skenario" mengacu pada tabel pengujian di soal nomor 5.
 */
class RbacTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $petugas;
    private User $siswa;
    private User $siswaLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);

        $this->admin = $this->buatUser('admin');
        $this->petugas = $this->buatUser('petugas');
        $this->siswa = $this->buatUser('siswa');
        $this->siswaLain = $this->buatUser('siswa');
    }

    private function buatUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $user->pastikanAnggota('0800');

        return $user;
    }

    private function buatBuku(): Buku
    {
        $kategori = Kategori::create(['nama' => 'Fiksi']);

        return Buku::create([
            'kategori_id' => $kategori->id,
            'judul' => 'Laskar Pelangi',
            'penulis' => 'Andrea Hirata',
            'tahun_terbit' => 2005,
        ]);
    }

    // ---------- Seeder: 3 role & 16 permission tersimpan di database ----------

    public function test_seeder_membuat_3_role_dan_16_permission(): void
    {
        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('permissions', 16);

        $this->assertCount(12, \Spatie\Permission\Models\Role::findByName('admin')->permissions);
        $this->assertCount(13, \Spatie\Permission\Models\Role::findByName('petugas')->permissions);
        $this->assertCount(6, \Spatie\Permission\Models\Role::findByName('siswa')->permissions);
    }

    // ---------- Skenario 1 ----------

    public function test_01_belum_login_membuka_dashboard_admin_dialihkan_ke_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    // ---------- Skenario 2-5 (Admin) ----------

    public function test_02_admin_dapat_membuka_dashboard_admin(): void
    {
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_03_admin_dapat_menambah_mengubah_menghapus_buku(): void
    {
        $kategori = Kategori::create(['nama' => 'Sejarah']);
        $this->actingAs($this->admin);

        $id = $this->postJson('/api/buku', [
            'kategori_id' => $kategori->id, 'judul' => 'Bumi Manusia',
            'penulis' => 'Pramoedya', 'tahun_terbit' => 1980,
        ])->assertCreated()->json('data.id');

        $this->putJson("/api/buku/{$id}", [
            'kategori_id' => $kategori->id, 'judul' => 'Bumi Manusia (Edisi 2)',
            'penulis' => 'Pramoedya', 'tahun_terbit' => 1980,
        ])->assertOk();

        $this->deleteJson("/api/buku/{$id}")->assertOk();
        $this->assertDatabaseMissing('bukus', ['id' => $id]);
    }

    public function test_04_admin_dapat_mengelola_akun_dan_role_pengguna(): void
    {
        $this->actingAs($this->admin)->get('/admin/users')->assertOk();

        $this->post('/admin/users', [
            'name' => 'Petugas Baru', 'email' => 'pb@example.test', 'no_telepon' => '0812',
            'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'role' => 'petugas',
        ])->assertRedirect(route('admin.users.index'));

        $baru = User::where('email', 'pb@example.test')->firstOrFail();
        $this->assertTrue($baru->hasRole('petugas'));

        $this->patch("/admin/users/{$baru->id}/role", ['role' => 'siswa'])->assertRedirect();
        $this->assertTrue($baru->fresh()->hasRole('siswa'));

        $this->delete("/admin/users/{$baru->id}")->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $baru->id]);
    }

    public function test_05_admin_dapat_mengakses_peminjaman_pengembalian_dan_laporan(): void
    {
        $this->actingAs($this->admin);

        $this->get('/kelola/peminjaman')->assertOk();
        $this->get('/kelola/pengembalian')->assertOk();
        $this->get('/laporan')->assertOk();
    }

    // ---------- Skenario 6-9 (Petugas) ----------

    public function test_06_petugas_dapat_membuka_dashboard_petugas(): void
    {
        $this->actingAs($this->petugas)->get('/petugas/dashboard')->assertOk();
    }

    public function test_06b_petugas_tidak_dapat_membuka_dashboard_admin(): void
    {
        $this->actingAs($this->petugas)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_07_petugas_dapat_mengelola_data_buku(): void
    {
        $kategori = Kategori::create(['nama' => 'Fiksi']);

        $this->actingAs($this->petugas)->postJson('/api/buku', [
            'kategori_id' => $kategori->id, 'judul' => 'Negeri 5 Menara',
            'penulis' => 'Ahmad Fuadi', 'tahun_terbit' => 2009,
        ])->assertCreated();
    }

    public function test_08_petugas_dapat_memproses_peminjaman_dan_pengembalian(): void
    {
        $buku = $this->buatBuku();
        $pinjam = Peminjaman::create([
            'anggota_id' => $this->siswa->anggota->id, 'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(), 'status' => Peminjaman::STATUS_DIAJUKAN,
        ]);

        $this->actingAs($this->petugas);

        $this->post("/kelola/peminjaman/{$pinjam->id}/setujui")->assertRedirect();
        $this->assertSame('dipinjam', $pinjam->fresh()->status);

        $this->post("/kelola/pengembalian/{$pinjam->id}/catat")->assertRedirect();
        $this->assertSame('dikembalikan', $pinjam->fresh()->status);
        $this->assertNotNull($pinjam->fresh()->tanggal_kembali);
    }

    public function test_09_petugas_tidak_dapat_membuka_pengelolaan_akun_atau_role(): void
    {
        $this->actingAs($this->petugas);

        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/users/create')->assertForbidden();
        $this->post('/admin/users', [])->assertForbidden();
        $this->patch("/admin/users/{$this->siswa->id}/role", ['role' => 'admin'])->assertForbidden();
        $this->assertFalse($this->siswa->fresh()->hasRole('admin'));
    }

    // ---------- Skenario 10-14 (Siswa) ----------

    public function test_10_siswa_dapat_melihat_daftar_dan_detail_buku(): void
    {
        $buku = $this->buatBuku();
        $this->actingAs($this->siswa);

        $this->get('/buku')->assertOk();
        $this->get("/buku/{$buku->id}")->assertOk()->assertSee('Laskar Pelangi');
        $this->getJson('/api/buku')->assertOk();
    }

    public function test_11_siswa_dapat_melihat_dan_mengubah_profil_sendiri(): void
    {
        $this->actingAs($this->siswa)->get('/profile')->assertOk();

        $this->patch('/profile', [
            'name' => 'Nama Baru', 'email' => $this->siswa->email, 'no_telepon' => '0899',
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame('Nama Baru', $this->siswa->fresh()->name);
        $this->assertSame('0899', $this->siswa->fresh()->anggota->no_telepon);
    }

    public function test_11b_profil_siswa_tidak_dapat_dipakai_untuk_mengubah_role_atau_pengguna_lain(): void
    {
        $this->actingAs($this->siswa)->patch('/profile', [
            'name' => 'X', 'email' => 'x@example.test', 'no_telepon' => '1',
            'role' => 'admin', 'id' => $this->siswaLain->id,
        ]);

        $this->assertFalse($this->siswa->fresh()->hasRole('admin'));
        $this->assertNotSame('X', $this->siswaLain->fresh()->name);
    }

    public function test_12_siswa_dapat_melihat_riwayat_peminjaman_sendiri(): void
    {
        $buku = $this->buatBuku();
        Peminjaman::create([
            'anggota_id' => $this->siswa->anggota->id, 'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(), 'tanggal_kembali' => now()->toDateString(),
            'status' => 'dikembalikan',
        ]);

        $this->actingAs($this->siswa);

        $this->get('/peminjaman-saya')->assertOk()->assertSee('Laskar Pelangi');
        $this->get("/riwayat-peminjaman/{$this->siswa->id}")->assertOk();
    }

    public function test_13_siswa_tidak_dapat_membuka_dashboard_admin_atau_pengelolaan_buku(): void
    {
        $kategori = Kategori::create(['nama' => 'Fiksi']);
        $this->actingAs($this->siswa);

        $this->get('/admin/dashboard')->assertForbidden();
        $this->get('/petugas/dashboard')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/kelola/peminjaman')->assertForbidden();
        $this->get('/laporan')->assertForbidden();

        // pengelolaan data buku (API) ditolak, walau URL diakses langsung
        $this->postJson('/api/buku', [
            'kategori_id' => $kategori->id, 'judul' => 'Buku Ilegal',
            'penulis' => 'Penyusup', 'tahun_terbit' => 2020,
        ])->assertForbidden();
        $this->assertDatabaseMissing('bukus', ['judul' => 'Buku Ilegal']);
    }

    public function test_14_siswa_tidak_dapat_melihat_riwayat_siswa_lain(): void
    {
        $buku = $this->buatBuku();
        Peminjaman::create([
            'anggota_id' => $this->siswaLain->anggota->id, 'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(), 'status' => 'dipinjam',
        ]);

        $this->actingAs($this->siswa)
            ->get("/riwayat-peminjaman/{$this->siswaLain->id}")
            ->assertForbidden();

        // staf boleh melihat riwayat anggota
        $this->actingAs($this->petugas)
            ->get("/riwayat-peminjaman/{$this->siswaLain->id}")
            ->assertOk();
    }

    public function test_siswa_dapat_mengajukan_peminjaman_tetapi_tidak_dapat_menyetujuinya(): void
    {
        $buku = $this->buatBuku();
        $this->actingAs($this->siswa);

        $this->post('/peminjaman/ajukan', ['buku_id' => $buku->id])->assertRedirect(route('peminjaman.saya'));

        $pinjam = Peminjaman::firstOrFail();
        $this->assertSame('diajukan', $pinjam->status);
        $this->assertSame($this->siswa->anggota->id, $pinjam->anggota_id);

        $this->post("/kelola/peminjaman/{$pinjam->id}/setujui")->assertForbidden();
        $this->assertSame('diajukan', $pinjam->fresh()->status);
    }

    public function test_admin_tidak_dapat_meminjam_buku(): void
    {
        $buku = $this->buatBuku();
        $this->actingAs($this->admin);

        $this->post('/peminjaman/ajukan', ['buku_id' => $buku->id])->assertForbidden();
        $this->get('/peminjaman-saya')->assertForbidden();
        $this->assertDatabaseCount('peminjamans', 0);

        // tombol ajukan tidak tampil di detail buku
        $this->get("/buku/{$buku->id}")->assertOk()->assertDontSee('Ajukan Peminjaman');
    }

    public function test_admin_tetap_dapat_melihat_riwayat_anggota(): void
    {
        $this->actingAs($this->admin)
            ->get("/riwayat-peminjaman/{$this->siswa->id}")
            ->assertOk();
    }

    // ---------- Skenario 15 ----------

    public function test_15_setelah_logout_halaman_dilindungi_harus_login_kembali(): void
    {
        foreach ([$this->admin, $this->petugas, $this->siswa] as $user) {
            $this->actingAs($user)->post('/logout');
            $this->assertGuest();

            $this->get('/buku')->assertRedirect(route('login'));
            $this->get('/profile')->assertRedirect(route('login'));
        }
    }

    // ---------- API tanpa login ----------

    public function test_api_buku_tanpa_login_ditolak_401(): void
    {
        $this->getJson('/api/buku')->assertUnauthorized();
    }
}
