<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * /dashboard hanya "pengarah": setelah login pengguna dikirim ke halaman
 * awal yang sesuai dengan permission-nya.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->can('akses-dashboard-admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->can('akses-dashboard-petugas')) {
            return redirect()->route('petugas.dashboard');
        }

        return redirect()->route('buku.index');
    }
}
