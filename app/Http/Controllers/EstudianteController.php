<?php

namespace App\Http\Controllers;

use App\Models\AnioLectivo;
use App\Models\Auditoria;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Grado;
use App\Models\Matricula;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as ValidationRule;

/**
 * RF-09 a RF-11: estudiantes. No es uno de los 9 modulos del README — es una
 * entidad compartida que usan varios de ellos (Calificaciones, Asistencia,
 * etc.), igual que Docente. Ver docs/estadoProyectoDesarrollo.md.
 *
 * DATOS SENSIBLES DE MENORES (Ley 1581/1098) — ver App\Models\Estudiante.
 */
class EstudianteController extends Controller
{
    /**
     * RF-11 (parcial): no hay un RF dedicado a "listar estudiantes", pero
     * hace falta algo para llegar al ID que RF-11 pide como entrada.
     */
    public function index(Request $request)
    {
        $estudiantes = Estudiante::query()
            ->with(['matriculaVigente.curso.grado'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $termino = "%{$request->query('q')}%";
                $q->where(fn ($sub) => $sub
                    ->where('nombres', 'like', $termino)
                    ->orWhere('apellidos', 'like', $termino)
                    ->orWhere('documento', 'like', $termino));
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('estudiantes.index', ['estudiantes' => $estudiantes]);
    }

    /**
     * RF-11: perfil del estudiante. RNF-10: acceder a datos de un menor
     * queda auditado — es el mecanismo de trazabilidad que pide la Ley 1581,
     * no solo las modificaciones (RNF-11) sino tambien la consulta.
     */
    public function show(Estudiante $estudiante)
    {
        Auditoria::registrar('consultado', $estudiante);

        return view('estudiantes.show', [
            'estudiante' => $estudiante->load('matriculaVigente.curso.grado'),
        ]);
    }

    public function create()
    {
        return view('estudiantes.create', [
            'grados' => Grado::orderBy('nivel')->get(),
        ]);
    }

    /**
     * RF-09: registro. Ademas del estudiante, crea la matricula del grado
     * solicitado en el anio lectivo activo — su ID es el "numero de
     * matricula preliminar" que pide la salida del RF.
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatosPersonales($request);
        $gradoId = $request->validate(['grado_id' => ['required', 'exists:grados,id']])['grado_id'];

        $anioActivo = AnioLectivo::activo()->first();
        abort_unless($anioActivo, 404, 'No hay un año lectivo activo. Corre: php artisan migrate:fresh --seed');

        $curso = Curso::where('grado_id', $gradoId)->where('anio_lectivo_id', $anioActivo->id)->first();
        abort_unless($curso, 404, 'Ese grado no tiene un curso creado en el año lectivo activo.');

        $estudiante = Estudiante::create([...$datos, 'activo' => true]);

        $matricula = Matricula::create([
            'estudiante_id' => $estudiante->id,
            'curso_id' => $curso->id,
            'anio_lectivo_id' => $anioActivo->id,
            'fecha_matricula' => now(),
            'estado' => 'activa',
        ]);

        return redirect()->route('estudiantes.index')->with(
            'status',
            "Estudiante \"{$estudiante->nombre_completo}\" registrado. N.º de matrícula preliminar: {$matricula->id}."
        );
    }

    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.edit', [
            'estudiante' => $estudiante->load('matriculaVigente'),
            'grados' => Grado::orderBy('nivel')->get(),
        ]);
    }

    /**
     * RF-10: actualizacion. "Datos academicos" se interpreta como el
     * grado/curso de la matricula vigente y el estado activo/inactivo — el
     * documento de identidad no se edita aqui (mismo criterio que RF-07).
     */
    public function update(Request $request, Estudiante $estudiante): RedirectResponse
    {
        $datos = $this->validarDatosPersonales($request, $estudiante);
        $extra = $request->validate([
            'grado_id' => ['required', 'exists:grados,id'],
            'activo' => ['required', 'boolean'],
        ]);

        $estudiante->update([...$datos, 'activo' => $extra['activo']]);

        $matricula = $estudiante->matriculaVigente;
        $anioId = $matricula->anio_lectivo_id ?? AnioLectivo::activo()->value('id');
        abort_unless($anioId, 404, 'No hay un año lectivo activo. Corre: php artisan migrate:fresh --seed');

        if (! $matricula || $matricula->curso->grado_id != $extra['grado_id']) {
            $curso = Curso::where('grado_id', $extra['grado_id'])
                ->where('anio_lectivo_id', $anioId)
                ->first();
            abort_unless($curso, 404, 'Ese grado no tiene un curso creado en el año lectivo activo.');

            if ($matricula) {
                $matricula->update(['curso_id' => $curso->id]);
            } else {
                Matricula::create([
                    'estudiante_id' => $estudiante->id,
                    'curso_id' => $curso->id,
                    'anio_lectivo_id' => $anioId,
                    'fecha_matricula' => now(),
                    'estado' => 'activa',
                ]);
            }
        }

        return redirect()->route('estudiantes.index')
            ->with('status', "Estudiante \"{$estudiante->nombre_completo}\" actualizado.");
    }

    private function validarDatosPersonales(Request $request, ?Estudiante $estudiante = null): array
    {
        return $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'string', 'max:5'],
            'documento' => [
                'required', 'string', 'max:20',
                ValidationRule::unique('estudiantes', 'documento')->ignore($estudiante?->id),
            ],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'genero' => ['required', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'direccion' => ['required', 'string', 'max:200'],
            'acudiente_nombre' => ['required', 'string', 'max:150'],
            'acudiente_telefono' => ['required', 'string', 'max:30'],
        ]);
    }
}
