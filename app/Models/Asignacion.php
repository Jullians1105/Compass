<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    use HasFactory;

    // "Asignacion" pluralizaria a "asignacions". Se fija a mano.
    protected $table = 'asignaciones';

    protected $fillable = [
        'anio_lectivo_id',
        'curso_id',
        'asignatura_id',
        'docente_id',
    ];

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'anio_lectivo_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function calificaciones()
    {
        return $this->hasManyThrough(Calificacion::class, Evaluacion::class);
    }
}
