<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnioLectivo extends Model
{
    use HasFactory;

    // Laravel pluralizaria "AnioLectivo" como "anio_lectivos". Se fija a mano.
    protected $table = 'anios_lectivos';

    protected $fillable = [
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'cerrado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activo' => 'boolean',
            'cerrado' => 'boolean',
        ];
    }

    public function periodos()
    {
        return $this->hasMany(Periodo::class);
    }

    public function escalas()
    {
        return $this->hasMany(EscalaDesempeno::class)->orderBy('orden');
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * El anio sobre el que trabaja la app cuando el usuario no elige otro.
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
