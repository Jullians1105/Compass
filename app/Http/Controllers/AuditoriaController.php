<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;

/**
 * RNF-11: consulta del log de auditoria. Solo lectura — un log que se puede
 * editar desde la UI no es un log. Actor implicito: Administrativo (permiso
 * 'auditoria', ver RolePermissionSeeder).
 */
class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $eventos = Auditoria::query()
            ->with('usuario')
            ->when($request->filled('evento'), fn ($q) => $q->where('evento', $request->query('evento')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('hasta')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $termino = "%{$request->query('q')}%";
                $q->whereHas('usuario', fn ($sub) => $sub
                    ->where('nombres', 'like', $termino)
                    ->orWhere('apellidos', 'like', $termino)
                    ->orWhere('email', 'like', $termino));
            })
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('auditoria.index', [
            'eventos' => $eventos,
            'tiposEvento' => Auditoria::query()->distinct()->orderBy('evento')->pluck('evento'),
        ]);
    }
}
