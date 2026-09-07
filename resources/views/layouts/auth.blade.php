<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
{{--
    Cascara comun de las pantallas sin sesion: login, recuperar contrasena y
    elegir contrasena nueva.

    Existe porque las tres repetian el mismo esqueleto completo (head, fuentes,
    fondo, marca, pie). Cuando el login se rediseno segun el Figma, las otras
    dos se quedaron con el estilo viejo y nadie se dio cuenta hasta verlas.
--}}
<body class="compass-login">

    {{-- Fondo y velo en dos capas: asi se desenfoca la foto sin desenfocar
         tambien el degradado que va encima. --}}
    <div class="compass-login-fondo" aria-hidden="true"></div>
    <div class="compass-login-velo" aria-hidden="true"></div>

    <main class="compass-login-caja px-3 py-4">

        <div class="text-center mb-4">
            <div class="compass-login-logo mx-auto mb-3">
                <img src="{{ asset('img/logo-sjc.jpg') }}"
                     alt="Escudo del Colegio San Jose de Calasanz">
            </div>
            <h1 class="compass-login-marca mb-1">Compass</h1>
            <p class="compass-login-lema mb-0">Gestion academica + BI</p>
        </div>

        <div class="compass-login-tarjeta">
            <h2 class="h5 fw-semibold mb-1" style="color: #0b1c30;">@yield('encabezado')</h2>
            <p class="text-body-secondary mb-4" style="font-size: .875rem;">@yield('descripcion')</p>

            @if (session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 py-2" role="alert">
                    <span class="material-symbols-outlined" style="font-size: 1.1rem;">check_circle</span>
                    <span class="small">{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger py-2" role="alert">
                    @if ($errors->count() === 1)
                        <span class="small">{{ $errors->first() }}</span>
                    @else
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->unique() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            @yield('formulario')
        </div>

        <p class="compass-login-pie text-center mt-4 mb-0">
            Compass &mdash; Universidad Manuela Beltran | Ingenieria de Software
        </p>
    </main>

    <script>
        // Mostrar/ocultar contrasena. Se conecta a cualquier boton con
        // data-ojo="<id del campo>", asi sirve igual en el login (un campo) y
        // en la pantalla de contrasena nueva (dos).
        //
        // Sin esto, en un teclado de celular una clave larga se escribe a
        // ciegas y el error mas comun al entrar es haberla tecleado mal.
        document.querySelectorAll('[data-ojo]').forEach(function (boton) {
            const campo = document.getElementById(boton.dataset.ojo);
            const icono = boton.querySelector('.material-symbols-outlined');

            if (!campo) {
                return;
            }

            boton.addEventListener('click', function () {
                const oculta = campo.type === 'password';

                campo.type = oculta ? 'text' : 'password';
                icono.textContent = oculta ? 'visibility_off' : 'visibility';
                boton.setAttribute('aria-pressed', oculta ? 'true' : 'false');
                boton.setAttribute('aria-label', oculta ? 'Ocultar contraseña' : 'Mostrar contraseña');
                campo.focus();
            });
        });
    </script>

</body>
</html>
