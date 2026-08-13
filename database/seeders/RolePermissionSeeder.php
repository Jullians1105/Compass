<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Modulos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * RF-04: catalogo de permisos y roles por defecto.
 *
 * El catalogo de permisos sale de App\Support\Modulos (los 9 modulos del
 * README) mas 'roles-y-permisos' (RF-04) y 'gestion-de-usuarios' (RF-06 a
 * RF-08), que son pantallas de administracion, no modulos academicos. No hay
 * nada mas granular todavia porque no hay mas acciones definidas dentro de
 * cada modulo.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = collect(Modulos::todos())
            ->pluck('permiso')
            ->push('roles-y-permisos')
            ->push('gestion-de-usuarios')
            ->unique()
            ->values();

        $permisos->each(fn (string $slug) => Permission::firstOrCreate(
            ['slug' => $slug],
            ['nombre' => Str::headline($slug)]
        ));

        $todos = Permission::pluck('id')->all();
        $soloCalificaciones = Permission::whereIn('slug', ['gestion-de-calificaciones'])->pluck('id')->all();

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema.']
        );
        $admin->permissions()->sync($todos);

        $coordinador = Role::firstOrCreate(
            ['slug' => 'coordinador'],
            ['nombre' => 'Coordinador', 'descripcion' => 'Acceso academico, sin gestion de roles.']
        );
        $coordinador->permissions()->sync(
            Permission::whereNotIn('slug', ['roles-y-permisos', 'gestion-de-usuarios'])->pluck('id')
        );

        $docente = Role::firstOrCreate(
            ['slug' => 'docente'],
            ['nombre' => 'Docente', 'descripcion' => 'Acceso a sus propios cursos y asignaturas.']
        );
        $docente->permissions()->sync($soloCalificaciones);
    }
}
