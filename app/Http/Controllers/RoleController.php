<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * RF-04: gestion de roles del sistema. Entrada/salida segun la tesis:
 * nombre + permisos asociados -> ID del rol creado/actualizado y la lista
 * completa de roles.
 */
class RoleController extends Controller
{
    public function index()
    {
        return view('roles.index', [
            'roles' => Role::withCount('usuarios')->with('permissions')->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('roles.create', [
            'permisos' => Permission::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        $rol = Role::create([
            'nombre' => $datos['nombre'],
            'slug' => Str::slug($datos['nombre']),
            'descripcion' => $datos['descripcion'],
        ]);

        $rol->permissions()->sync($datos['permisos'] ?? []);

        return redirect()->route('roles.index')->with('status', "Rol \"{$rol->nombre}\" creado.");
    }

    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role' => $role->load('permissions'),
            'permisos' => Permission::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $datos = $this->validarDatos($request, $role);

        $role->update([
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
        ]);

        $role->permissions()->sync($datos['permisos'] ?? []);

        return redirect()->route('roles.index')->with('status', "Rol \"{$role->nombre}\" actualizado.");
    }

    private function validarDatos(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'permisos' => ['array'],
            'permisos.*' => ['integer', 'exists:permissions,id'],
        ]);
    }
}
