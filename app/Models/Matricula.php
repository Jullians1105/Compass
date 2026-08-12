<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'anio_lectivo_id',
        'fecha_matricula',
        'estado',
        'fecha_retiro',
        'motivo_retiro',
    ];

    protected function casts(): array
    {
        return [
            'fecha_matricula' => 'date',
            'fecha_retiro' => 'date',
        ];
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'anio_lectivo_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Nota de un periodo en una asignatura: promedio ponderado de las
     * evaluaciones de ese periodo por su peso (RF-14).
     *
     * Devuelve null si el estudiante aun no tiene notas en ese periodo.
     */
    public function notaPeriodo(int $asignacionId, int $periodoId): ?float
    {
        $notas = $this->calificaciones()
            ->whereHas('evaluacion', fn ($q) => $q
                ->where('asignacion_id', $asignacionId)
                ->where('periodo_id', $periodoId))
            ->with('evaluacion')
            ->get();

        return self::ponderar(
            $notas->map(fn ($c) => [(float) $c->nota, (float) $c->evaluacion->porcentaje])
        );
    }

    /**
     * Nota definitiva de una asignatura: promedio ponderado de las notas de
     * periodo por el porcentaje de cada periodo.
     *
     * Se pondera solo sobre los periodos que YA tienen nota, no sobre los 4.
     * Si no fuera asi, en el periodo 1 todos los estudiantes tendrian una
     * definitiva cercana a 1.0 y la clasificacion de riesgo (RF-29) reportaria
     * a medio colegio como caso critico.
     */
    public function definitiva(int $asignacionId): ?float
    {
        $periodos = Periodo::where('anio_lectivo_id', $this->anio_lectivo_id)
            ->orderBy('numero')
            ->get();

        $componentes = collect();

        foreach ($periodos as $periodo) {
            $nota = $this->notaPeriodo($asignacionId, $periodo->id);

            if ($nota !== null) {
                $componentes->push([$nota, (float) $periodo->porcentaje]);
            }
        }

        return self::ponderar($componentes);
    }

    /**
     * Promedio ponderado de pares [valor, peso]. Null si no hay componentes o
     * si los pesos suman cero (evita una division por cero silenciosa).
     */
    private static function ponderar($componentes): ?float
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
