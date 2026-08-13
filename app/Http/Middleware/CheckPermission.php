<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RF-05: control de acceso segun rol. Restringe una ruta a los roles cuyo
 * permiso (RF-04) incluya el slug de funcionalidad dado.
 *
 * Uso: Route::middleware('permission:gestion-de-calificaciones'). Se asume
 * que ya paso por 'auth' (siempre va en el mismo grupo de ruta).
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        abort_unless(
            $request->user()->tienePermiso($permiso),
            403,
            'No tienes permiso para acceder a esta funcionalidad.'
        );

        return $next($request);
    }
}
