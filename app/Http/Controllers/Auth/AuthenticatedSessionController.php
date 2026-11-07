<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /** Tampilkan halaman login. */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Proses login: autentikasi lalu buat session baru. */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Kembali ke halaman yang tadinya dituju; bila tidak ada, ke dashboard sesuai role
        return redirect()->intended(route('dashboard'));
    }

    /** Logout: hapus autentikasi, hancurkan session, buat token CSRF baru. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar. Silakan login kembali untuk mengakses halaman yang dilindungi.');
    }
}
