@extends('layouts.app')

@section('titulo', 'Registrar estudiante')

@section('contenido')

    <h1 class="h3 fw-semibold compass-titulo mb-4">Registrar estudiante</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('estudiantes.store') }}">
                @csrf
                @include('estudiantes._form_personal')

                <div class="mb-4">
                    <label for="grado_id" class="form-label small fw-semibold">Grado solicitado</label>
                    <select class="form-select" id="grado_id" name="grado_id" required>
                        <option value="">Selecciona un grado</option>
                        @foreach ($grados as $grado)
                            <option value="{{ $grado->id }}" @selected(old('grado_id') == $grado->id)>{{ $grado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                        <span class="material-symbols-outlined">check</span>
                        Registrar
                    </button>
                    <a href="{{ route('estudiantes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection
