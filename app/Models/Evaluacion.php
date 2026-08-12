<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    use HasFactory;

    // "Evaluacion" pluralizaria a "evaluacions". Se fija a mano.
    protected $table = 'evaluaciones';

    /** Tipos admitidos por RF-13. */
    public const TIPOS = [
        'parcial' => 'Parcial',
        'final' => 'Final',
        'quiz' => 'Quiz',
        'taller' => 'Taller',
        'proyecto' => 'Proyecto',
        'exposicion' => 'Exposicion',
        'otro' => 'Otro',
    ];

    protected $fillable = [
        'asignacion_id',
        'periodo_id',
        'nombre',
        'tipo',
        'porcentaje',
        'fecha',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'fecha' => 'date',
        ];
    }

    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class);
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function getTipoLegibleAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
