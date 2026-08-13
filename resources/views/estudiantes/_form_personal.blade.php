@php $e = $estudiante ?? null; @endphp

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="nombres" class="form-label small fw-semibold">Nombres</label>
        <input type="text" class="form-control" id="nombres" name="nombres" value="{{ old('nombres', $e?->nombres) }}" required>
    </div>
    <div class="col-md-6">
        <label for="apellidos" class="form-label small fw-semibold">Apellidos</label>
        <input type="text" class="form-control" id="apellidos" name="apellidos" value="{{ old('apellidos', $e?->apellidos) }}" required>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label for="tipo_documento" class="form-label small fw-semibold">Tipo doc.</label>
        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
            @foreach (['TI' => 'TI', 'RC' => 'RC', 'CC' => 'CC', 'PPT' => 'PPT', 'PEP' => 'PEP'] as $valor => $etiqueta)
                <option value="{{ $valor }}" @selected(old('tipo_documento', $e?->tipo_documento ?? 'TI') === $valor)>{{ $etiqueta }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label for="documento" class="form-label small fw-semibold">Documento</label>
        <input type="text" class="form-control" id="documento" name="documento" value="{{ old('documento', $e?->documento) }}" required>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="fecha_nacimiento" class="form-label small fw-semibold">Fecha de nacimiento</label>
        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
               value="{{ old('fecha_nacimiento', $e?->fecha_nacimiento?->format('Y-m-d')) }}" required>
    </div>
    <div class="col-md-6">
        <label for="genero" class="form-label small fw-semibold">Género</label>
        <select class="form-select" id="genero" name="genero" required>
            <option value="">Selecciona</option>
            @foreach (['Femenino', 'Masculino', 'Otro'] as $opcion)
                <option value="{{ $opcion }}" @selected(old('genero', $e?->genero) === $opcion)>{{ $opcion }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label for="direccion" class="form-label small fw-semibold">Dirección</label>
    <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion', $e?->direccion) }}" required>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="telefono" class="form-label small fw-semibold">Teléfono (opcional)</label>
        <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono', $e?->telefono) }}">
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label small fw-semibold">Email (opcional)</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $e?->email) }}">
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="acudiente_nombre" class="form-label small fw-semibold">Nombre del acudiente</label>
        <input type="text" class="form-control" id="acudiente_nombre" name="acudiente_nombre" value="{{ old('acudiente_nombre', $e?->acudiente_nombre) }}" required>
    </div>
    <div class="col-md-6">
        <label for="acudiente_telefono" class="form-label small fw-semibold">Teléfono del acudiente</label>
        <input type="text" class="form-control" id="acudiente_telefono" name="acudiente_telefono" value="{{ old('acudiente_telefono', $e?->acudiente_telefono) }}" required>
    </div>
</div>
