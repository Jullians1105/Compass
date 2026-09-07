@extends('layouts.app')

@section('titulo', $estudiante->nombre_completo)

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">{{ $estudiante->nombre_completo }}</h1>
            <p class="text-body-secondary mb-0">
                @if ($estudiante->activo)
                    <span class="badge text-bg-success">Activo</span>
                @else
                    <span class="badge text-bg-secondary">Inactivo</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @if (auth()->user()->tienePermiso('gestion-de-estudiantes'))
                <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-outline-secondary">Editar</a>
            @endif
            <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">Datos personales</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Documento</span>
                        <span>{{ $estudiante->tipo_documento }} {{ $estudiante->documento }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Fecha de nacimiento</span>
                        <span>{{ $estudiante->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Género</span>
                        <span>{{ $estudiante->genero ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Grado actual</span>
                        <span>{{ $estudiante->matriculaVigente?->curso?->grado?->nombre ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Fecha de registro</span>
                        <span>{{ $estudiante->created_at->format('d/m/Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header">Contacto</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Dirección</span>
                        <span>{{ $estudiante->direccion ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Teléfono</span>
                        <span>{{ $estudiante->telefono ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Email</span>
                        <span>{{ $estudiante->email ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Acudiente</span>
                        <span>{{ $estudiante->acudiente_nombre ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-body-secondary">Teléfono acudiente</span>
                        <span>{{ $estudiante->acudiente_telefono ?? '—' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

@endsection
