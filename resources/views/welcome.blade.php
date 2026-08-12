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

    {{-- Verificacion del stack: si estas tarjetas se ven con estilo, Bootstrap 5
         compilo bien. Si el grafico de abajo se dibuja, Chart.js tambien. --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 text-body-secondary">Laravel</h2>
                    <p class="h4 mb-0">{{ app()->version() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 text-body-secondary">PHP</h2>
                    <p class="h4 mb-0">{{ PHP_VERSION }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="h6 text-body-secondary">Base de datos</h2>
                    <p class="h4 mb-0">{{ $baseDatos }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">Modulos funcionales</div>
                <ul class="list-group list-group-flush">
                    @foreach ($modulos as $modulo)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $modulo }}
                            <span class="badge text-bg-secondary">Pendiente</span>
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
