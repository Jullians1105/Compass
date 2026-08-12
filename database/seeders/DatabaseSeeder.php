<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario de desarrollo. La contrasena por defecto de UserFactory es
        // "password"; se cambia cuando se implemente RF-35 y RF-36.
        User::factory()->create([
            'name' => 'Coordinador de Prueba',
            'email' => 'coordinador@sjc.edu.co',
        ]);

        $this->call([
            AcademicoSeeder::class,
        ]);
    }
}
