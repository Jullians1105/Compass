@extends('layouts.app')

@section('titulo', 'Editar estudiante')

@section('contenido')

    <h1 class="h3 fw-bold text-primary mb-4">Editar: {{ $estudiante->nombre_completo }}</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('estudiantes.update', $estudiante) }}">
                @csrf
                @method('PUT')
                @include('estudiantes._form_personal')

                <div class="mb-3">
                    <label for="grado_id" class="form-label small fw-semibold">Grado (matrícula vigente)</label>
                    <select class="form-select" id="grado_id" name="grado_id" required>
                        @foreach ($grados as $grado)
                            <option value="{{ $grado->id }}"
                                @selected(old('grado_id', $estudiante->matriculaVigente?->curso?->grado_id) == $grado->id)>
                                {{ $grado->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @unless ($estudiante->matriculaVigente)
                        <div class="form-text text-warning">Este estudiante no tiene matrícula vigente; se creará una nueva.</div>
                    @endunless
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold d-block">Estado</label>
                    <div class="form-check form-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1"
                               @checked(old('activo', $estudiante->activo))>
                        <label class="form-check-label" for="activo">Estudiante activo</label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">check</span>
                        Guardar
                    </button>
                    <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection
