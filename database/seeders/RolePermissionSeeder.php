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
 * README) mas 'roles-y-permisos' (RF-04), 'gestion-de-usuarios' (RF-06 a
 * RF-08), los dos de estudiantes (RF-09 a RF-11) y 'auditoria' (RNF-11), que
 * son pantallas de administracion o entidades compartidas, no modulos
 * academicos del README. No hay nada mas granular todavia porque no hay mas
 * acciones definidas dentro de cada modulo.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = collect(Modulos::todos())
            ->pluck('permiso')
            ->push('roles-y-permisos')
            ->push('gestion-de-usuarios')
            // RF-09/RF-10 (Administrativo): alta y edicion de estudiantes.
            ->push('gestion-de-estudiantes')
            // RF-11 (Docente / Administrativo): solo consultar el perfil.
            ->push('consulta-de-estudiantes')
            // RNF-11: ver el log de auditoria. Solo admin — igual que roles
            // y usuarios, es informacion de seguridad, no un modulo academico.
            ->push('auditoria')
            ->unique()
            ->values();

        $permisos->each(fn (string $slug) => Permission::firstOrCreate(
            ['slug' => $slug],
            ['nombre' => Str::headline($slug)]
        ));

        $todos = Permission::pluck('id')->all();

        // RF-15 nombra al docente como actor del registro de asistencia, asi
        // que el rol lo necesita ademas de calificaciones. El controller acota
        // despues a los cursos que cada docente dirige.
        $soloDocente = Permission::whereIn('slug', [
            'gestion-de-calificaciones',
            'consulta-de-estudiantes',
            'asistencia-y-puntualidad',
        ])->pluck('id')->all();

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
            Permission::whereNotIn('slug', ['roles-y-permisos', 'gestion-de-usuarios', 'auditoria'])->pluck('id')
        );

        $docente = Role::firstOrCreate(
            ['slug' => 'docente'],
            ['nombre' => 'Docente', 'descripcion' => 'Acceso a sus propios cursos y asignaturas.']
        );
        $docente->permissions()->sync($soloDocente);
    }
}
