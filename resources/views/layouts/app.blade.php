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
            {{-- text-white explicito: sin el, el enlace toma el azul de $primary,
                 que sobre el azul marino del sidebar casi no se lee. Antes no se
                 notaba porque el sidebar era de ese mismo azul. --}}
            <a href="{{ url('/') }}" class="text-decoration-none text-white px-2 mb-4 mt-1">
                <h1 class="h5 fw-bold mb-0">Compass</h1>
                <p class="compass-sidebar-muted small mb-0">Gestion academica + BI</p>
            </a>

            <nav class="nav flex-column">
                <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('inicio')) active @endif"
                   href="{{ route('inicio') }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Inicio
                </a>
                {{-- RF-05: un modulo solo es clicable si ya tiene pantalla Y el rol
                     tiene el permiso; si no, se ve pero deshabilitado (igual que un
                     modulo sin construir todavia, no distinguimos el motivo). --}}
                @foreach (\App\Support\Modulos::todos() as $modulo)
                    @php
                        // Se resalta por familia de rutas y no por la ruta exacta:
                        // 'calificaciones.index' apunta a la pantalla de entrada,
                        // pero el modulo tiene mas ('calificaciones.planilla' de
                        // RF-13) y todas deben marcar el mismo item del menu.
                        $familiaRutas = \Illuminate\Support\Str::beforeLast($modulo['ruta'] ?? '', '.') . '.*';
                    @endphp
                    @if ($modulo['ruta'] && auth()->user()->tienePermiso($modulo['permiso']))
                        <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs($familiaRutas)) active @endif"
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

                @if (auth()->user()->tienePermiso('consulta-de-estudiantes'))
                    <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('estudiantes.*')) active @endif"
                       href="{{ route('estudiantes.index') }}">
                        <span class="material-symbols-outlined">groups</span>
                        Estudiantes
                    </a>
                @endif

                @if (auth()->user()->tienePermiso('gestion-de-usuarios'))
                    <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('usuarios.*')) active @endif"
                       href="{{ route('usuarios.index') }}">
                        <span class="material-symbols-outlined">group</span>
                        Usuarios
                    </a>
                @endif

                @if (auth()->user()->tienePermiso('roles-y-permisos'))
                    <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('roles.*')) active @endif"
                       href="{{ route('roles.index') }}">
                        <span class="material-symbols-outlined">admin_panel_settings</span>
                        Roles y permisos
                    </a>
                @endif

                @if (auth()->user()->tienePermiso('auditoria'))
                    <a class="nav-link d-flex align-items-center gap-2 py-2 px-2 @if (request()->routeIs('auditoria.*')) active @endif"
                       href="{{ route('auditoria.index') }}">
                        <span class="material-symbols-outlined">fact_check</span>
                        Auditoría
                    </a>
                @endif
            </nav>

            <div class="compass-sidebar-divider mt-auto pt-3 px-2">
                {{-- El diseno pone una foto de perfil aqui; se usan iniciales
                     porque `users` no guarda imagenes. Ver .compass-avatar. --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="compass-avatar">
                        {{ mb_substr(auth()->user()->nombres, 0, 1) }}{{ mb_substr(auth()->user()->apellidos, 0, 1) }}
                    </span>
                    <div class="d-flex flex-column" style="min-width: 0;">
                        <span class="fw-semibold text-truncate" style="font-size: .875rem;">
                            {{ auth()->user()->nombre_completo }}
                        </span>
                        <span class="compass-sidebar-muted text-truncate" style="font-size: .75rem;">
                            {{ auth()->user()->role?->nombre }}
                        </span>
                    </div>
                </div>

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
            <header class="compass-topbar px-3 px-md-4 py-2 d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary d-md-none p-1" id="sidebarToggle" aria-label="Abrir menu">
                    <span class="material-symbols-outlined d-block">menu</span>
                </button>

                {{-- Buscador real, no decorativo: apunta a la consulta de
                     estudiantes que ya existe (EstudianteController@index
                     acepta ?q=). Se oculta a quien no tenga el permiso, porque
                     para esos usuarios cada busqueda terminaria en un 403. --}}
                @if (auth()->user()->tienePermiso('consulta-de-estudiantes'))
                    <form method="GET" action="{{ route('estudiantes.index') }}"
                          class="compass-buscador d-none d-sm-flex align-items-center gap-2 px-3 py-1 flex-grow-1">
                        <span class="material-symbols-outlined text-body-secondary" style="font-size: 1rem;">search</span>
                        <input type="search" name="q" class="form-control p-0"
                               placeholder="Buscar estudiante o registro..."
                               value="{{ request()->routeIs('estudiantes.*') ? request('q') : '' }}"
                               aria-label="Buscar estudiante">

                        {{-- El diseno no dibuja boton aqui, pero sin uno el
                             formulario depende del envio implicito del navegador,
                             que solo aplica mientras haya un unico campo: si
                             manana se agrega un filtro al lado, el Enter deja de
                             funcionar y nadie se entera. Va oculto a la vista y
                             disponible para lectores de pantalla. --}}
                        <button type="submit" class="visually-hidden">Buscar</button>
                    </form>
                @endif

                {{-- En pantallas donde no cabe el buscador, la topbar quedaria
                     casi vacia: ahi se muestra el titulo de la pagina. --}}
                <h2 class="h6 text-body-secondary mb-0 text-truncate d-sm-none">@yield('titulo', config('app.name'))</h2>

                <div class="d-flex align-items-center ms-auto">
                    {{-- Campana y ayuda salen en el diseno, pero todavia no hay
                         a donde mandarlas: el modulo de alertas (EWS) esta
                         pendiente y no existe pantalla de ayuda. Se dejan a la
                         vista y deshabilitadas para conservar la composicion,
                         en vez de simular botones que no hacen nada. --}}
                    <span class="d-none d-md-inline-flex" title="Notificaciones: pendiente hasta que exista el modulo de alertas (EWS)">
                        <button type="button" class="compass-topbar-boton" disabled aria-label="Notificaciones (no disponible)">
                            <span class="material-symbols-outlined">notifications</span>
                        </button>
                    </span>

                    <span class="d-none d-md-inline-flex" title="Ayuda: pendiente, aun no hay pantalla de ayuda">
                        <button type="button" class="compass-topbar-boton" disabled aria-label="Ayuda (no disponible)">
                            <span class="material-symbols-outlined">help</span>
                        </button>
                    </span>

                    <span class="compass-topbar-separador d-none d-md-block"></span>

                    <span class="compass-topbar-usuario text-truncate d-none d-sm-inline">
                        {{ auth()->user()->nombre_completo }}
                    </span>

                    <span class="compass-chip-rol ms-2">{{ auth()->user()->role?->slug }}</span>
                </div>
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
