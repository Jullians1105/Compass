<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo', config('app.name'))</title>

    {{-- Tipografia e iconos del prototipo de Stitch (docs/prototipos/DesarrolloStitch.txt).
         Un unico request de Google Fonts; el resto del diseño (colores, radios,
         layout) vive en app.scss para no depender de Tailwind. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    {{-- Vite compila resources/css/app.scss (Bootstrap 5) y resources/js/app.js (Chart.js) --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>

    <div class="compass-shell d-flex">
        {{-- Fondo oscuro detras del sidebar en movil, para cerrarlo tocando afuera. --}}
        <div class="compass-sidebar-backdrop" id="sidebarBackdrop"></div>

        <aside class="compass-sidebar d-flex flex-column p-3" id="sidebar">
            <a href="{{ url('/') }}" class="text-decoration-none px-2 mb-4 mt-1">
                <h1 class="h5 fw-bold mb-0">Compass</h1>
                <p class="compass-sidebar-muted small mb-0">Gestion academica + BI</p>
            </a>

            <nav class="nav flex-column">
                <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('inicio')) active @endif"
                   href="{{ route('inicio') }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Inicio
                </a>
                @foreach (\App\Support\Modulos::todos() as $modulo)
                    @if ($modulo['ruta'])
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs($modulo['ruta'] . '*')) active @endif"
                           href="{{ route($modulo['ruta']) }}">
                            <span class="material-symbols-outlined">{{ $modulo['icono'] }}</span>
                            {{ $modulo['nombre'] }}
                        </a>
                    @else
                        <span class="nav-link disabled d-flex align-items-center gap-2 py-2 px-2">
                            <span class="material-symbols-outlined">{{ $modulo['icono'] }}</span>
                            {{ $modulo['nombre'] }}
                        </span>
                    @endif
                @endforeach
            </nav>

            <div class="compass-sidebar-divider mt-auto pt-3 px-2">
                <p class="mb-1 small">
                    {{ auth()->user()->name }}
                    <span class="compass-sidebar-muted d-block text-capitalize">{{ auth()->user()->role }}</span>
                </p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">logout</span>
                        Cerrar sesion
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-grow-1 d-flex flex-column" style="min-width: 0;">
            <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex align-items-center gap-3">
                <button type="button" class="btn btn-outline-secondary d-md-none p-1" id="sidebarToggle" aria-label="Abrir menu">
                    <span class="material-symbols-outlined d-block">menu</span>
                </button>
                <h2 class="h6 text-body-secondary mb-0 text-truncate">@yield('titulo', config('app.name'))</h2>
            </header>

            <main class="container-fluid p-3 p-md-4 flex-grow-1">
                @yield('contenido')
            </main>

            <footer class="border-top py-3">
                <div class="text-center text-body-secondary small px-3">
                    Compass &mdash; Universidad Manuela Beltran | Ingenieria de Software
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Sidebar deslizable en movil: nada de esto corre en escritorio
        // (el boton esta oculto con d-md-none), asi que no interfiere.
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggle = document.getElementById('sidebarToggle');

        const cerrarSidebar = () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        };

        toggle?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', cerrarSidebar);
    </script>

</body>
</html>
