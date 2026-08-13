@csrf

<div class="mb-3">
    <label for="nombre" class="form-label small fw-semibold">Nombre del rol</label>
    <input type="text" class="form-control" id="nombre" name="nombre"
           value="{{ old('nombre', $role->nombre ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label small fw-semibold">Descripcion</label>
    <input type="text" class="form-control" id="descripcion" name="descripcion"
           value="{{ old('descripcion', $role->descripcion ?? '') }}">
</div>

<div class="mb-4">
    <label class="form-label small fw-semibold d-block">Permisos</label>
    @php $seleccionados = old('permisos', isset($role) ? $role->permissions->pluck('id')->all() : []); @endphp
    <div class="row row-cols-1 row-cols-md-2 g-2">
        @foreach ($permisos as $permiso)
            <div class="col">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="permiso-{{ $permiso->id }}"
                           name="permisos[]" value="{{ $permiso->id }}"
                           @checked(in_array($permiso->id, $seleccionados))>
                    <label class="form-check-label" for="permiso-{{ $permiso->id }}">{{ $permiso->nombre }}</label>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
        <span class="material-symbols-outlined">check</span>
        Guardar
    </button>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
