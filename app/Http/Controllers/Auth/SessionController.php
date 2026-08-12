<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * RF-01: autenticacion de usuarios.
 *
 * Solo login/logout por ahora. Lo que falta de RF-02 a RF-08 (recuperacion
 * de contrasena, gestion de usuarios desde la UI, bloqueo de cuentas,
 * auditoria de accesos) no esta definido con precision en el documento de
 * tesis disponible en el repo — se deja pendiente en vez de adivinar el
 * alcance.
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

        $request->session()->regenerate();

        return redirect()->intended(route('inicio'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
