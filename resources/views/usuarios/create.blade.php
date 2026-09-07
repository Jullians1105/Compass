@extends('layouts.app')

@section('titulo', 'Nuevo usuario')

@section('contenido')

    <h1 class="h3 fw-semibold compass-titulo mb-4">Nuevo usuario</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <p class="text-body-secondary small">
                Se genera una contraseña temporal y se envia al correo del usuario
                (en local, revisa <code>storage/logs/laravel.log</code>).
            </p>

            <form method="POST" action="{{ route('usuarios.store') }}">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nombres" class="form-label small fw-semibold">Nombres</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label small fw-semibold">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ old('apellidos') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Correo electronico</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="documento" class="form-label small fw-semibold">Documento de identidad</label>
                        <input type="text" class="form-control" id="documento" name="documento" value="{{ old('documento') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="telefono" class="form-label small fw-semibold">Telefono (opcional)</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="role_id" class="form-label small fw-semibold">Rol</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">Selecciona un rol</option>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('role_id') == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">check</span>
                        Crear usuario
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection
