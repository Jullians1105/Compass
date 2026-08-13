@extends('layouts.app')

@section('titulo', 'Nuevo rol')

@section('contenido')

    <h1 class="h3 fw-bold text-primary mb-4">Nuevo rol</h1>

    <div class="card border-0 shadow-sm" style="max-width: 40rem;">
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('roles.store') }}">
                @include('roles._form')
            </form>
        </div>
    </div>

@endsection
