@extends('layouts.app')

@section('titulo', 'Estudiantes')

@section('contenido')

    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="h3 fw-semibold compass-titulo mb-1">Estudiantes</h1>
            <p class="text-body-secondary mb-0">Datos de menores — no se muestran fuera de esta pantalla.</p>
        </div>
        @if (auth()->user()->tienePermiso('gestion-de-estudiantes'))
            <a href="{{ route('estudiantes.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <span class="material-symbols-outlined">person_add</span>
                Registrar estudiante
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="GET" class="d-flex flex-wrap gap-3 mb-3">
        <input type="search" name="q" class="form-control" style="max-width: 20rem;"
               placeholder="Buscar por nombre o documento" value="{{ request('q') }}">
        <button type="submit" class="btn btn-outline-secondary">Buscar</button>
        @if (request()->hasAny(['q']))
            <a href="{{ route('estudiantes.index') }}" class="btn btn-link">Limpiar</a>
        @endif
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Documento</th>
                        <th>Grado actual</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($estudiantes as $estudiante)
                        <tr>
                            <td class="fw-semibold">{{ $estudiante->nombre_completo }}</td>
                            <td>{{ $estudiante->documento }}</td>
                            <td>{{ $estudiante->matriculaVigente?->curso?->grado?->nombre ?? '—' }}</td>
                            <td>
                                @if ($estudiante->activo)
                                    <span class="badge text-bg-success">Activo</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('estudiantes.show', $estudiante) }}" class="btn btn-sm btn-outline-secondary">Ver perfil</a>
                                @if (auth()->user()->tienePermiso('gestion-de-estudiantes'))
                                    <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-4">Sin resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $estudiantes->links() }}
    </div>

@endsection
