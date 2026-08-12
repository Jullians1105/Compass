@extends('layouts.app')

@section('titulo', 'Calificaciones - ' . $curso->nombre)

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold text-primary mb-1">Gestion de Calificaciones</h1>
            <p class="text-body-secondary mb-0">
                {{ $curso->nombre }} &middot; Ano lectivo {{ $curso->anioLectivo->anio }}
                @if ($asignacion?->docente)
                    &middot; Docente: {{ $asignacion->docente->nombre_completo }}
                @endif
            </p>
        </div>

        {{-- Los filtros son GET para que la URL sea compartible: un coordinador
             puede mandarle a un docente el link exacto de su curso. --}}
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
                <select name="asignatura" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach ($asignaciones as $a)
                        <option value="{{ $a->id }}" @selected($a->id === $asignacion?->id)>
                            {{ $a->asignatura->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Bento de estadisticas del grupo (RF-14). Solo considera filas con
         definitiva, para no diluir el % de aprobados con estudiantes que aun
         no tienen ninguna nota registrada. --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border">
                <div class="card-body">
                    <span class="text-uppercase text-body-secondary small fw-semibold">Promedio grupo</span>
                    <div class="h3 text-primary fw-bold mt-2 mb-0">
                        {{ $estadisticas['promedio'] !== null ? number_format($estadisticas['promedio'], 2) : '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border">
                <div class="card-body">
                    <span class="text-uppercase text-body-secondary small fw-semibold">% Aprobados</span>
                    <div class="h3 text-primary fw-bold mt-2 mb-0">
                        {{ $estadisticas['porcentajeAprobados'] !== null ? $estadisticas['porcentajeAprobados'] . '%' : '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border">
                <div class="card-body">
                    <span class="text-uppercase text-body-secondary small fw-semibold">Mejor nota</span>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <span class="h3 text-success fw-bold mb-0">
                            {{ $estadisticas['mejor'] ? number_format($estadisticas['mejor']['definitiva'], 2) : '—' }}
                        </span>
                        @if ($estadisticas['mejor'])
                            <span class="text-body-secondary small fst-italic text-truncate ms-2">
                                {{ $estadisticas['mejor']['estudiante']->nombres }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border">
                <div class="card-body">
                    <span class="text-uppercase text-body-secondary small fw-semibold">Peor nota</span>
                    <div class="d-flex align-items-end justify-content-between mt-2">
                        <span class="h3 text-danger fw-bold mb-0">
                            {{ $estadisticas['peor'] ? number_format($estadisticas['peor']['definitiva'], 2) : '—' }}
                        </span>
                        @if ($estadisticas['peor'])
                            <span class="text-body-secondary small fst-italic text-truncate ms-2">
                                {{ $estadisticas['peor']['estudiante']->nombres }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card overflow-hidden">
                <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-uppercase small text-primary">
                        {{ $asignacion?->asignatura->nombre ?? 'Sin asignatura' }}
                    </span>
                    <span class="badge rounded-pill text-bg-secondary">{{ $filas->count() }} estudiantes</span>
                </div>

                {{-- Tabla completa: desde md hacia arriba. Con P1-P4 + DEF + ESTADO
                     son 8 columnas — en celular no caben ni con scroll horizontal
                     comodo, asi que abajo hay una version en tarjetas. --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem">#</th>
                                <th>ESTUDIANTE</th>
                                @foreach ($periodos as $p)
                                    @php $evals = $evaluacionesPorPeriodo->get($p->id, collect()); @endphp
                                    <th class="text-center text-nowrap"
                                        @if ($evals->isNotEmpty())
                                            title="{{ $evals->map(fn ($e) => $e->nombre . ' (' . (int) $e->porcentaje . '%)')->implode(' · ') }}"
                                        @endif>
                                        P{{ $p->numero }}
                                        <small class="text-body-secondary">({{ (int) $p->porcentaje }}%)</small>
                                        @if ($evals->isNotEmpty())
                                            <br><small class="fw-normal text-body-tertiary">{{ $evals->count() }} evals.</small>
                                        @endif
                                    </th>
                                @endforeach
                                <th class="text-center">DEF.</th>
                                <th class="text-center">ESTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filas as $i => $fila)
                                <tr>
                                    <td class="text-body-secondary">{{ $i + 1 }}</td>
                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="compass-avatar">
                                                {{ mb_substr($fila['estudiante']->nombres, 0, 1) }}{{ mb_substr($fila['estudiante']->apellidos, 0, 1) }}
                                            </span>
                                            <div class="d-flex flex-column">
                                                <span>
                                                    {{ $fila['estudiante']->apellidos }},
                                                    {{ $fila['estudiante']->nombres }}
                                                </span>
                                                @unless ($fila['estudiante']->consentimiento_datos)
                                                    {{-- Ley 1581: sin consentimiento no se le puede
                                                         aplicar analitica de riesgo. --}}
                                                    <span class="badge rounded-pill text-bg-light border align-self-start"
                                                          title="Sin consentimiento de tratamiento de datos">sin consent.</span>
                                                @endunless
                                            </div>
                                        </div>
                                    </td>

                                    @foreach ($periodos as $p)
                                        @php $nota = $fila['periodos'][$p->id] ?? null; @endphp
                                        <td class="text-center">
                                            @if ($nota !== null)
                                                {{-- Dos decimales, igual que la definitiva. Con uno
                                                     solo, 3.53 se ve como 3.5 y la definitiva
                                                     ponderada parece mal calculada. --}}
                                                {{ number_format($nota, 2) }}
                                            @else
                                                <span class="text-body-tertiary">&mdash;</span>
                                            @endif
                                        </td>
                                    @endforeach

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

                {{-- Version en tarjetas: solo en celular (menos de md). Cada
                     estudiante muestra de una vez lo que mas importa (DEF. y
                     ESTADO); el detalle por periodo va en un desplegable para
                     no amontonar ocho columnas en una pantalla angosta. --}}
                <div class="d-md-none">
                    @foreach ($filas as $i => $fila)
                        <div class="p-3 @if (!$loop->last) border-bottom @endif">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div class="d-flex align-items-center gap-2" style="min-width: 0;">
                                    <span class="compass-avatar">
                                        {{ mb_substr($fila['estudiante']->nombres, 0, 1) }}{{ mb_substr($fila['estudiante']->apellidos, 0, 1) }}
                                    </span>
                                    <div class="d-flex flex-column" style="min-width: 0;">
                                        <span class="text-truncate">
                                            {{ $fila['estudiante']->apellidos }}, {{ $fila['estudiante']->nombres }}
                                        </span>
                                        @unless ($fila['estudiante']->consentimiento_datos)
                                            <span class="badge rounded-pill text-bg-light border align-self-start"
                                                  title="Sin consentimiento de tratamiento de datos">sin consent.</span>
                                        @endunless
                                    </div>
                                </div>

                                <div class="text-end flex-shrink-0">
                                    <div class="fw-bold {{ $fila['desempeno'] ? 'text-' . $fila['desempeno']->color : '' }}">
                                        {{ $fila['definitiva'] !== null ? number_format($fila['definitiva'], 2) : '—' }}
                                    </div>
                                    @if ($fila['desempeno'])
                                        <span class="badge rounded-pill bg-{{ $fila['desempeno']->color }}-subtle text-{{ $fila['desempeno']->color }}-emphasis">
                                            {{ $fila['desempeno']->nombre }}
                                        </span>
                                    @else
                                        <span class="text-body-tertiary small">&mdash;</span>
                                    @endif
                                </div>
                            </div>

                            <button type="button"
                                    class="btn btn-sm btn-link text-decoration-none px-0 mt-2 d-flex align-items-center gap-1"
                                    data-bs-toggle="collapse" data-bs-target="#periodos-{{ $i }}">
                                <span class="material-symbols-outlined" style="font-size: 1.1rem;">expand_more</span>
                                Ver periodos
                            </button>

                            <div class="collapse mt-1" id="periodos-{{ $i }}">
                                <div class="row row-cols-2 g-2">
                                    @foreach ($periodos as $p)
                                        @php $nota = $fila['periodos'][$p->id] ?? null; @endphp
                                        <div class="col">
                                            <div class="bg-body-tertiary rounded p-2 text-center">
                                                <div class="text-body-secondary small">
                                                    P{{ $p->numero }} ({{ (int) $p->porcentaje }}%)
                                                </div>
                                                <div class="fw-semibold">
                                                    {{ $nota !== null ? number_format($nota, 2) : '—' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header bg-body-tertiary fw-semibold text-uppercase small text-primary">
                    Distribucion por desempeno
                </div>
                <div class="card-body">
                    <canvas id="graficoDesempeno" height="240"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-body-tertiary fw-semibold text-uppercase small text-primary">
                    Escala vigente (Decreto 1290)
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($escalas as $e)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge rounded-pill bg-{{ $e->color }}-subtle text-{{ $e->color }}-emphasis me-2">
                                    {{ $e->nombre }}
                                </span>
                                {{-- Dos decimales obligatorio: con uno, 2.99 se redondea a 3.0
                                     y los rangos parecen solaparse con el nivel siguiente. --}}
                                <small class="text-body-secondary">
                                    {{ number_format($e->nota_minima, 2) }} &ndash; {{ number_format($e->nota_maxima, 2) }}
                                </small>
                            </span>
                            <small class="{{ $e->aprueba ? 'text-success' : 'text-danger' }}">
                                {{ $e->aprueba ? 'Aprueba' : 'Reprueba' }}
                            </small>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script type="module">
        const datos = @json($distribucion);
        const raiz = getComputedStyle(document.documentElement);

        new window.Chart(document.getElementById('graficoDesempeno'), {
            type: 'doughnut',
            data: {
                labels: datos.map(d => d.nombre),
                datasets: [{
                    data: datos.map(d => d.total),
                    // Los colores salen de las variables de Bootstrap para que
                    // el grafico y los badges de la tabla siempre coincidan.
                    backgroundColor: datos.map(d => raiz.getPropertyValue(`--bs-${d.color}`).trim()),
                }],
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
            },
        });
    </script>

@endsection
