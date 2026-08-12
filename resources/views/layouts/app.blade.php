<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', config('app.name'))</title>

    {{-- Vite compila resources/css/app.scss (Bootstrap 5) y resources/js/app.js (Chart.js) --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="{{ url('/') }}">
                Compass
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navPrincipal" aria-controls="navPrincipal"
                    aria-expanded="false" aria-label="Alternar navegacion">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navPrincipal">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="{{ url('/') }}">Inicio</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @yield('contenido')
    </main>

    <footer class="border-top py-3 mt-5">
        <div class="container text-center text-body-secondary small">
            Compass &mdash; Universidad Manuela Beltran | Ingenieria de Software
        </div>
    </footer>

</body>
</html>
