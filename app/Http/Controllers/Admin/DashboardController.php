<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalBuku' => Buku::count(),
            'totalKategori' => Kategori::count(),
            'totalPengguna' => User::count(),
            'penggunaPerRole' => Role::withCount('users')->orderBy('name')->get(),
            'menunggu' => Peminjaman::where('status', Peminjaman::STATUS_DIAJUKAN)->count(),
            'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
            'dikembalikan' => Peminjaman::where('status', Peminjaman::STATUS_DIKEMBALIKAN)->count(),
        ]);
    }
}
