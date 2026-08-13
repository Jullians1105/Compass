@extends('layouts.app')

@section('titulo', 'Editar usuario')

@section('contenido')

    <h1 class="h3 fw-bold text-primary mb-4">Editar usuario: {{ $usuario->nombre_completo }}</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <p class="text-body-secondary small">
                Documento: {{ $usuario->documento ?: '—' }} (no editable aqui).
            </p>

            <form method="POST" action="{{ route('usuarios.update', $usuario) }}">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nombres" class="form-label small fw-semibold">Nombres</label>
                        <input type="text" class="form-control" id="nombres" name="nombres"
                               value="{{ old('nombres', $usuario->nombres) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label small fw-semibold">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos"
                               value="{{ old('apellidos', $usuario->apellidos) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold">Correo electronico</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email', $usuario->email) }}" required>
                </div>

                <div class="mb-3">
                    <label for="role_id" class="form-label small fw-semibold">Rol</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        @foreach ($roles as $rol)
                            <option value="{{ $rol->id }}" @selected(old('role_id', $usuario->role_id) == $rol->id)>{{ $rol->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold d-block">Estado de la cuenta</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                               @checked(old('activo', $usuario->activo))>
                        <label class="form-check-label" for="activo">Cuenta activa</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">check</span>
                        Guardar
                    </button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection
