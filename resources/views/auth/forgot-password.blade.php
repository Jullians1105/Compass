<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Recuperar contraseña - {{ config('app.name') }}</title>

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
                <h2 class="h5 fw-bold mb-1">Recuperar contraseña</h2>
                <p class="text-body-secondary small mb-4">
                    Escribe tu correo y te enviamos un enlace para elegir una contraseña nueva.
                </p>

                @if (session('status'))
                    <div class="alert alert-success py-2 small" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Correo electronico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email') }}" placeholder="nombre@sjc.edu.co" required autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        Enviar enlace
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>

                <a href="{{ route('login') }}" class="d-block text-center small mt-3 text-decoration-none">
                    Volver a iniciar sesion
                </a>
            </div>
        </div>
    </main>

</body>
</html>
