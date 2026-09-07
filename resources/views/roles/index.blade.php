@extends('layouts.app')

@section('titulo', 'Roles y permisos')

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">Roles y permisos</h1>
            <p class="text-body-secondary mb-0">Define los roles del sistema y las funcionalidades que puede usar cada uno.</p>
        </div>
        <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <span class="material-symbols-outlined">add</span>
            Nuevo rol
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Descripcion</th>
                        <th>Permisos</th>
                        <th>Usuarios</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $rol)
                        <tr>
                            <td class="fw-semibold">{{ $rol->nombre }}</td>
                            <td class="text-body-secondary">{{ $rol->descripcion ?: '—' }}</td>
                            <td>
                                @forelse ($rol->permissions as $permiso)
                                    <span class="badge text-bg-light border me-1 mb-1">{{ $permiso->nombre }}</span>
                                @empty
                                    <span class="text-body-secondary">Sin permisos</span>
                                @endforelse
                            </td>
                            <td>{{ $rol->usuarios_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('roles.edit', $rol) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
