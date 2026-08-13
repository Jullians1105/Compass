@extends('layouts.app')

@section('titulo', 'Compass - Entorno de desarrollo')

@section('contenido')

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-1">Entorno de desarrollo activo</h1>
            <p class="text-body-secondary mb-0">
                Plataforma de gestion academica + BI para deteccion temprana de riesgo estudiantil.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">Modulos funcionales</div>
                <ul class="list-group list-group-flush">
                    @foreach ($modulos as $modulo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @if ($modulo['ruta'] && auth()->user()->tienePermiso($modulo['permiso']))
                                <a href="{{ route($modulo['ruta']) }}">{{ $modulo['nombre'] }}</a>
                                <span class="badge text-bg-primary">En desarrollo</span>
                            @else
                                <span class="text-body-secondary">{{ $modulo['nombre'] }}</span>
                                <span class="badge text-bg-secondary">Pendiente</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">Prueba de Chart.js</div>
                <div class="card-body">
                    <canvas id="graficoPrueba" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Verificacion del stack, al final y discreta: no es informacion que le
         importe a alguien que solo quiere ver los modulos. Si estos valores
         se leen, Bootstrap 5 y la conexion a BD compilaron/funcionan bien. --}}
    <div class="d-flex flex-wrap gap-4 text-body-secondary small border-top pt-3">
        <span>Laravel <strong class="text-body">{{ app()->version() }}</strong></span>
        <span>PHP <strong class="text-body">{{ PHP_VERSION }}</strong></span>
        <span>Base de datos <strong class="text-body">{{ $baseDatos }}</strong></span>
    </div>

    <script type="module">
        // Smoke test del pipeline de assets: usa la paleta de riesgo del EWS
        // definida como custom properties en resources/css/app.scss.
        const estilos = getComputedStyle(document.documentElement);

        new window.Chart(document.getElementById('graficoPrueba'), {
            type: 'doughnut',
            data: {
                labels: ['Riesgo bajo', 'Riesgo medio', 'Riesgo alto'],
                datasets: [{
                    data: [62, 25, 13],
                    backgroundColor: [
                        estilos.getPropertyValue('--compass-riesgo-bajo').trim(),
                        estilos.getPropertyValue('--compass-riesgo-medio').trim(),
                        estilos.getPropertyValue('--compass-riesgo-alto').trim(),
                    ],
                }],
            },
            options: {
                plugins: { legend: { position: 'bottom' } },
            },
        });
    </script>

@endsection
