@extends('layouts.app')

@section('titulo', 'Registro de asistencia' . ($curso ? ' - ' . $curso->nombre : ''))

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">Registro de Asistencia</h1>
            <p class="text-body-secondary mb-0">
                Marca la asistencia diaria por curso.
                @if ($curso?->director)
                    &middot; Director: {{ $curso->director->nombre_completo }}
                @endif
            </p>
        </div>

        {{-- Filtros GET, igual que en calificaciones: la URL queda compartible
             para mandarle a alguien el dia exacto de un curso. --}}
        <form method="GET" class="d-flex flex-wrap gap-3">
            <div>
                <label class="form-label small text-body-secondary mb-1">Curso</label>
                <select name="curso" class="form-select form-select-sm" onchange="this.form.submit()"
                        @disabled($cursos->isEmpty())>
                    @forelse ($cursos as $c)
                        <option value="{{ $c->id }}" @selected($c->id === $curso?->id)>{{ $c->nombre }}</option>
                    @empty
                        <option>Sin cursos</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label class="form-label small text-body-secondary mb-1">Fecha</label>
                {{-- max=hoy: el navegador ya impide elegir el futuro, y el
                     controller lo vuelve a validar por si llega por URL. --}}
                <input type="date" name="fecha" class="form-control form-control-sm"
                       value="{{ $fecha->toDateString() }}"
                       max="{{ now()->toDateString() }}"
                       onchange="this.form.submit()">
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
            <p class="fw-semibold mb-1">No se guardo nada:</p>
            <ul class="mb-0 ps-3">
                @foreach ($errors->unique() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($cursos->isEmpty())
        {{-- Un docente que no dirige ningun curso: la asistencia diaria la toma
             el director de curso, no el profesor de cada asignatura. --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <span class="material-symbols-outlined text-body-tertiary" style="font-size: 3rem;">event_busy</span>
                <p class="h5 mt-3 mb-1">No diriges ningun curso</p>
                <p class="text-body-secondary mb-0">
                    La asistencia diaria la registra el director de curso.
                    Si crees que es un error, pidele a coordinacion que te asigne uno.
                </p>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach (['presente' => 'Presentes', 'ausente' => 'Ausentes', 'tardanza' => 'Tardanzas'] as $clave => $rotulo)
                @php $info = \App\Models\Asistencia::ESTADOS[$clave]; @endphp
                <div class="col-6 col-xl-3">
                    <div class="compass-metrica h-100">
                        <span class="compass-metrica-rotulo">{{ $rotulo }}</span>
                        <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                            <span class="compass-metrica-valor text-{{ $info['color'] }}">
                                {{ $resumen[$clave] }}
                            </span>
                            <span class="material-symbols-outlined text-{{ $info['color'] }}">
                                {{ $info['icono'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-6 col-xl-3">
                <div class="compass-metrica h-100">
                    <span class="compass-metrica-rotulo">Asistencia del dia</span>
                    <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                        <span class="compass-metrica-valor">
                            {{ $resumen['porcentaje'] !== null ? $resumen['porcentaje'] . '%' : '—' }}
                        </span>
                        @if ($resumen['porcentaje'] !== null)
                            <span class="compass-barra-progreso"
                                  role="img"
                                  aria-label="{{ $resumen['porcentaje'] }} por ciento de asistencia hoy">
                                <span style="width: {{ $resumen['porcentaje'] }}%;"></span>
                            </span>
                        @endif
                    </div>
                    @if ($porcentajeCurso !== null)
                        <small class="compass-metrica-nota fst-normal d-block mt-2">
                            Historico del curso: {{ $porcentajeCurso }}%
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('asistencia.guardar') }}" id="formAsistencia">
            @csrf
            @method('PUT')
            <input type="hidden" name="curso" value="{{ $curso->id }}">
            <input type="hidden" name="fecha" value="{{ $fecha->toDateString() }}">

            <div class="card overflow-hidden mb-3">
                <div class="card-header bg-body-tertiary d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <span class="fw-semibold text-uppercase small compass-rotulo">Planilla de asistencia</span>
                        <span class="text-body-secondary small ms-2">
                            {{ $curso->nombre }} &middot; {{ $fecha->translatedFormat('l, d \d\e F Y') }}
                        </span>
                    </div>
                    <span class="badge rounded-pill text-bg-secondary">{{ $filas->count() }} estudiantes</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem">#</th>
                                <th class="text-nowrap">ESTUDIANTE</th>
                                <th class="text-center" style="min-width: 20rem;">ESTADO DE ASISTENCIA</th>
                                <th style="min-width: 14rem;">JUSTIFICACION / OBSERVACION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($filas as $fila)
                                @php
                                    $id = $fila['matricula']->id;
                                    $actual = old("estados.{$id}", $fila['asistencia']?->estado);
                                    $nota = old("justificaciones.{$id}", $fila['asistencia']?->justificacion);
                                @endphp
                                <tr>
                                    <td class="text-body-secondary">{{ $loop->iteration }}</td>

                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="compass-avatar">
                                                {{ mb_substr($fila['estudiante']->nombres, 0, 1) }}{{ mb_substr($fila['estudiante']->apellidos, 0, 1) }}
                                            </span>
                                            <div class="d-flex flex-column">
                                                <span>{{ $fila['estudiante']->apellidos }}, {{ $fila['estudiante']->nombres }}</span>
                                                <small class="text-body-secondary">
                                                    ID: {{ $fila['estudiante']->documento ?? $fila['estudiante']->id }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group compass-estados" role="group"
                                             aria-label="Estado de {{ $fila['estudiante']->apellidos }}">
                                            @foreach (\App\Models\Asistencia::ESTADOS as $clave => $info)
                                                <input type="radio" class="btn-check"
                                                       name="estados[{{ $id }}]"
                                                       id="estado-{{ $id }}-{{ $clave }}"
                                                       value="{{ $clave }}"
                                                       @checked($actual === $clave)>
                                                <label class="btn compass-estado compass-estado-{{ $clave }}"
                                                       for="estado-{{ $id }}-{{ $clave }}">
                                                    {{ $info['nombre'] }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </td>

                                    <td>
                                        {{-- Solo se guarda si el estado no es "presente": una
                                             justificacion sobre alguien que asistio no significa
                                             nada, y el controller la descarta. --}}
                                        <input type="text"
                                               name="justificaciones[{{ $id }}]"
                                               value="{{ $nota }}"
                                               maxlength="500"
                                               class="form-control form-control-sm"
                                               placeholder="Anadir nota..."
                                               aria-label="Justificacion de {{ $fila['estudiante']->apellidos }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-body-secondary">
                        Sin marcar, un estudiante queda como "no registrado" y no cuenta
                        para el porcentaje.
                    </small>

                    <div class="d-flex gap-2">
                        <a href="{{ route('asistencia.index', ['curso' => $curso->id, 'fecha' => $fecha->toDateString()]) }}"
                           class="btn btn-outline-secondary">
                            Descartar cambios
                        </a>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <span class="material-symbols-outlined" style="font-size: 1.1rem;">save</span>
                            Guardar asistencia
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Mismo aviso que en la planilla de notas: marcar 28 estudiantes y
             perderlo por cerrar la pestana es el error mas caro de esta
             pantalla. --}}
        <script type="module">
            const formulario = document.getElementById('formAsistencia');
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
