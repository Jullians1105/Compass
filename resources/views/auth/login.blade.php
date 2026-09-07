<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesion - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="compass-login">

    {{-- Fondo y velo van en dos capas separadas para poder desenfocar la foto
         sin desenfocar tambien el degradado que va encima. --}}
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
            <h2 class="h5 fw-semibold mb-1" style="color: #0b1c30;">Bienvenido</h2>
            <p class="text-body-secondary mb-4" style="font-size: .875rem;">
                Por favor, ingresa tus credenciales para continuar.
            </p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label d-flex align-items-center gap-2 mb-1">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">person</span>
                        Correo electronico
                    </label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" placeholder="nombre@sjc.edu.co"
                           autocomplete="username" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label d-flex align-items-center gap-2 mb-1">
                        <span class="material-symbols-outlined" style="font-size: 1rem;">lock</span>
                        Contraseña
                    </label>

                    {{-- El ojo se posiciona sobre el campo, de ahi el position-relative
                         en el contenedor. --}}
                    <div class="position-relative">
                        <input type="password" class="form-control pe-5" id="password" name="password"
                               autocomplete="current-password" required>
                        <button type="button" class="compass-login-ojo" id="verClave"
                                aria-label="Mostrar contraseña" aria-pressed="false">
                            <span class="material-symbols-outlined d-block" style="font-size: 1.25rem;">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="recordarme" name="recordarme">
                        <label class="form-check-label" for="recordarme"
                               style="font-size: .75rem; color: {{ '#43474e' }};">Recordarme</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-decoration-none fw-bold"
                       style="font-size: .75rem; color: #1a3a5c;">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit" class="btn compass-login-entrar w-100 d-flex align-items-center justify-content-center gap-2">
                    Ingresar
                    <span class="material-symbols-outlined" style="font-size: 1.125rem;">login</span>
                </button>
            </form>
        </div>

        {{-- El diseno pone aqui "© 2024 Colegio San Jose de Calasanz" y una
             version "v4.2.0". No se copian: el colegio no es el titular de este
             software (es un proyecto de grado de la UMB) y esa version no
             existe. Se conserva el pie real del proyecto. --}}
        <p class="compass-login-pie text-center mt-4 mb-0">
            Compass &mdash; Universidad Manuela Beltran | Ingenieria de Software
        </p>
    </main>

    <script>
        // Mostrar/ocultar contrasena. Sin esto, en un teclado de celular una
        // clave larga se escribe a ciegas y el error mas comun al entrar es
        // simplemente haberla tecleado mal.
        (function () {
            const boton = document.getElementById('verClave');
            const campo = document.getElementById('password');
            const icono = boton.querySelector('.material-symbols-outlined');

            boton.addEventListener('click', function () {
                const oculta = campo.type === 'password';

                campo.type = oculta ? 'text' : 'password';
                icono.textContent = oculta ? 'visibility_off' : 'visibility';
                boton.setAttribute('aria-pressed', oculta ? 'true' : 'false');
                boton.setAttribute('aria-label', oculta ? 'Ocultar contraseña' : 'Mostrar contraseña');
                campo.focus();
            });
        })();
    </script>

</body>
</html>
