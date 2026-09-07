@extends('layouts.auth')

@section('titulo', 'Iniciar sesion')
@section('encabezado', 'Bienvenido')
@section('descripcion', 'Por favor, ingresa tus credenciales para continuar.')

@section('formulario')
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

        @include('auth._campo-clave', [
            'id' => 'password',
            'nombre' => 'password',
            'etiqueta' => 'Contraseña',
            'autocomplete' => 'current-password',
        ])

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" id="recordarme" name="recordarme">
                <label class="form-check-label" for="recordarme"
                       style="font-size: .75rem; color: #43474e;">Recordarme</label>
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
@endsection
