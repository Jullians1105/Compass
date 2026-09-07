<?php

namespace Database\Seeders;

use App\Models\Docente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Usuarios de desarrollo (RF-01). Contrasena = el rol, para que sea
        // facil de recordar en local. RF-06 a RF-08 (alta/edicion/consulta
        // de usuarios desde la UI) ya existen en /usuarios para el resto.
        User::factory()->create([
            'nombres' => 'Administrador',
            'apellidos' => 'Compass',
            'email' => 'admin@sjc.edu.co',
            'role_id' => Role::where('slug', 'admin')->value('id'),
            'password' => Hash::make('admin'),
        ]);

        User::factory()->create([
            'nombres' => 'Coordinador',
            'apellidos' => 'de Prueba',
            'email' => 'coordinador@sjc.edu.co',
            'role_id' => Role::where('slug', 'coordinador')->value('id'),
            'password' => Hash::make('coordinador'),
        ]);

        // RF-05: rol con menos permisos que los dos de arriba, util para
        // probar que el control de acceso realmente restringe algo.
        $docenteUser = User::factory()->create([
            'nombres' => 'Docente',
            'apellidos' => 'de Prueba',
            'email' => 'docente@sjc.edu.co',
            'role_id' => Role::where('slug', 'docente')->value('id'),
            'password' => Hash::make('docente'),
        ]);

        $this->call([
            AcademicoSeeder::class,
        ]);

        // RF-13 + RF-05: la planilla editable solo deja calificar las
        // asignaciones del docente que entro, y esa relacion se resuelve por
        // `docentes.user_id`. Sin enlazar la cuenta de prueba a una ficha, el
        // rol 'docente' entra a la pantalla pero no ve ninguna asignatura, y
        // el control de acceso no se puede comprobar en local. Se enlaza
        // despues de AcademicoSeeder porque es el que crea los docentes.
        Docente::whereHas('asignaciones')
            ->orderBy('id')
            ->first()
            ?->update(['user_id' => $docenteUser->id]);
    }
}
