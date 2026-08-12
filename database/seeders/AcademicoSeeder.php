<?php

namespace Database\Seeders;

use App\Models\AnioLectivo;
use App\Models\Asignacion;
use App\Models\Asignatura;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\EscalaDesempeno;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Grado;
use App\Models\Matricula;
use App\Models\Periodo;
use Illuminate\Database\Seeder;

/**
 * Datos FICTICIOS para desarrollo y pruebas.
 *
 * Simula un colegio a mitad del anio lectivo: periodo 1 cerrado, periodo 2 en
 * curso, periodos 3 y 4 por venir.
 *
 * Alcance: solo grados 9, 10 y 11, que es el rango que cubre el proyecto.
 *
 * PROHIBIDO cargar aqui datos reales de estudiantes (README / Ley 1581).
 */
class AcademicoSeeder extends Seeder
{
    /** Areas obligatorias segun el articulo 23 de la Ley 115 de 1994. */
    private const ASIGNATURAS = [
        ['MAT', 'Matematicas', 'Matematicas', 5],
        ['LEN', 'Lengua Castellana', 'Humanidades', 4],
        ['ING', 'Ingles', 'Humanidades', 3],
        ['SOC', 'Ciencias Sociales', 'Ciencias Sociales', 4],
        ['NAT', 'Ciencias Naturales', 'Ciencias Naturales', 4],
        ['FIS', 'Educacion Fisica', 'Educacion Fisica y Deportes', 2],
        ['ART', 'Educacion Artistica', 'Educacion Artistica', 2],
        ['TEC', 'Tecnologia e Informatica', 'Tecnologia e Informatica', 2],
        ['ETI', 'Etica y Valores', 'Etica y Valores Humanos', 1],
        ['REL', 'Educacion Religiosa', 'Educacion Religiosa', 1],
    ];

    public function run(): void
    {
        $anio = $this->crearAnioLectivo();
        $periodos = $this->crearPeriodos($anio);
        $this->crearEscalaDesempeno($anio);

        $grados = $this->crearGrados();
        $asignaturas = $this->crearAsignaturas();
        $docentes = Docente::factory(18)->create();

        $cursos = $this->crearCursos($anio, $grados, $docentes);
        $asignaciones = $this->crearAsignaciones($anio, $cursos, $asignaturas, $docentes);
        $matriculas = $this->matricularEstudiantes($anio, $cursos);

        // Solo los dos primeros periodos tienen notas: el colegio esta a mitad
        // de anio. Si se sembraran los 4, no habria forma de probar la vista
        // de "periodo en curso" ni el calculo de definitiva parcial.
        $evaluaciones = $this->crearEvaluaciones($asignaciones, $periodos->take(2));
        $this->calificar($matriculas, $evaluaciones, $asignaciones->pluck('curso_id', 'id'));

        $this->command->newLine();
        $this->command->info('Datos de prueba generados:');
        $this->command->line('  Cursos        : ' . $cursos->count());
        $this->command->line('  Estudiantes   : ' . $matriculas->count());
        $this->command->line('  Asignaciones  : ' . $asignaciones->count());
        $this->command->line('  Evaluaciones  : ' . $evaluaciones->count());
        $this->command->line('  Calificaciones: ' . Calificacion::count());
    }

    private function crearAnioLectivo(): AnioLectivo
    {
        return AnioLectivo::create([
            'anio' => 2026,
            'fecha_inicio' => '2026-01-26',
            'fecha_fin' => '2026-11-27',
            'activo' => true,
            'cerrado' => false,
        ]);
    }

    /**
     * Cuatro periodos de 25%, como muestra el prototipo de Stitch.
     */
    private function crearPeriodos(AnioLectivo $anio)
    {
        $rangos = [
            [1, '2026-01-26', '2026-04-03', true],
            [2, '2026-04-06', '2026-06-12', false],
            [3, '2026-07-06', '2026-09-18', false],
            [4, '2026-09-21', '2026-11-27', false],
        ];

        return collect($rangos)->map(fn ($r) => Periodo::create([
            'anio_lectivo_id' => $anio->id,
            'numero' => $r[0],
            'nombre' => "Periodo {$r[0]}",
            'porcentaje' => 25.00,
            'fecha_inicio' => $r[1],
            'fecha_fin' => $r[2],
            'cerrado' => $r[3],
        ]));
    }

