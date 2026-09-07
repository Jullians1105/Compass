{{--
    Paginador propio (nodo 1:514 del Figma).

    No se usa el de Laravel ('pagination::bootstrap-5') por tres razones:
      1. Imprime las claves crudas 'pagination.previous' / 'pagination.next'
         como texto visible en movil: el proyecto corre con APP_LOCALE=en y no
         tiene carpeta lang/ publicada.
      2. Trae su propio "Showing X to Y of Z results" en ingles, que duplica el
         "Mostrando ... estudiantes" que ya pone la vista.
      3. Usa flechas ‹ › y el diseno pide botones con texto.

    El estilo vive en .compass-paginacion (app.scss).
--}}
@if ($paginator->hasPages())
    <nav aria-label="Paginacion de la lista">
        <ul class="pagination">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Anterior</a>
                </li>
            @endif

            @foreach ($elements as $elemento)
                {{-- Los "..." que Laravel intercala cuando hay muchas paginas. --}}
                @if (is_string($elemento))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $elemento }}</span>
                    </li>
                @endif

                @if (is_array($elemento))
                    @foreach ($elemento as $pagina => $url)
                        @if ($pagina == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $pagina }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}"
                                   aria-label="Ir a la pagina {{ $pagina }}">{{ $pagina }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
