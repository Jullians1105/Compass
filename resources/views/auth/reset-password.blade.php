@extends('layouts.auth')

@section('titulo', 'Nueva contraseña')
@section('encabezado', 'Elige una contraseña nueva')
@section('descripcion', 'Minimo 8 caracteres. Escribela dos veces para confirmar que no hay errores de tecleo.')

@section('formulario')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="form-label d-flex align-items-center gap-2 mb-1">
                <span class="material-symbols-outlined" style="font-size: 1rem;">mail</span>
                Correo electronico
            </label>
            {{-- El correo llega en el enlace del email, asi que ya viene lleno.
                 Se deja editable por si el usuario abrio el enlace con otra
                 cuenta a medio escribir, pero no roba el foco: el primer campo
                 que toca llenar es la contrasena. --}}
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email"
                   value="{{ old('email', $email) }}"
                   autocomplete="username" required>
        </div>

        @include('auth._campo-clave', [
            'id' => 'password',
            'nombre' => 'password',
            'etiqueta' => 'Contraseña nueva',
            'autocomplete' => 'new-password',
            'minimo' => 8,
        ])

        @include('auth._campo-clave', [
            'id' => 'password_confirmation',
            'nombre' => 'password_confirmation',
            'etiqueta' => 'Confirmar contraseña',
            'autocomplete' => 'new-password',
            'minimo' => 8,
        ])

        <button type="submit" class="btn compass-login-entrar w-100 d-flex align-items-center justify-content-center gap-2">
            Guardar contraseña
            <span class="material-symbols-outlined" style="font-size: 1.125rem;">check</span>
        </button>
    </form>

    <div class="border-top mt-4 pt-3">
        <a href="{{ route('login') }}"
           class="d-flex align-items-center justify-content-center gap-1 text-decoration-none fw-semibold"
           style="font-size: .75rem; color: #1a3a5c;">
            <span class="material-symbols-outlined" style="font-size: 1rem;">arrow_back</span>
            Volver a iniciar sesion
        </a>
    </div>
@endsection
