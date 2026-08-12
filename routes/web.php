<?php

use App\Http\Controllers\CalificacionController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Landing temporal de verificacion del entorno.
// Se reemplaza por el login (RF-35) al arrancar el Sprint 1.
Route::get('/', function () {
    return view('welcome', [
        'baseDatos' => DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
        // Los modulos con ruta ya son navegables; el resto siguen pendientes.
        'modulos' => [
            ['nombre' => 'Gestion de Calificaciones', 'ruta' => 'calificaciones.index'],
            ['nombre' => 'Asistencia y Puntualidad', 'ruta' => null],
            ['nombre' => 'Convivencia Escolar', 'ruta' => null],
            ['nombre' => 'Observador Academico', 'ruta' => null],
            ['nombre' => 'Reportes de Periodo', 'ruta' => null],
            ['nombre' => 'Sistema de Alertas Tempranas (EWS)', 'ruta' => null],
            ['nombre' => 'Portal de Acudientes', 'ruta' => null],
        ],
    ]);
})->name('inicio');

// Modulo 1 - Gestion de Calificaciones (RF-02: consultar promedio por periodo).
// Sin middleware de auth todavia: RF-35 y RF-36 aun no estan implementados.
Route::get('/calificaciones', [CalificacionController::class, 'index'])
    ->name('calificaciones.index');
