<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\EscalaDesempeno;
use App\Models\Evaluacion;
use App\Models\Periodo;
use Illuminate\Http\Request;

/**
 * Modulo de gestion de calificaciones.
 *
 * Por ahora solo la consulta (RF-14). El registro y la edicion (RF-13) van
 * despues, cuando exista autenticacion (RF-01) y control de acceso por rol
 * (RF-05) para saber que docente califica y validar que solo toque sus
 * propias asignaciones.
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

                $porPeriodo[$periodo->id] = $this->ponderar($componentes);
            }

            // Definitiva = promedio ponderado de las notas de periodo por el
            // peso de cada periodo, contando solo los periodos ya calificados.
            $definitiva = $this->ponderar(
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

        return view('calificaciones.index', compact(
            'cursos',
            'curso',
            'asignaciones',
            'asignacion',
            'periodos',
            'escalas',
            'filas',
            'distribucion',
            'evaluacionesPorPeriodo'
        ));
    }

    /**
     * Promedio ponderado de pares [valor, peso]. Null si no hay componentes o
     * si los pesos suman cero, para no dividir por cero en silencio.
     */
    private function ponderar($componentes): ?float
    {
        if ($componentes->isEmpty()) {
            return null;
        }

        $peso = $componentes->sum(fn ($c) => $c[1]);

        if ($peso <= 0) {
            return null;
        }

        return round($componentes->sum(fn ($c) => $c[0] * $c[1]) / $peso, 2);
    }
}
