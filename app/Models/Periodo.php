<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $fillable = [
        'anio_lectivo_id',
        'numero',
        'nombre',
        'porcentaje',
        'fecha_inicio',
        'fecha_fin',
        'cerrado',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'cerrado' => 'boolean',
        ];
    }

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'anio_lectivo_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }

    /**
     * Las notas del periodo llegan a traves de sus evaluaciones: una
     * calificacion no apunta al periodo directamente, sino a la evaluacion.
     */
    public function calificaciones()
    {
        return $this->hasManyThrough(Calificacion::class, Evaluacion::class);
    }

    /**
     * Un periodo cerrado no admite registrar ni editar notas (RF-03).
     * Tambien se cierra si se cerro el anio completo.
     */
    public function admiteRegistro(): bool
    {
        return ! $this->cerrado && ! $this->anioLectivo->cerrado;
    }
}
