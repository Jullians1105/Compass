<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalaDesempeno extends Model
{
    use HasFactory;

    protected $table = 'escalas_desempeno';

    protected $fillable = [
        'anio_lectivo_id',
        'nombre',
        'nota_minima',
        'nota_maxima',
        'aprueba',
        'color',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'nota_minima' => 'decimal:2',
            'nota_maxima' => 'decimal:2',
            'aprueba' => 'boolean',
        ];
    }

    public function anioLectivo()
    {
        return $this->belongsTo(AnioLectivo::class, 'anio_lectivo_id');
    }

    /**
     * Traduce una nota numerica al nivel de desempeno del Decreto 1290.
     *
     * Devuelve null si la nota cae fuera de todos los rangos configurados,
     * lo cual indica que la escala del anio esta mal armada. Se prefiere null
     * antes que adivinar un nivel: en un boletin, inventarse el desempeno es
     * peor que mostrar que falta configuracion.
     */
    public static function paraNota(float $nota, int $anioLectivoId): ?self
    {
        return static::where('anio_lectivo_id', $anioLectivoId)
            ->where('nota_minima', '<=', $nota)
            ->where('nota_maxima', '>=', $nota)
            ->orderBy('orden')
            ->first();
    }
}
