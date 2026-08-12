<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Genera docentes FICTICIOS. Mismo criterio de nombres que EstudianteFactory.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Docente>
 */
class DocenteFactory extends Factory
{
    private const NOMBRES = [
        'Carlos', 'Martha', 'Luis', 'Gloria', 'Jorge', 'Patricia', 'Fernando',
        'Claudia', 'Ricardo', 'Sandra', 'Hernan', 'Beatriz', 'Alvaro', 'Yolanda',
        'Oscar', 'Consuelo', 'German', 'Amparo', 'Javier', 'Rocio',
    ];

    private const APELLIDOS = [
        'Rodriguez', 'Gomez', 'Gonzalez', 'Martinez', 'Garcia', 'Lopez',
        'Hernandez', 'Ramirez', 'Sanchez', 'Perez', 'Torres', 'Vargas',
        'Castro', 'Alvarez', 'Moreno', 'Munoz', 'Jimenez', 'Ortiz',
    ];

    public function definition(): array
    {
        $nombres = fake()->randomElement(self::NOMBRES);
        $apellidos = fake()->randomElement(self::APELLIDOS)
            . ' ' . fake()->randomElement(self::APELLIDOS);

        $usuario = strtolower(
            explode(' ', $nombres)[0] . '.' . explode(' ', $apellidos)[0]
        );

        return [
            'user_id' => null,
            'tipo_documento' => 'CC',
            'documento' => fake()->unique()->numerify('##########'),
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'email' => $usuario . fake()->unique()->numberBetween(1, 999) . '@sjc.edu.co',
            'telefono' => '3' . fake()->numerify('#########'),
            'activo' => true,
        ];
    }
}
