<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // [Soal 3] Aturan pengelolaan buku dipusatkan pada satu Gate:
        // hanya role admin dan petugas yang boleh menambah, mengubah, dan menghapus buku.
        Gate::define('kelola-buku', function (User $user): bool {
            return $user->hasAnyRole(['admin', 'petugas']);
        });
    }
}
