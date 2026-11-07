<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PesanValidasi;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /** Tampilkan halaman registrasi. */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi.
     *
     * Role TIDAK diambil dari input formulir: setiap pendaftar baru otomatis
     * berperan "siswa". Role admin/petugas hanya dapat ditetapkan oleh Admin
     * melalui halaman Kelola Pengguna (atau seeder).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // disimpan sebagai hash
            ]);

            Role::findOrCreate('siswa', 'web');
            $user->assignRole('siswa');

            // Sesuaikan dengan struktur tabel proyek: akun siswa punya data anggota
            $user->anggota()->create([
                'nama' => $user->name,
                'email' => $user->email,
                'no_telepon' => $request->no_telepon,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
