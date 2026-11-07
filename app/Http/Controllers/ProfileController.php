<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Profil SENDIRI. Seluruh method hanya bekerja pada $request->user()
 * (pengguna yang sedang login) dan tidak menerima ID pengguna dari URL,
 * sehingga pengguna tidak mungkin melihat/mengubah profil orang lain.
 */
class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'anggota' => $user->pastikanAnggota(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->only('name', 'email'))->save();

        // Sinkronkan ke data anggota milik akun yang sama
        $user->pastikanAnggota()->update([
            'nama' => $user->name,
            'email' => $user->email,
            'no_telepon' => $request->no_telepon,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}
