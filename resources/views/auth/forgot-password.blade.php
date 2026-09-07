@extends('layouts.auth')

@section('titulo', 'Recuperar contraseña')
@section('encabezado', 'Recuperar contraseña')
@section('descripcion', 'Escribe tu correo y te enviamos un enlace para elegir una contraseña nueva.')

@section('formulario')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label d-flex align-items-center gap-2 mb-1">
                <span class="material-symbols-outlined" style="font-size: 1rem;">mail</span>
                Correo electronico
            </label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email"
                   value="{{ old('email') }}" placeholder="nombre@sjc.edu.co"
                   autocomplete="username" required autofocus>
        </div>

        <button type="submit" class="btn compass-login-entrar w-100 d-flex align-items-center justify-content-center gap-2">
            Enviar enlace
            <span class="material-symbols-outlined" style="font-size: 1.125rem;">send</span>
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
