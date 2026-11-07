<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PesanValidasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

/**
 * Kelola akun & role pengguna (khusus Admin).
 * Route resource dilindungi permission "kelola-data-pengguna";
 * perubahan role dilindungi tambahan permission "kelola-role-pengguna".
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with('roles')
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->cari;
                $q->where(fn ($w) => $w->where('name', 'like', "%{$cari}%")->orWhere('email', 'like', "%{$cari}%"));
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['nullable', Rule::in(Role::pluck('name')->all())],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        // Role hanya boleh ditentukan oleh yang punya permission "kelola-role-pengguna".
        // Selain itu, akun baru otomatis berperan siswa.
        $role = $request->user()->can('kelola-role-pengguna') && ! empty($data['role'])
            ? $data['role']
            : 'siswa';

        DB::transaction(function () use ($data, $role) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->email_verified_at = now();
            $user->save();

            $user->syncRoles([$role]);
            $user->pastikanAnggota($data['no_telepon']);
        });

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user->load('anggota'),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'no_telepon' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->pastikanAnggota()->update([
            'nama' => $user->name,
            'email' => $user->email,
            'no_telepon' => $data['no_telepon'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    /** Tetapkan / ubah role pengguna (permission: kelola-role-pengguna). */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['required', Rule::in(Role::pluck('name')->all())],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        // Cegah admin mencabut role-nya sendiri sehingga tidak ada lagi yang bisa mengelola sistem
        if ($user->is($request->user()) && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $user->syncRoles([$request->role]);

        return back()->with('success', "Role {$user->name} diubah menjadi {$request->role}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna berhasil dihapus.');
    }
}
