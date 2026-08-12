<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Genera estudiantes FICTICIOS.
 *
 * Los nombres se curan a mano en vez de usar Faker: la libreria no trae locale
 * es_CO, y con es_ES salen nombres muy peninsulares. Estas listas son de uso
 * comun en Colombia, que es lo que necesita el piloto.
 *
 * NUNCA reemplazar esto por datos reales de estudiantes (README, Ley 1581).
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Estudiante>
 */
class EstudianteFactory extends Factory
{
    private const NOMBRES_M = [
        'Santiago', 'Sebastian', 'Juan Jose', 'Andres', 'Camilo', 'David',
        'Nicolas', 'Samuel', 'Mateo', 'Daniel', 'Diego', 'Miguel Angel',
        'Julian', 'Felipe', 'Alejandro', 'Emmanuel', 'Tomas', 'Simon',
        'Martin', 'Juan Pablo',
    ];

    private const NOMBRES_F = [
        'Sofia', 'Valentina', 'Isabella', 'Mariana', 'Salome', 'Camila',
        'Luciana', 'Gabriela', 'Sara', 'Maria Jose', 'Antonella', 'Juliana',
        'Manuela', 'Daniela', 'Laura', 'Valeria', 'Paula', 'Alejandra',
        'Catalina', 'Ximena',
    ];

    private const APELLIDOS = [
        'Rodriguez', 'Gomez', 'Gonzalez', 'Martinez', 'Garcia', 'Lopez',
        'Hernandez', 'Ramirez', 'Sanchez', 'Perez', 'Diaz', 'Torres',
        'Vargas', 'Castro', 'Ruiz', 'Alvarez', 'Romero', 'Moreno', 'Munoz',
        'Rojas', 'Jimenez', 'Gutierrez', 'Ortiz', 'Cardenas', 'Mendoza',
        'Suarez', 'Guerrero', 'Salazar', 'Quintero', 'Arias', 'Betancur',
        'Ospina', 'Zapata', 'Restrepo', 'Marin', 'Agudelo', 'Velasquez',
    ];

    public function definition(): array
    {
        $genero = fake()->randomElement(['M', 'F']);

        $nombres = $genero === 'M'
            ? fake()->randomElement(self::NOMBRES_M)
            : fake()->randomElement(self::NOMBRES_F);

        $apellidos = fake()->randomElement(self::APELLIDOS)
            . ' ' . fake()->randomElement(self::APELLIDOS);

        // Los estudiantes de bachillerato son menores: Tarjeta de Identidad.
        // El numero arranca con el anio de nacimiento, como las TI reales.
        $nacimiento = fake()->dateTimeBetween('-18 years', '-11 years');

        return [
            'tipo_documento' => 'TI',
            'documento' => $nacimiento->format('y') . fake()->unique()->numerify('########'),
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'fecha_nacimiento' => $nacimiento,
            'genero' => $genero === 'M' ? 'Masculino' : 'Femenino',
            'telefono' => '3' . fake()->numerify('#########'),
            'email' => null,
            'direccion' => fake()->randomElement(['Calle', 'Carrera', 'Diagonal', 'Transversal'])
                . ' ' . fake()->numberBetween(1, 180)
                . ' # ' . fake()->numberBetween(1, 90)
                . '-' . fake()->numberBetween(1, 99),

            // La mayoria con consentimiento firmado, pero no todos: hace falta
            // que existan casos sin consentimiento para probar que el EWS los
            // excluye correctamente.
            'consentimiento_datos' => fake()->boolean(85),
            'fecha_consentimiento' => fake()->dateTimeBetween('-1 year', 'now'),
            'activo' => true,
        ];
    }

    /**
     * Estudiante sin autorizacion de tratamiento de datos.
     */
    public function sinConsentimiento(): static
    {
        return $this->state(fn () => [
            'consentimiento_datos' => false,
            'fecha_consentimiento' => null,
        ]);
    }
}
