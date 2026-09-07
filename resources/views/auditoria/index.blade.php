@extends('layouts.app')

@section('titulo', 'Auditoría')

@section('contenido')

    <div class="mb-4">
        <h1 class="h3 fw-semibold compass-titulo mb-1">Auditoría</h1>
        <p class="text-body-secondary mb-0">Accesos y modificaciones registrados por el sistema (RNF-11).</p>
    </div>

    <form method="GET" class="d-flex flex-wrap gap-3 mb-3">
        <input type="search" name="q" class="form-control" style="max-width: 16rem;"
               placeholder="Buscar por usuario" value="{{ request('q') }}">

        <select name="evento" class="form-select" style="max-width: 12rem;" onchange="this.form.submit()">
            <option value="">Todos los eventos</option>
            @foreach ($tiposEvento as $tipo)
                <option value="{{ $tipo }}" @selected(request('evento') === $tipo)>{{ $tipo }}</option>
            @endforeach
        </select>

        <input type="date" name="desde" class="form-control" style="max-width: 10rem;" value="{{ request('desde') }}">
        <input type="date" name="hasta" class="form-control" style="max-width: 10rem;" value="{{ request('hasta') }}">

        <button type="submit" class="btn btn-outline-secondary">Filtrar</button>
        @if (request()->hasAny(['q', 'evento', 'desde', 'hasta']))
            <a href="{{ route('auditoria.index') }}" class="btn btn-link">Limpiar</a>
        @endif
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>Sobre</th>
                        <th>Detalle</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eventos as $evento)
                        <tr>
                            <td class="text-nowrap">{{ $evento->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $evento->usuario?->nombre_completo ?? '—' }}</td>
                            <td>
                                <span class="badge text-bg-light border">{{ $evento->evento }}</span>
                            </td>
                            <td>
                                @if ($evento->auditable_type)
                                    {{ class_basename($evento->auditable_type) }} #{{ $evento->auditable_id }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small text-body-secondary">
                                {{ $evento->detalle ? json_encode($evento->detalle, JSON_UNESCAPED_UNICODE) : '—' }}
                            </td>
                            <td class="text-body-secondary">{{ $evento->ip ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-4">Sin eventos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $eventos->links() }}
    </div>

@endsection
