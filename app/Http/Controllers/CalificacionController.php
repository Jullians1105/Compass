<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\EscalaDesempeno;
use App\Models\Evaluacion;
use App\Models\Periodo;
use App\Support\Promedios;
use Illuminate\Http\Request;

/**
 * Modulo de gestion de calificaciones.
 *
 * Por ahora solo la consulta (RF-14), protegida por el middleware 'auth'
 * (RF-01). El registro y la edicion (RF-13) van despues: falta filtrar por
 * rol (RF-05) para que un docente solo pueda calificar sus propias
 * asignaciones, ademas del FormRequest de validacion.
 */
class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $cursos = Curso::with('grado')->orderBy('nombre')->get();

        if ($cursos->isEmpty()) {
            abort(404, 'No hay cursos cargados. Corre: php artisan migrate:fresh --seed');
        }

        $curso = $cursos->firstWhere('id', (int) $request->query('curso')) ?? $cursos->first();

        $asignaciones = $curso->asignaciones()
            ->with(['asignatura', 'docente'])
            ->get()
            ->sortBy(fn ($a) => $a->asignatura->nombre)
            ->values();

        $asignacion = $asignaciones->firstWhere('id', (int) $request->query('asignatura'))
            ?? $asignaciones->first();

        $periodos = Periodo::where('anio_lectivo_id', $curso->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        $escalas = EscalaDesempeno::where('anio_lectivo_id', $curso->anio_lectivo_id)
            ->orderBy('orden')
            ->get();

        $matriculas = $curso->matriculas()
            ->activas()
            ->with('estudiante')
            ->get()
            ->sortBy(fn ($m) => $m->estudiante->apellidos . ' ' . $m->estudiante->nombres)
            ->values();

        // Dos consultas para toda la tabla, sin importar cuantos estudiantes
        // haya. Llamar a Matricula::definitiva() por fila dispararia decenas de
        // consultas (N+1) y el RNF-02 pide el dashboard en menos de 3 segundos.
        $evaluaciones = Evaluacion::where('asignacion_id', $asignacion?->id)
            ->orderBy('periodo_id')
            ->orderBy('id')
            ->get();

        $notas = Calificacion::whereIn('evaluacion_id', $evaluaciones->pluck('id'))
            ->whereIn('matricula_id', $matriculas->pluck('id'))
            ->get()
            ->groupBy('matricula_id');

        $evaluacionesPorPeriodo = $evaluaciones->groupBy('periodo_id');
        $mapaEvaluaciones = $evaluaciones->keyBy('id');

        $filas = $matriculas->map(function ($matricula) use (
            $notas, $periodos, $escalas, $mapaEvaluaciones
        ) {
            $suyas = $notas->get($matricula->id, collect());
            $porPeriodo = [];

            foreach ($periodos as $periodo) {
                // Nota del periodo = promedio ponderado de sus evaluaciones.
                $componentes = $suyas
                    ->filter(fn ($c) => optional($mapaEvaluaciones->get($c->evaluacion_id))->periodo_id === $periodo->id)
                    ->map(fn ($c) => [
                        (float) $c->nota,
                        (float) $mapaEvaluaciones->get($c->evaluacion_id)->porcentaje,
                    ]);

                $porPeriodo[$periodo->id] = Promedios::ponderar($componentes);
            }

            // Definitiva = promedio ponderado de las notas de periodo por el
            // peso de cada periodo, contando solo los periodos ya calificados.
            $definitiva = Promedios::ponderar(
                $periodos
                    ->filter(fn ($p) => $porPeriodo[$p->id] !== null)
                    ->map(fn ($p) => [$porPeriodo[$p->id], (float) $p->porcentaje])
                    ->values()
            );

            $desempeno = $definitiva === null ? null : $escalas->first(
                fn ($e) => $definitiva >= (float) $e->nota_minima
                    && $definitiva <= (float) $e->nota_maxima
            );

            return [
                'estudiante' => $matricula->estudiante,
                'periodos' => $porPeriodo,
                'definitiva' => $definitiva,
                'desempeno' => $desempeno,
            ];
        });

        $distribucion = $escalas->map(fn ($e) => [
            'nombre' => $e->nombre,
            'color' => $e->color,
            'total' => $filas->where('desempeno.id', $e->id)->count(),
        ]);

        // Estadisticas del "bento" superior. Solo sobre filas con definitiva:
        // un curso a mitad de periodo 1 no deberia mostrar un 0% de aprobados
        // por los estudiantes que aun no tienen ninguna nota.
        $conNota = $filas->filter(fn ($f) => $f['definitiva'] !== null);

        $estadisticas = [
            'promedio' => $conNota->isNotEmpty() ? round($conNota->avg('definitiva'), 2) : null,
            'porcentajeAprobados' => $conNota->isNotEmpty()
                ? round($conNota->filter(fn ($f) => $f['desempeno']?->aprueba)->count() / $conNota->count() * 100)
                : null,
            'mejor' => $conNota->sortByDesc('definitiva')->first(),
            'peor' => $conNota->sortBy('definitiva')->first(),
        ];

        return view('calificaciones.index', compact(
            'cursos',
            'curso',
            'asignaciones',
            'asignacion',
            'periodos',
            'escalas',
            'filas',
            'distribucion',
            'evaluacionesPorPeriodo',
            'estadisticas'
        ));
    }
}