    /**
     * Escala del Decreto 1290. Los cortes numericos son los que usa la mayoria
     * de colegios colombianos, pero el colegio piloto puede cambiarlos sin
     * tocar codigo porque viven en tabla.
     */
    private function crearEscalaDesempeno(AnioLectivo $anio): void
    {
        $niveles = [
            ['Bajo', 0.00, 2.99, false, 'danger', 1],
            ['Basico', 3.00, 3.99, true, 'warning', 2],
            ['Alto', 4.00, 4.59, true, 'primary', 3],
            ['Superior', 4.60, 5.00, true, 'success', 4],
        ];

        foreach ($niveles as $n) {
            EscalaDesempeno::create([
                'anio_lectivo_id' => $anio->id,
                'nombre' => $n[0],
                'nota_minima' => $n[1],
                'nota_maxima' => $n[2],
                'aprueba' => $n[3],
                'color' => $n[4],
                'orden' => $n[5],
            ]);
        }
    }

    /**
     * El alcance del proyecto cubre unicamente los grados 9, 10 y 11.
     *
     * La tabla "grados" no impone ese limite: si mas adelante el colegio
     * quiere incluir 6 a 8, basta agregarlos aqui sin tocar el esquema.
     *
     * Ciclos segun la Ley 115 de 1994: 6 a 9 es Basica Secundaria,
     * 10 y 11 es Media.
     */
    private function crearGrados()
    {
        $definicion = [
            [9, 'Basica Secundaria'],
            [10, 'Media'],
            [11, 'Media'],
        ];

        return collect($definicion)->map(fn ($g) => Grado::create([
            'nombre' => $g[0] . '°',
            'nivel' => $g[0],
            'ciclo' => $g[1],
        ]));
    }

    private function crearAsignaturas()
    {
        return collect(self::ASIGNATURAS)->map(fn ($a) => Asignatura::create([
            'codigo' => $a[0],
            'nombre' => $a[1],
            'area' => $a[2],
            'intensidad_horaria' => $a[3],
            'activa' => true,
        ]));
    }

    /**
     * Un solo curso por grado (9°, 10°, 11°): el colegio piloto no tiene
     * paralelos. 'seccion' sigue existiendo en el esquema (una fila necesita
     * algo ahi por el UNIQUE de la migracion), pero no se le pega al nombre
     * visible porque no hay "10°A" y "10°B" entre los que distinguir.
     */
    private function crearCursos(AnioLectivo $anio, $grados, $docentes)
    {
        $cursos = collect();
        $i = 0;

        foreach ($grados as $grado) {
            $cursos->push(Curso::create([
                'anio_lectivo_id' => $anio->id,
                'grado_id' => $grado->id,
                'seccion' => 'Unica',
                'nombre' => $grado->nombre,
                'director_id' => $docentes[$i % $docentes->count()]->id,
                'cupo' => 35,
            ]));
            $i++;
        }

        return $cursos;
    }

    private function crearAsignaciones(AnioLectivo $anio, $cursos, $asignaturas, $docentes)
    {
        $asignaciones = collect();
        $i = 0;

        foreach ($cursos as $curso) {
            foreach ($asignaturas as $asignatura) {
                $asignaciones->push(Asignacion::create([
                    'anio_lectivo_id' => $anio->id,
                    'curso_id' => $curso->id,
                    'asignatura_id' => $asignatura->id,
                    'docente_id' => $docentes[$i % $docentes->count()]->id,
                ]));
                $i++;
            }
        }

        return $asignaciones;
    }

    private function matricularEstudiantes(AnioLectivo $anio, $cursos)
    {
        $matriculas = collect();

        foreach ($cursos as $curso) {
            $estudiantes = Estudiante::factory(28)->create();

            foreach ($estudiantes as $estudiante) {
                $matriculas->push(Matricula::create([
                    'estudiante_id' => $estudiante->id,
                    'curso_id' => $curso->id,
                    'anio_lectivo_id' => $anio->id,
                    'fecha_matricula' => '2026-01-20',
                    'estado' => 'activa',
                ]));
            }
        }

        return $matriculas;
    }

