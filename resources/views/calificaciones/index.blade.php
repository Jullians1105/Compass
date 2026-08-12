@extends('layouts.app')

@section('titulo', 'Calificaciones - ' . $curso->nombre)

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Gestion de Calificaciones</h1>
            <p class="text-body-secondary mb-0">
                {{ $curso->nombre }} &middot; Ano lectivo {{ $curso->anioLectivo->anio }}
                @if ($asignacion?->docente)
                    &middot; Docente: {{ $asignacion->docente->nombre_completo }}
                @endif
            </p>
        </div>

        {{-- Los filtros son GET para que la URL sea compartible: un coordinador
             puede mandarle a un docente el link exacto de su curso. --}}
        <form method="GET" class="d-flex gap-2">
            <select name="curso" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach ($cursos as $c)
                    <option value="{{ $c->id }}" @selected($c->id === $curso->id)>{{ $c->nombre }}</option>
                @endforeach
            </select>

            <select name="asignatura" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach ($asignaciones as $a)
                    <option value="{{ $a->id }}" @selected($a->id === $asignacion?->id)>
                        {{ $a->asignatura->nombre }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ $asignacion?->asignatura->nombre ?? 'Sin asignatura' }}</span>
                    <span class="badge text-bg-secondary">{{ $filas->count() }} estudiantes</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
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
                                        {{ $fila['estudiante']->apellidos }},
                                        {{ $fila['estudiante']->nombres }}
                                        @unless ($fila['estudiante']->consentimiento_datos)
                                            {{-- Ley 1581: sin consentimiento no se le puede
                                                 aplicar analitica de riesgo. --}}
                                            <span class="badge text-bg-light border ms-1"
                                                  title="Sin consentimiento de tratamiento de datos">sin consent.</span>
                                        @endunless
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

                                    <td class="text-center fw-semibold">
                                        {{ $fila['definitiva'] !== null ? number_format($fila['definitiva'], 2) : '—' }}
                                    </td>

                                    <td class="text-center">
                                        @if ($fila['desempeno'])
                                            <span class="badge text-bg-{{ $fila['desempeno']->color }}">
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
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-header">Distribucion por desempeno</div>
                <div class="card-body">
                    <canvas id="graficoDesempeno" height="240"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Escala vigente (Decreto 1290)</div>
                <ul class="list-group list-group-flush">
                    @foreach ($escalas as $e)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <span class="badge text-bg-{{ $e->color }} me-2">{{ $e->nombre }}</span>
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
