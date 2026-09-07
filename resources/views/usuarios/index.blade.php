@extends('layouts.app')

@section('titulo', 'Usuarios')

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">Usuarios del sistema</h1>
            <p class="text-body-secondary mb-0">Cuentas de acceso a Compass, no la ficha de docentes/estudiantes.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <span class="material-symbols-outlined">person_add</span>
            Nuevo usuario
        </a>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- RF-08: filtros GET para que el link sea compartible. --}}
    <form method="GET" class="d-flex flex-wrap gap-3 mb-3">
        <input type="search" name="q" class="form-control" style="max-width: 16rem;"
               placeholder="Buscar por nombre o correo" value="{{ request('q') }}">

        <select name="rol" class="form-select" style="max-width: 12rem;" onchange="this.form.submit()">
            <option value="">Todos los roles</option>
            @foreach ($roles as $rol)
                <option value="{{ $rol->id }}" @selected(request('rol') == $rol->id)>{{ $rol->nombre }}</option>
            @endforeach
        </select>

        <select name="estado" class="form-select" style="max-width: 10rem;" onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            <option value="activo" @selected(request('estado') === 'activo')>Activos</option>
            <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivos</option>
        </select>

        <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
        @if (request()->hasAny(['q', 'rol', 'estado']))
            <a href="{{ route('usuarios.index') }}" class="btn btn-link">Limpiar</a>
        @endif
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td class="fw-semibold">{{ $usuario->nombre_completo }}</td>
                            <td>{{ $usuario->documento ?: '—' }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->role?->nombre ?? '—' }}</td>
                            <td>
                                @if ($usuario->activo)
                                    <span class="badge text-bg-success">Activo</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-4">Sin resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $usuarios->links() }}
    </div>

@endsection
