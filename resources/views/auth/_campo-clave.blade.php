{{--
    Campo de contrasena con boton de mostrar/ocultar.

    Lo usan el login (un campo) y la pantalla de contrasena nueva (dos), asi
    que el marcado del ojo vive aqui y no repetido en cada vista.

    Parametros:
      $id           id del input, tambien lo usa el boton via data-ojo
      $nombre       atributo name
      $etiqueta     texto de la etiqueta
      $autocomplete valor de autocomplete (current-password / new-password)
      $minimo       longitud minima, opcional
--}}
@php
    $minimo = $minimo ?? null;
@endphp

<div class="mb-4">
    <label for="{{ $id }}" class="form-label d-flex align-items-center gap-2 mb-1">
        <span class="material-symbols-outlined" style="font-size: 1rem;">lock</span>
        {{ $etiqueta }}
    </label>

    {{-- El ojo se posiciona sobre el campo, de ahi el position-relative. --}}
    <div class="position-relative">
        <input type="password"
               class="form-control pe-5 @error($nombre) is-invalid @enderror"
               id="{{ $id }}"
               name="{{ $nombre }}"
               autocomplete="{{ $autocomplete }}"
               @if ($minimo) minlength="{{ $minimo }}" @endif
               required>

        <button type="button" class="compass-login-ojo" data-ojo="{{ $id }}"
                aria-label="Mostrar contraseña" aria-pressed="false">
            <span class="material-symbols-outlined d-block" style="font-size: 1.25rem;">visibility</span>
        </button>
    </div>
</div>
