<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Modulo de asistencia y puntualidad (RF-15, RF-16).
 *
 * index()/guardar() son el registro diario (RF-15). La asistencia se toma UNA
 * vez al dia por estudiante, no por asignatura: asi lo define RF-15, cuya
 * entrada es "ID del estudiante, fecha, estado y justificacion".
 *
 * Por eso el filtro por rol no es el mismo que en calificaciones. Alli un
 * docente califica las asignaturas que dicta; aqui, al ser un registro del dia
 * completo, le corresponde a quien dirige el curso (cursos.director_id).
 */
class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $cursos = $this->cursosVisibles($request);
        $fecha = $this->fechaPedida($request);

        if ($cursos->isEmpty()) {
            return view('asistencia.index', [
                'cursos' => $cursos,
                'curso' => null,
                'fecha' => $fecha,
                'filas' => collect(),
                'resumen' => $this->resumenVacio(),
                'porcentajeCurso' => null,
            ]);
        }

        $curso = $cursos->firstWhere('id', (int) $request->query('curso')) ?? $cursos->first();

        $matriculas = $curso->matriculas()
            ->activas()
            ->with('estudiante')
            ->get()
            ->sortBy(fn ($m) => $m->estudiante->apellidos . ' ' . $m->estudiante->nombres)
            ->values();

        // Una sola consulta para todo el dia, indexada por matricula: pedirle
        // el registro a cada fila seria un N+1 sobre 28 estudiantes.
        $registros = Asistencia::whereIn('matricula_id', $matriculas->pluck('id'))
            ->delDia($fecha)
            ->get()
            ->keyBy('matricula_id');

        $filas = $matriculas->map(fn ($matricula) => [
            'matricula' => $matricula,
            'estudiante' => $matricula->estudiante,
            'asistencia' => $registros->get($matricula->id),
        ]);

        return view('asistencia.index', [
            'cursos' => $cursos,
            'curso' => $curso,
            'fecha' => $fecha,
            'filas' => $filas,
            'resumen' => $this->resumen($filas),
            // RF-15 pide devolver el "porcentaje de asistencia actualizado";
            // se calcula sobre todo lo registrado del curso, no solo hoy.
            'porcentajeCurso' => $this->porcentajeCurso($matriculas->pluck('id')),
        ]);
    }

    public function guardar(Request $request): RedirectResponse
    {
        $curso = $this->cursosVisibles($request)
            ->firstWhere('id', (int) $request->input('curso'));

        abort_unless($curso, 403, 'No puedes registrar la asistencia de ese curso.');

        $datos = $request->validate([
            // No se permite registrar el futuro: una asistencia de manana no
            // es un dato, es una suposicion.
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'estados' => ['array'],
            'estados.*' => ['nullable', 'in:' . implode(',', array_keys(Asistencia::ESTADOS))],
            'justificaciones' => ['array'],
            'justificaciones.*' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha.before_or_equal' => 'No se puede registrar asistencia de una fecha futura.',
            'estados.*.in' => 'Estado de asistencia invalido.',
            'justificaciones.*.max' => 'La justificacion no puede pasar de 500 caracteres.',
        ]);

        $matriculasValidas = $curso->matriculas()->activas()->pluck('id')->all();
        $justificaciones = (array) $request->input('justificaciones', []);
        $usuarioId = $request->user()->id;

        $guardados = 0;
        $borrados = 0;

        DB::transaction(function () use (
            $datos, $justificaciones, $matriculasValidas, $usuarioId, &$guardados, &$borrados
        ) {
            foreach ((array) ($datos['estados'] ?? []) as $matriculaId => $estado) {
                if (! in_array((int) $matriculaId, $matriculasValidas, true)) {
                    continue;
                }

                $registro = Asistencia::firstOrNew([
                    'matricula_id' => (int) $matriculaId,
                    'fecha' => $datos['fecha'],
                ]);

                // Sin estado la fila se borra: "no registrado" y "presente" son
                // cosas distintas para el porcentaje de asistencia (RF-16).
                if ($estado === null || $estado === '') {
                    if ($registro->exists) {
                        $registro->delete();
                        $borrados++;
                    }

                    continue;
                }

                // Una justificacion solo tiene sentido si falto o llego tarde.
                // Si el docente marca presente despues de haber escrito una,
                // se descarta en vez de quedar colgando sin significado.
                $justificacion = $estado === 'presente'
                    ? null
                    : (trim((string) ($justificaciones[$matriculaId] ?? '')) ?: null);

                $sinCambios = $registro->exists
                    && $registro->estado === $estado
                    && $registro->justificacion === $justificacion;

                if ($sinCambios) {
                    continue;
                }

                $registro->estado = $estado;
                $registro->justificacion = $justificacion;
                $registro->{$registro->exists ? 'actualizado_por' : 'registrado_por'} = $usuarioId;
                $registro->save();
                $guardados++;
            }
        });

        return redirect()
            ->route('asistencia.index', ['curso' => $curso->id, 'fecha' => $datos['fecha']])
            ->with('exito', $this->resumenGuardado($guardados, $borrados));
    }

    /**
     * Cursos sobre los que el usuario puede tomar asistencia.
     *
     * Un usuario con ficha de docente solo ve los cursos que dirige; admin y
     * coordinador, que no tienen ficha, los ven todos.
     */
    private function cursosVisibles(Request $request)
    {
        $docente = $request->user()->docente;

        return Curso::with(['grado', 'anioLectivo', 'director'])
            ->when($docente, fn ($q) => $q->where('director_id', $docente->id))
            ->orderBy('nombre')
            ->get();
    }

    /** Fecha pedida, acotada a hoy: no se registra asistencia del futuro. */
    private function fechaPedida(Request $request): Carbon
    {
        $hoy = Carbon::today();

        try {
            $fecha = $request->filled('fecha')
                ? Carbon::parse($request->query('fecha'))->startOfDay()
                : $hoy;
        } catch (\Exception $e) {
            return $hoy;
        }

        return $fecha->greaterThan($hoy) ? $hoy : $fecha;
    }

    private function resumenVacio(): array
    {
        return ['presente' => 0, 'ausente' => 0, 'tardanza' => 0, 'sin_registrar' => 0, 'porcentaje' => null];
    }

    private function resumen($filas): array
    {
        $resumen = $this->resumenVacio();

        foreach ($filas as $fila) {
            $estado = $fila['asistencia']?->estado;
            $clave = $estado === null ? 'sin_registrar' : $estado;
            $resumen[$clave]++;
        }

        $registrados = $resumen['presente'] + $resumen['ausente'] + $resumen['tardanza'];

        // El porcentaje se calcula sobre lo registrado, no sobre el total del
        // curso: a media jornada, con 5 de 28 marcados, un 18% seria falso.
        $resumen['porcentaje'] = $registrados > 0
            ? round(($resumen['presente'] + $resumen['tardanza']) / $registrados * 100)
            : null;

        return $resumen;
    }

    /** Porcentaje historico del curso (RF-15: "porcentaje actualizado"). */
    private function porcentajeCurso($matriculaIds): ?int
    {
        $total = Asistencia::whereIn('matricula_id', $matriculaIds)->count();

        if ($total === 0) {
            return null;
        }

        $asistio = Asistencia::whereIn('matricula_id', $matriculaIds)->asistio()->count();

        return (int) round($asistio / $total * 100);
    }

    private function resumenGuardado(int $guardados, int $borrados): string
    {
        if ($guardados === 0 && $borrados === 0) {
            return 'No hubo cambios que guardar.';
        }

        $partes = [];

        if ($guardados > 0) {
            $partes[] = $guardados === 1 ? '1 registro guardado' : "{$guardados} registros guardados";
        }

        if ($borrados > 0) {
            $partes[] = $borrados === 1 ? '1 registro borrado' : "{$borrados} registros borrados";
        }

        return implode(' y ', $partes) . '.';
    }
}
