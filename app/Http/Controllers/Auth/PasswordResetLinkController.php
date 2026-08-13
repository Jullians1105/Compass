<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

/**
 * RF-03: recuperacion de contrasena, primer paso (solicitar el enlace).
 *
 * Usa el password broker nativo de Laravel: Password::sendResetLink genera
 * el token, lo guarda en password_reset_tokens (migracion por defecto, ya
 * existia) y despacha la notificacion ResetPassword del propio framework.
 * En local, MAIL_MAILER=log manda el correo a storage/logs/laravel.log en
 * vez de enviarlo de verdad.
 */
class PasswordResetLinkController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return back()->with('status', __($status));
    }
}
