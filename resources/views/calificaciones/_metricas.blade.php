{{--
    Tarjetas de metricas del encabezado (nodo 1:324 del Figma).

    Lo usan la consulta (RF-14) y la planilla (RF-13). Vive en un parcial para
    que las dos pantallas no terminen mostrando el mismo dato con formatos
    distintos, que es como empezo la divergencia del listado de modulos.

    $estadisticas viene de CalificacionController::estadisticas().
    $tendencia es opcional: solo la planilla la manda, porque necesita un
    periodo seleccionado contra el cual comparar.
--}}
@php
    $tendencia = $tendencia ?? null;
    $aprobados = $estadisticas['porcentajeAprobados'];
@endphp

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="compass-metrica h-100">
            {{-- La planilla manda "Promedio P2" porque ahi el titular es el
                 promedio del periodo que se califica, que es lo que compara la
                 flecha. La consulta no manda nada y queda el de definitivas. --}}
            <span class="compass-metrica-rotulo">{{ $estadisticas['rotuloPromedio'] ?? 'Promedio grupo' }}</span>
            <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                <span class="compass-metrica-valor">
                    {{ $estadisticas['promedio'] !== null ? number_format($estadisticas['promedio'], 2) : '—' }}
                </span>

                @if ($tendencia !== null && $tendencia != 0)
                    @php $subio = $tendencia > 0; @endphp
                    <span class="compass-metrica-tendencia d-flex align-items-center {{ $subio ? 'text-success' : 'text-danger' }}"
                          title="Variacion del promedio frente al periodo anterior">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">
                            {{ $subio ? 'trending_up' : 'trending_down' }}
                        </span>
                        {{ number_format(abs($tendencia), 2) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="compass-metrica h-100">
            <span class="compass-metrica-rotulo">% Aprobados</span>
            <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                <span class="compass-metrica-valor">
                    {{ $aprobados !== null ? $aprobados . '%' : '—' }}
                </span>

                @if ($aprobados !== null)
                    <span class="compass-barra-progreso"
                          role="img"
                          aria-label="{{ $aprobados }} por ciento de estudiantes aprobados">
                        <span style="width: {{ $aprobados }}%;"></span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="compass-metrica h-100">
            <span class="compass-metrica-rotulo">Mejor nota</span>
            <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                <span class="compass-metrica-valor">
                    {{ $estadisticas['mejor'] ? number_format($estadisticas['mejor']['definitiva'], 2) : '—' }}
                </span>
                @if ($estadisticas['mejor'])
                    <span class="compass-metrica-nota text-truncate">
                        {{ mb_substr($estadisticas['mejor']['estudiante']->nombres, 0, 1) }}.
                        {{ $estadisticas['mejor']['estudiante']->apellidos }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="compass-metrica h-100">
            <span class="compass-metrica-rotulo">Peor nota</span>
            <div class="d-flex align-items-end justify-content-between gap-2 mt-2">
                {{-- La peor nota va en rojo aunque el estudiante apruebe: la
                     tarjeta senala donde mirar primero, no si paso o no. --}}
                <span class="compass-metrica-valor es-riesgo">
                    {{ $estadisticas['peor'] ? number_format($estadisticas['peor']['definitiva'], 2) : '—' }}
                </span>
                @if ($estadisticas['peor'])
                    <span class="compass-metrica-nota text-truncate">
                        {{ mb_substr($estadisticas['peor']['estudiante']->nombres, 0, 1) }}.
                        {{ $estadisticas['peor']['estudiante']->apellidos }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
