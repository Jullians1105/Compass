<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\CuentaCreada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * RF-06 a RF-08: gestion de usuarios del sistema. Actor = Administrativo.
 */
class UserController extends Controller
{
    /**
     * RF-08: consulta con filtros (rol, estado, busqueda) y paginacion.
     */
    public function index(Request $request)
    {
        $usuarios = User::query()
            ->with('role')
            ->when($request->filled('rol'), fn ($q) => $q->where('role_id', $request->integer('rol')))
            ->when($request->filled('estado'), fn ($q) => $q->where('activo', $request->query('estado') === 'activo'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $termino = "%{$request->query('q')}%";
                $q->where(fn ($sub) => $sub
                    ->where('nombres', 'like', $termino)
                    ->orWhere('apellidos', 'like', $termino)
                    ->orWhere('email', 'like', $termino));
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => Role::orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('usuarios.create', [
            'roles' => Role::orderBy('nombre')->get(),
        ]);
    }

    /**
     * RF-06: registro. Genera una contraseña temporal y la envia por
     * notificacion en vez de dejar que el administrador la elija.
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'documento' => ['required', 'string', 'max:20', 'unique:users,documento'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $passwordTemporal = Str::password(12);

        $usuario = User::create([
            ...$datos,
            'password' => Hash::make($passwordTemporal),
            'activo' => true,
        ]);

        $usuario->notify(new CuentaCreada($passwordTemporal));

        return redirect()->route('usuarios.index')
            ->with('status', "Usuario \"{$usuario->nombre_completo}\" creado. Se envio la contraseña temporal a {$usuario->email}.");
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', [
            'usuario' => $usuario,
            'roles' => Role::orderBy('nombre')->get(),
        ]);
    }

    /**
     * RF-07: actualizacion. Segun la tesis, solo nombre, email, rol y estado
     * de cuenta — el documento de identidad no se edita despues del alta.
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'activo' => ['required', 'boolean'],
        ]);

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('status', "Usuario \"{$usuario->nombre_completo}\" actualizado.");
    }
}
