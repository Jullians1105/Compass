<?php

namespace App\Support;

/**
 * Lista de los 9 modulos del sistema (README.md), con su icono de Material
 * Symbols para el sidebar. 'ruta' es null en los modulos que aun no tienen
 * pantalla — se muestran mas no son clicables, hasta que se construyan.
 *
 * Vive en un solo lugar para que el sidebar y la landing de verificacion no
 * se desincronicen (ver revision de RF-13: dos copias del mismo listado
 * terminan divergiendo).
 */
class Modulos
{
    public static function todos(): array
    {
        return [
            ['nombre' => 'Gestion de Calificaciones', 'ruta' => 'calificaciones.index', 'icono' => 'school'],
            ['nombre' => 'Asistencia y Puntualidad', 'ruta' => null, 'icono' => 'event_available'],
            ['nombre' => 'Convivencia Escolar', 'ruta' => null, 'icono' => 'diversity_3'],
            ['nombre' => 'Observador Academico', 'ruta' => null, 'icono' => 'visibility'],
            ['nombre' => 'Reportes de Periodo', 'ruta' => null, 'icono' => 'assessment'],
            ['nombre' => 'Sistema de Alertas Tempranas (EWS)', 'ruta' => null, 'icono' => 'warning'],
            ['nombre' => 'Portal de Acudientes', 'ruta' => null, 'icono' => 'family_restroom'],
            ['nombre' => 'Gestion de Matriculas', 'ruta' => null, 'icono' => 'person_add'],
            ['nombre' => 'Dashboard Financiero', 'ruta' => null, 'icono' => 'payments'],
        ];
    }
}
