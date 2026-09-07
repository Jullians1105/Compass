<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\EscalaDesempeno;
use App\Models\Evaluacion;
use App\Models\Periodo;
use App\Support\Promedios;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Modulo de gestion de calificaciones.
 *
 * index() es la consulta de solo lectura (RF-14) y planilla()/guardar() son
 * el registro y la edicion (RF-13). Ambas rutas van tras 'auth' (RF-01) y el
 * permiso 'gestion-de-calificaciones' (RF-05).
 *
 * Ese permiso lo tienen admin, coordinador y docente por igual, asi que no
 * alcanza para RF-13: un docente solo puede calificar SUS asignaciones. El
 * filtro extra vive en asignacionesVisibles() y se aplica tanto al armar la
 * planilla como al guardarla — validar solo al mostrar dejaria la escritura
 * abierta a un POST armado a mano.
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

        $estadisticas = $this->estadisticas($filas);

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

    /**
     * RF-13: planilla editable de un periodo.
     *
     * Las columnas escribibles son las EVALUACIONES del periodo elegido, no
     * P1..P4. El diseno de Figma dibuja P1..P4 como celdas de entrada, pero una
     * nota de periodo es el promedio ponderado de sus evaluaciones (Parcial 1
     * 30%, Parcial 2 30%, Final 40%): no es un dato que exista en la tabla y no
     * hay forma de repartir hacia atras un numero escrito ahi. El filtro de
     * "Periodo" que el mismo diseno ya trae es lo que resuelve la diferencia.
     */
    public function planilla(Request $request)
    {
        $cursos = Curso::with('grado')->orderBy('nombre')->get();

        if ($cursos->isEmpty()) {
            abort(404, 'No hay cursos cargados. Corre: php artisan migrate:fresh --seed');
        }

        $curso = $cursos->firstWhere('id', (int) $request->query('curso')) ?? $cursos->first();

        $asignaciones = $this->asignacionesVisibles($curso, $request);
        $asignacion = $asignaciones->firstWhere('id', (int) $request->query('asignatura'))
            ?? $asignaciones->first();

        $periodos = Periodo::where('anio_lectivo_id', $curso->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        $periodo = $periodos->firstWhere('id', (int) $request->query('periodo'))
            ?? $periodos->first();

        $escalas = EscalaDesempeno::where('anio_lectivo_id', $curso->anio_lectivo_id)
            ->orderBy('orden')
            ->get();

        // Todas las evaluaciones de la asignatura sirven para la definitiva;
        // solo las del periodo elegido se vuelven columnas editables.
        $todas = $asignacion === null
            ? collect()
            : Evaluacion::where('asignacion_id', $asignacion->id)->orderBy('periodo_id')->orderBy('id')->get();

        $evaluaciones = $periodo === null
            ? collect()
            : $todas->where('periodo_id', $periodo->id)->values();

        $matriculas = $curso->matriculas()
            ->activas()
            ->with('estudiante')
            ->get()
            ->sortBy(fn ($m) => $m->estudiante->apellidos . ' ' . $m->estudiante->nombres)
            ->values();

        // Mismo patron de dos consultas que index(): traer notas por fila
        // dispararia un N+1 y RNF-02 pide la pantalla en menos de 3 segundos.
        $notas = Calificacion::whereIn('evaluacion_id', $todas->pluck('id'))
            ->whereIn('matricula_id', $matriculas->pluck('id'))
            ->get()
            ->groupBy('matricula_id');

        $mapa = $todas->keyBy('id');

        $filas = $matriculas->map(function ($matricula) use ($notas, $evaluaciones, $periodos, $escalas, $mapa) {
            $suyas = $notas->get($matricula->id, collect())->keyBy('evaluacion_id');

            $porPeriodo = [];

            foreach ($periodos as $p) {
                $componentes = $suyas
                    ->filter(fn ($c) => optional($mapa->get($c->evaluacion_id))->periodo_id === $p->id)
                    ->map(fn ($c) => [(float) $c->nota, (float) $mapa->get($c->evaluacion_id)->porcentaje]);

                $porPeriodo[$p->id] = Promedios::ponderar($componentes);
            }

            $definitiva = Promedios::ponderar(
                $periodos
                    ->filter(fn ($p) => $porPeriodo[$p->id] !== null)
                    ->map(fn ($p) => [$porPeriodo[$p->id], (float) $p->porcentaje])
                    ->values()
            );

            $desempeno = $definitiva === null ? null : $escalas->first(
                fn ($e) => $definitiva >= (float) $e->nota_minima && $definitiva <= (float) $e->nota_maxima
            );

            return [
                'matricula' => $matricula,
                'estudiante' => $matricula->estudiante,
                // Valor actual de cada celda editable, indexado por evaluacion.
                'notas' => $evaluaciones->mapWithKeys(fn ($e) => [
                    $e->id => $suyas->get($e->id)?->nota,
                ])->all(),
                'porPeriodo' => $porPeriodo,
                'definitiva' => $definitiva,
                'desempeno' => $desempeno,
            ];
        });

        // Las estadisticas salen del curso completo y solo despues se corta la
        // pagina. Al reves, el "promedio del grupo" cambiaria al pasar de
        // pagina, que es justo lo que una estadistica de grupo no debe hacer.
        $estadisticas = $this->estadisticas($filas);
        $tendencia = $periodo === null ? null : $this->tendencia($filas, $periodos, $periodo);

        // En la planilla el titular es el promedio DEL PERIODO que se esta
        // calificando, no el de las definitivas. La flecha compara periodo
        // contra periodo: dejar arriba el promedio de definitivas daria una
        // cifra cuya variacion mide otra cosa, que confunde mas que informar.
        if ($periodo !== null) {
            $conPeriodo = $filas->filter(fn ($f) => $f['porPeriodo'][$periodo->id] !== null);

            $estadisticas['promedio'] = $conPeriodo->isNotEmpty()
                ? round($conPeriodo->avg(fn ($f) => $f['porPeriodo'][$periodo->id]), 2)
                : null;

            $estadisticas['rotuloPromedio'] = 'Promedio P' . $periodo->numero;
        }

        $porPagina = 20;
        $paginaActual = LengthAwarePaginator::resolveCurrentPage();

        $filas = new LengthAwarePaginator(
            $filas->forPage($paginaActual, $porPagina)->values(),
            $filas->count(),
            $porPagina,
            $paginaActual,
            // Conserva curso/asignatura/periodo en los enlaces de pagina: sin
            // esto, pasar a la pagina 2 devolveria al curso por defecto.
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('calificaciones.planilla', compact(
            'cursos',
            'curso',
            'asignaciones',
            'asignacion',
            'periodos',
            'periodo',
            'evaluaciones',
            'escalas',
            'filas',
            'estadisticas',
            'tendencia'
        ));
    }

    /**
     * RF-13: guarda la planilla completa de un periodo.
     *
     * Una celda vacia borra la nota en vez de guardar un 0: son cosas
     * distintas — "sin calificar aun" no debe contar como cero en el promedio
     * ni arrastrar al estudiante a riesgo alto (RF-29).
     */
    public function guardar(Request $request): RedirectResponse
    {
        $curso = Curso::findOrFail((int) $request->input('curso'));

        $asignacion = $this->asignacionesVisibles($curso, $request)
            ->firstWhere('id', (int) $request->input('asignatura'));

        abort_unless($asignacion, 403, 'No puedes registrar notas en esa asignatura.');

        $periodo = Periodo::where('anio_lectivo_id', $curso->anio_lectivo_id)
            ->findOrFail((int) $request->input('periodo'));

        $request->validate([
            'notas' => ['array'],
            'notas.*' => ['array'],
            'notas.*.*' => ['nullable', 'numeric', 'between:0,5'],
        ], [
            'notas.*.*.numeric' => 'Las notas deben ser numeros.',
            'notas.*.*.between' => 'Las notas deben estar entre 0.00 y 5.00.',
        ]);

        // Los ids permitidos salen de la BD, no del formulario: sin esto un
        // POST armado a mano podria escribir notas de otro curso o de un
        // periodo que el docente no eligio.
        $evaluacionesValidas = Evaluacion::where('asignacion_id', $asignacion->id)
            ->where('periodo_id', $periodo->id)
            ->pluck('id')
            ->all();

        $matriculasValidas = $curso->matriculas()->activas()->pluck('id')->all();

        $usuarioId = $request->user()->id;
        $guardadas = 0;
        $borradas = 0;

        DB::transaction(function () use (
            $request, $evaluacionesValidas, $matriculasValidas, $usuarioId, &$guardadas, &$borradas
        ) {
            foreach ((array) $request->input('notas', []) as $matriculaId => $porEvaluacion) {
                if (! in_array((int) $matriculaId, $matriculasValidas, true)) {
                    continue;
                }

                foreach ((array) $porEvaluacion as $evaluacionId => $valor) {
                    if (! in_array((int) $evaluacionId, $evaluacionesValidas, true)) {
                        continue;
                    }

                    $calificacion = Calificacion::firstOrNew([
                        'matricula_id' => (int) $matriculaId,
                        'evaluacion_id' => (int) $evaluacionId,
                    ]);

                    if ($valor === null || $valor === '') {
                        if ($calificacion->exists) {
                            $calificacion->delete();
                            $borradas++;
                        }

                        continue;
                    }

                    // Sin cambio real no se toca la fila: evita ensuciar
                    // actualizado_por en cada guardado de la planilla entera.
                    if ($calificacion->exists && (float) $calificacion->nota === (float) $valor) {
                        continue;
                    }

                    $calificacion->nota = (float) $valor;
                    $calificacion->{$calificacion->exists ? 'actualizado_por' : 'registrado_por'} = $usuarioId;
                    $calificacion->save();
                    $guardadas++;
                }
            }
        });

        return redirect()
            ->route('calificaciones.planilla', [
                'curso' => $curso->id,
                'asignatura' => $asignacion->id,
                'periodo' => $periodo->id,
            ])
            ->with('exito', $this->resumenGuardado($guardadas, $borradas));
    }

    /**
     * Asignaciones que el usuario puede calificar en un curso.
     *
     * Un usuario con ficha de docente solo ve las suyas; admin y coordinador
     * no tienen ficha y las ven todas. Un docente sin asignaciones en el curso
     * recibe una coleccion vacia — preferible a mostrarle notas ajenas.
     */
    private function asignacionesVisibles(Curso $curso, Request $request)
    {
        $docente = $request->user()->docente;

        return $curso->asignaciones()
            ->when($docente, fn ($q) => $q->where('docente_id', $docente->id))
            ->with(['asignatura', 'docente'])
            ->get()
            ->sortBy(fn ($a) => $a->asignatura->nombre)
            ->values();
    }

    /**
     * Estadisticas del "bento" superior, compartidas por RF-14 y RF-13.
     *
     * Solo cuentan las filas con definitiva: un curso a mitad del periodo 1 no
     * deberia mostrar 0% de aprobados por los estudiantes que aun no tienen
     * ninguna nota registrada.
     */
    private function estadisticas($filas): array
    {
        $conNota = $filas->filter(fn ($f) => $f['definitiva'] !== null);

        return [
            'promedio' => $conNota->isNotEmpty() ? round($conNota->avg('definitiva'), 2) : null,
            'porcentajeAprobados' => $conNota->isNotEmpty()
                ? round($conNota->filter(fn ($f) => $f['desempeno']?->aprueba)->count() / $conNota->count() * 100)
                : null,
            'mejor' => $conNota->sortByDesc('definitiva')->first(),
            'peor' => $conNota->sortBy('definitiva')->first(),
        ];
    }

    /**
     * Variacion del promedio del grupo frente al periodo anterior (la flecha
     * verde/roja de la primera tarjeta en el diseno).
     *
     * Devuelve null cuando no hay con que comparar — en el periodo 1, o si el
     * anterior aun no tiene notas. Mostrar "0.0" ahi seria mentir: no es que no
     * haya cambiado, es que no hay dato previo.
     */
    private function tendencia($filas, $periodos, $periodo): ?float
    {
        $anterior = $periodos->where('numero', '<', $periodo->numero)->sortByDesc('numero')->first();

        if ($anterior === null) {
            return null;
        }

        $conAmbos = $filas->filter(fn ($f) => $f['porPeriodo'][$periodo->id] !== null
            && $f['porPeriodo'][$anterior->id] !== null);

        if ($conAmbos->isEmpty()) {
            return null;
        }

        return round(
            $conAmbos->avg(fn ($f) => $f['porPeriodo'][$periodo->id])
            - $conAmbos->avg(fn ($f) => $f['porPeriodo'][$anterior->id]),
            2
        );
    }

    private function resumenGuardado(int $guardadas, int $borradas): string
    {
        if ($guardadas === 0 && $borradas === 0) {
            return 'No hubo cambios que guardar.';
        }

        $partes = [];

        if ($guardadas > 0) {
            $partes[] = $guardadas === 1 ? '1 nota guardada' : "{$guardadas} notas guardadas";
        }

        if ($borradas > 0) {
            $partes[] = $borradas === 1 ? '1 nota borrada' : "{$borradas} notas borradas";
        }

        return implode(' y ', $partes) . '.';
    }
}
