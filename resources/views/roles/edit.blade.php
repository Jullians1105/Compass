@extends('layouts.app')

@section('titulo', 'Editar rol')

@section('contenido')

    <h1 class="h3 fw-semibold compass-titulo mb-4">Editar rol: {{ $role->nombre }}</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('roles.update', $role) }}">
                @method('PUT')
                @include('roles._form')
            </form>
        </div>
    </div>

@endsection
