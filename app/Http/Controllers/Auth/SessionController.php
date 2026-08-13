<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * RF-01 (login) y RF-02 (logout). La recuperacion de contrasena (RF-03) vive
 * en PasswordResetLinkController/NewPasswordController; la gestion de roles
 * (RF-04) en RoleController.
 */
class SessionController extends Controller
{
    public function create(): \Illuminate\View\View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            throw ValidationException::withMessages([
                'email' => 'Esas credenciales no coinciden con ningun registro.',
            ]);
        }

        // RF-07: el estado de cuenta (activo/inactivo) tiene que restringir
        // algo de verdad, si no el toggle de /usuarios no serviria de nada.
        if (! Auth::user()->activo) {
            Auth::logout();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta esta inactiva. Contacta a un administrador.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('inicio'));
    }

    // RF-02: cierre de sesion. Invalida la sesion del lado del servidor y
    // regenera el token CSRF, que hace las veces de "invalidacion de token"
    // en una app de sesiones Laravel (no hay JWT: la tesis especifica Blade,
    // ver NORMAS_DESARROLLO.md).
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
