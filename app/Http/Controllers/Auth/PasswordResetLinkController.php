<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\PesanValidasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], PesanValidasi::pesan(), PesanValidasi::atribut());

        $status = Password::sendResetLink($request->only('email'));

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', 'Tautan reset kata sandi telah dikirim ke email Anda.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Kami tidak dapat menemukan pengguna dengan email tersebut.']);
    }
}
