<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Landing temporal de verificacion del entorno.
// Se reemplaza por el login (RF-35) al arrancar el Sprint 1.
Route::get('/', function () {
    return view('welcome', [
        'baseDatos' => DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
        'modulos' => [
            'Gestion de Calificaciones',
            'Asistencia y Puntualidad',
            'Convivencia Escolar',
            'Observador Academico',
            'Reportes de Periodo',
            'Sistema de Alertas Tempranas (EWS)',
            'Portal de Acudientes',
        ],
    ]);
});
