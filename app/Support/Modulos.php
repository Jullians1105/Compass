<?php

namespace App\Support;

/**
 * Lista de los 9 modulos del sistema (README.md), con su icono de Material
 * Symbols para el sidebar. 'ruta' es null en los modulos que aun no tienen
 * pantalla — se muestran mas no son clicables, hasta que se construyan.
 * 'permiso' es el slug que usa RolePermissionSeeder (RF-04) y el middleware
 * 'permission' (RF-05) para decidir que rol puede ver cada modulo.
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
            ['nombre' => 'Gestion de Calificaciones', 'ruta' => 'calificaciones.index', 'icono' => 'school', 'permiso' => 'gestion-de-calificaciones'],
            ['nombre' => 'Asistencia y Puntualidad', 'ruta' => 'asistencia.index', 'icono' => 'event_available', 'permiso' => 'asistencia-y-puntualidad'],
            ['nombre' => 'Convivencia Escolar', 'ruta' => null, 'icono' => 'diversity_3', 'permiso' => 'convivencia-escolar'],
            ['nombre' => 'Observador Academico', 'ruta' => null, 'icono' => 'visibility', 'permiso' => 'observador-academico'],
            ['nombre' => 'Reportes de Periodo', 'ruta' => null, 'icono' => 'assessment', 'permiso' => 'reportes-de-periodo'],
            ['nombre' => 'Sistema de Alertas Tempranas (EWS)', 'ruta' => null, 'icono' => 'warning', 'permiso' => 'sistema-de-alertas-tempranas-ews'],
            ['nombre' => 'Portal de Acudientes', 'ruta' => null, 'icono' => 'family_restroom', 'permiso' => 'portal-de-acudientes'],
            ['nombre' => 'Gestion de Matriculas', 'ruta' => null, 'icono' => 'person_add', 'permiso' => 'gestion-de-matriculas'],
            ['nombre' => 'Dashboard Financiero', 'ruta' => null, 'icono' => 'payments', 'permiso' => 'dashboard-financiero'],
        ];
    }
}
