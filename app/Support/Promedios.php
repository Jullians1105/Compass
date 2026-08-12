<?php

namespace App\Support;

/**
 * Calculo de promedios ponderados compartido entre Matricula (consulta
 * por-fila) y CalificacionController (consulta en lote para la tabla
 * completa). Vivir en un solo lugar evita que ambos caminos calculen la
 * definitiva distinto si algun dia cambia el redondeo o el manejo de
 * peso-cero.
 */
class Promedios
{
    /**
     * Promedio ponderado de pares [valor, peso]. Null si no hay componentes o
     * si los pesos suman cero (evita una division por cero silenciosa).
     */
    public static function ponderar($componentes): ?float
    {
        if ($componentes->isEmpty()) {
            return null;
        }

        $peso = $componentes->sum(fn ($c) => $c[1]);

        if ($peso <= 0) {
            return null;
        }

        return round($componentes->sum(fn ($c) => $c[0] * $c[1]) / $peso, 2);
    }
}