    /**
     * Crea las evaluaciones de cada asignacion en cada periodo.
     *
     * Tres por periodo, con pesos que suman 100%. Es un esquema comun en
     * colegios colombianos y ademas hace demostrable RF-13: dos evaluaciones
     * del mismo tipo ("parcial") conviviendo en el mismo periodo, que es
     * exactamente lo que el diseno anterior impedia.
     */
    private function crearEvaluaciones($asignaciones, $periodos)
    {
        $plantilla = [
            ['Parcial 1', 'parcial', 30.00],
            ['Parcial 2', 'parcial', 30.00],
            ['Evaluacion Final', 'final', 40.00],
        ];

        $evaluaciones = collect();

        foreach ($asignaciones as $asignacion) {
            foreach ($periodos as $periodo) {
                foreach ($plantilla as $p) {
                    $evaluaciones->push(Evaluacion::create([
                        'asignacion_id' => $asignacion->id,
                        'periodo_id' => $periodo->id,
                        'nombre' => $p[0],
                        'tipo' => $p[1],
                        'porcentaje' => $p[2],
                        'fecha' => $periodo->fecha_fin,
                    ]));
                }
            }
        }

        return $evaluaciones;
    }

    /**
     * Genera las notas.
     *
     * Cada estudiante recibe un "perfil" de rendimiento y sus notas se mueven
     * alrededor de ese perfil. Esto no es capricho: con notas totalmente al
     * azar, todos los estudiantes terminan con promedios parecidos alrededor
     * de 2.5 y la clasificacion de riesgo (RF-29) no tendria nada real que
     * detectar. Con perfiles hay un grupo consistentemente bajo que el sistema
     * si puede identificar, que es justo lo que hay que demostrar.
     *
     * Se usa insert() por lotes en vez de create() por rendimiento: son miles
     * de filas. Eso salta el guard del modelo, asi que el generador acota la
     * nota al rango 0-5 antes de insertar.
     *
     * @param \Illuminate\Support\Collection $cursoPorAsignacion Mapa asignacion_id => curso_id,
     *   ya en memoria desde crearAsignaciones(). Evita acceder a $evaluacion->asignacion por
     *   fila (360 consultas sueltas) solo para saber a que curso pertenece.
     */
    private function calificar($matriculas, $evaluaciones, $cursoPorAsignacion): void
    {
        // Perfiles: [nota base, dispersion]. La distribucion busca parecerse a
        // un colegio real: la mayoria en basico/alto y una minoria en riesgo.
        //
        // El perfil "superior" lleva dispersion baja a proposito. Al promediar
        // 20 notas, el ruido se cancela y el promedio tiende a la base (efecto
        // del teorema del limite central). Con dispersion amplia ningun
        // estudiante alcanzaba el corte de 4.6 y el nivel Superior de la escala
        // no aparecia nunca en los boletines.
        $perfiles = [
            'riesgo' => [2.4, 0.6],
            'medio' => [3.6, 0.5],
            'alto' => [4.3, 0.4],
            'superior' => [4.80, 0.15],
        ];

        // Las evaluaciones se agrupan por curso para saber cuales le
        // corresponden a cada estudiante segun donde este matriculado.
        $evaluacionesPorCurso = $evaluaciones->groupBy(fn ($e) => $cursoPorAsignacion[$e->asignacion_id]);

        $filas = [];
        $ahora = now();

        foreach ($matriculas as $matricula) {
            // 15% en riesgo, 55% medio, 22% alto, 8% superior.
            $sorteo = random_int(1, 100);
            $perfil = match (true) {
                $sorteo <= 15 => 'riesgo',
                $sorteo <= 70 => 'medio',
                $sorteo <= 92 => 'alto',
                default => 'superior',
            };
            [$base, $dispersion] = $perfiles[$perfil];

            foreach ($evaluacionesPorCurso[$matricula->curso_id] as $evaluacion) {
                // Ruido por evaluacion: un estudiante puede ir bien en general
                // y mal en un parcial puntual.
                $ruido = (random_int(-100, 100) / 100) * $dispersion;
                $nota = round(max(0.0, min(5.0, $base + $ruido)), 2);

                $filas[] = [
                    'matricula_id' => $matricula->id,
                    'evaluacion_id' => $evaluacion->id,
                    'nota' => $nota,
                    'observacion' => null,
                    'registrado_por' => null,
                    'actualizado_por' => null,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ];
            }

            if (count($filas) >= 2000) {
                Calificacion::insert($filas);
                $filas = [];
            }
        }

        if ($filas !== []) {
            Calificacion::insert($filas);
        }
    }
}
