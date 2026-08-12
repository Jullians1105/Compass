<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuarios de desarrollo (RF-01/RF-05). Contrasena = el rol, para que
        // sea facil de recordar en local. Esto es solo para probar login
        // localmente: no hay flujo de alta de usuarios todavia (RF-02 a RF-08
        // sin definir con precision, ver SessionController).
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@sjc.edu.co',
            'role' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        User::factory()->create([
            'name' => 'Coordinador de Prueba',
            'email' => 'coordinador@sjc.edu.co',
            'role' => 'coordinador',
            'password' => Hash::make('coordinador'),
        ]);

        $this->call([
            AcademicoSeeder::class,
        ]);
    }
}
