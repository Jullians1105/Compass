<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'anio_lectivo_id',
        'grado_id',
        'seccion',
        'nombre',
        'director_id',
        'cupo',
    ];

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'anio_lectivo_id');
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    public function director()
    {
        return $this->belongsTo(Docente::class, 'director_id');
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }

    /**
     * Estudiantes actualmente matriculados (excluye retirados y trasladados).
     */
    public function estudiantes()
    {
        return $this->hasManyThrough(
            Estudiante::class,
            Matricula::class,
            'curso_id',
            'id',
            'id',
            'estudiante_id'
        )->where('matriculas.estado', 'activa');
    }
}
