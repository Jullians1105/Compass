<?php

use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\CalificacionController;
use App\Support\Modulos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// RF-01: autenticacion. 'login' es el nombre que el middleware 'auth' de
// Laravel usa por defecto para redirigir a un usuario no autenticado.
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
});

Route::post('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // Landing temporal de verificacion del entorno.
    Route::get('/', function () {
        return view('welcome', [
            'baseDatos' => DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
            // Los modulos con ruta ya son navegables; el resto siguen pendientes.
            'modulos' => Modulos::todos(),
        ]);
    })->name('inicio');

    // Modulo 1 - Gestion de Calificaciones (RF-14: consultar promedio por periodo).
    Route::get('/calificaciones', [CalificacionController::class, 'index'])
        ->name('calificaciones.index');
});
