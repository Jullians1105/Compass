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
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background-color: var(--bs-primary);">

    <main class="w-100 px-3" style="max-width: 24rem;">
        <div class="text-center text-white mb-4">
            <h1 class="h3 fw-bold mb-1">Compass</h1>
            <p class="mb-0 opacity-75">Gestion academica + BI</p>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-1">Bienvenido</h2>
                <p class="text-body-secondary small mb-4">Ingresa tus credenciales para continuar.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Correo electronico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}" placeholder="nombre@sjc.edu.co" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="recordarme" name="recordarme">
                        <label class="form-check-label small" for="recordarme">Recordarme</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        Ingresar
                        <span class="material-symbols-outlined">login</span>
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-white opacity-75 small mt-4 mb-0">
            Compass &mdash; Universidad Manuela Beltran | Ingenieria de Software
        </p>
    </main>

</body>
</html>
