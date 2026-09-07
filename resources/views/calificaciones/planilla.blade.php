@extends('layouts.app')

@section('titulo', 'Registro de calificaciones - ' . $curso->nombre)

@section('contenido')

    @php
        // Nota minima que aprueba segun la escala vigente (Decreto 1290). Se
        // saca de la escala y no de una constante para que el coloreado de las
        // celdas siga a la institucion si algun dia cambia el corte.
        $notaAprobatoria = $escalas->where('aprueba', true)->min('nota_minima');
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">Registro de Calificaciones</h1>
            <p class="text-body-secondary mb-0">
                {{ $curso->nombre }} &middot; Ano lectivo {{ $curso->anioLectivo->anio }}
                @if ($asignacion?->docente)
                    &middot; Docente: {{ $asignacion->docente->nombre_completo }}
                @endif
            </p>
        </div>

        {{-- Filtros GET, igual que en la consulta: la URL queda compartible
             para mandarle a alguien la planilla exacta de un periodo. --}}
        <form method="GET" class="d-flex flex-wrap gap-3">
            <div>
                <label class="form-label small text-body-secondary mb-1">Curso</label>
                <select name="curso" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach ($cursos as $c)
                        <option value="{{ $c->id }}" @selected($c->id === $curso->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label small text-body-secondary mb-1">Asignatura</label>
                <select name="asignatura" class="form-select form-select-sm" onchange="this.form.submit()"
                        @disabled($asignaciones->isEmpty())>
                    @forelse ($asignaciones as $a)
                        <option value="{{ $a->id }}" @selected($a->id === $asignacion?->id)>
                            {{ $a->asignatura->nombre }}
                        </option>
                    @empty
                        <option>Sin asignaturas</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="form-label small text-body-secondary mb-1">Periodo</label>
                <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach ($periodos as $p)
                        <option value="{{ $p->id }}" @selected($p->id === $periodo?->id)>
                            P{{ $p->numero }} ({{ (int) $p->porcentaje }}%)
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    @if (session('exito'))
        <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('exito') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="fw-semibold mb-1">No se guardo nada. Revisa las notas marcadas:</p>
            <ul class="mb-0 ps-3">
                @foreach ($errors->unique() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($asignaciones->isEmpty())
        {{-- Un docente sin asignaciones en este curso: no es un error, pero
             tampoco tiene nada que calificar aqui. --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <span class="material-symbols-outlined text-body-tertiary" style="font-size: 3rem;">school</span>
                <p class="h5 mt-3 mb-1">No tienes asignaturas en este curso</p>
                <p class="text-body-secondary mb-0">
                    Elige otro curso arriba, o pidele a coordinacion que te asigne una.
                </p>
            </div>
        </div>
    @elseif ($evaluaciones->isEmpty())
        {{-- El periodo existe pero aun no le crearon evaluaciones. Sin ellas no
             hay donde escribir: la nota de periodo se calcula a partir de sus
             evaluaciones, no se captura directo. --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <span class="material-symbols-outlined text-body-tertiary" style="font-size: 3rem;">event_busy</span>
                <p class="h5 mt-3 mb-1">
                    P{{ $periodo?->numero }} no tiene evaluaciones definidas
                </p>
                <p class="text-body-secondary mb-0">
                    Las notas se registran por evaluacion (parciales, quices, final).
                    Hay que crearlas antes de poder calificar este periodo.
                </p>
            </div>
        </div>
    @else
        {{-- Las metricas van sobre el curso completo, no sobre la pagina que se
             esta viendo: son del grupo. Mismo parcial que usa RF-14. --}}
        @include('calificaciones._metricas', [
            'estadisticas' => $estadisticas,
            'tendencia' => $tendencia,
        ])

        <form method="POST" action="{{ route('calificaciones.guardar') }}" id="formPlanilla">
            @csrf
            @method('PUT')
            <input type="hidden" name="curso" value="{{ $curso->id }}">
            <input type="hidden" name="asignatura" value="{{ $asignacion->id }}">
            <input type="hidden" name="periodo" value="{{ $periodo->id }}">

            <div class="card overflow-hidden mb-3">
                <div class="card-header bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold text-uppercase small compass-rotulo">
                            Planilla de calificaciones
                        </span>
                        <span class="text-body-secondary small ms-2">
                            {{ $asignacion->asignatura->nombre }} &middot; P{{ $periodo->numero }}
                        </span>
                    </div>
                    <span class="badge rounded-pill text-bg-secondary">{{ $filas->total() }} estudiantes</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem">#</th>
                                <th class="text-nowrap">ESTUDIANTE</th>

                                @foreach ($evaluaciones as $e)
                                    <th class="text-center text-nowrap" style="min-width: 7rem;">
                                        {{ $e->nombre }}
                                        <br>
                                        <small class="fw-normal text-body-secondary">
                                            {{ $e->tipo_legible }} &middot; {{ (int) $e->porcentaje }}%
                                        </small>
                                    </th>
                                @endforeach

                                <th class="text-center text-nowrap bg-body-secondary-subtle">
                                    P{{ $periodo->numero }}
                                    <br><small class="fw-normal text-body-secondary">calculado</small>
                                </th>
                                <th class="text-center">DEF.</th>
                                <th class="text-center">ESTADO</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($filas as $fila)
                                <tr>
                                    {{-- Numeracion continua entre paginas: en la
                                         pagina 2 el primero es el 21, no el 1. --}}
                                    <td class="text-body-secondary">{{ $filas->firstItem() + $loop->index }}</td>

                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="compass-avatar">
                                                {{ mb_substr($fila['estudiante']->nombres, 0, 1) }}{{ mb_substr($fila['estudiante']->apellidos, 0, 1) }}
                                            </span>
                                            <div class="d-flex flex-column">
                                                <span>
                                                    {{ $fila['estudiante']->apellidos }}, {{ $fila['estudiante']->nombres }}
                                                </span>
                                                <small class="text-body-secondary">
                                                    ID: {{ $fila['estudiante']->documento ?? $fila['estudiante']->id }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    @foreach ($evaluaciones as $e)
                                        @php
                                            $campo = "notas.{$fila['matricula']->id}.{$e->id}";
                                            $valor = old($campo, $fila['notas'][$e->id] ?? null);
                                            $clase = $errors->has($campo)
                                                ? 'is-invalid'
                                                : ($valor === null || $valor === ''
                                                    ? ''
                                                    : ((float) $valor >= (float) $notaAprobatoria
                                                        ? 'compass-nota-aprobada'
                                                        : 'compass-nota-reprobada'));
                                        @endphp
                                        <td class="text-center">
                                            <input type="number"
                                                   name="notas[{{ $fila['matricula']->id }}][{{ $e->id }}]"
                                                   value="{{ $valor !== null && $valor !== '' ? number_format((float) $valor, 2, '.', '') : '' }}"
                                                   step="0.01" min="0" max="5"
                                                   class="form-control form-control-sm text-center compass-celda-nota {{ $clase }}"
                                                   aria-label="Nota de {{ $fila['estudiante']->apellidos }} en {{ $e->nombre }}">
                                        </td>
                                    @endforeach

                                    <td class="text-center fw-semibold bg-body-secondary-subtle">
                                        {{ $fila['porPeriodo'][$periodo->id] !== null
                                            ? number_format($fila['porPeriodo'][$periodo->id], 2)
                                            : '—' }}
                                    </td>

                                    <td class="text-center fw-bold {{ $fila['desempeno'] ? 'text-' . $fila['desempeno']->color : '' }}">
                                        {{ $fila['definitiva'] !== null ? number_format($fila['definitiva'], 2) : '—' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($fila['desempeno'])
                                            <span class="badge rounded-pill bg-{{ $fila['desempeno']->color }}-subtle text-{{ $fila['desempeno']->color }}-emphasis">
                                                {{ $fila['desempeno']->nombre }}
                                            </span>
                                        @else
                                            <span class="text-body-tertiary">&mdash;</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginacion (nodo 1:514). Los enlaces salen del formulario,
                     asi que si hay notas escritas sin guardar salta el aviso de
                     salida que esta al final de la vista. --}}
                <div class="compass-paginacion border-top d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 py-2">
                    <small class="compass-metrica-nota fst-normal">
                        Mostrando {{ $filas->firstItem() ?? 0 }}&ndash;{{ $filas->lastItem() ?? 0 }}
                        de {{ $filas->total() }} estudiantes
                    </small>

                    {{ $filas->links('pagination.compass') }}
                </div>

                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-body-secondary">
                        Deja la celda vacia para borrar una nota. P{{ $periodo->numero }} y la
                        definitiva se recalculan al guardar.
                    </small>

                    <div class="d-flex gap-2">
                        <a href="{{ route('calificaciones.planilla', ['curso' => $curso->id, 'asignatura' => $asignacion->id, 'periodo' => $periodo->id]) }}"
                           class="btn btn-outline-secondary">
                            Descartar cambios
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">save</span>
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Aviso de salida: la planilla no guarda sola, y perder media hora de
             digitacion por cerrar la pestana es el error mas caro de esta
             pantalla. --}}
        <script type="module">
            const formulario = document.getElementById('formPlanilla');
            let sucio = false;

            formulario.addEventListener('input', () => { sucio = true; });
            formulario.addEventListener('submit', () => { sucio = false; });

            window.addEventListener('beforeunload', (evento) => {
                if (sucio) {
                    evento.preventDefault();
                    evento.returnValue = '';
                }
            });
        </script>
    @endif

@endsection
