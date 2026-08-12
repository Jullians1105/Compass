<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Calificacion extends Model
{
    use HasFactory;

    // "Calificacion" pluralizaria a "calificacions". Se fija a mano.
    protected $table = 'calificaciones';

    /** Rango institucional de notas. */
    public const NOTA_MINIMA = 0.0;

    public const NOTA_MAXIMA = 5.0;

    protected $fillable = [
        'matricula_id',
        'evaluacion_id',
        'nota',
        'observacion',
        'registrado_por',
        'actualizado_por',
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
        ];
    }

    /**
     * Guarda de ultimo recurso para la validacion de rango (RNF-17).
     *
     * La validacion "de verdad" va en el FormRequest, que es quien devuelve
     * mensajes legibles al usuario. Pero tambien tiene que cubrirse aqui porque
     * hay caminos que no pasan por un formulario: los seeders, la importacion
     * masiva desde archivos (RF-21) y cualquier script de migracion. Una nota
     * de 50 colandose por ahi corrompe todos los promedios y la clasificacion
     * de riesgo (RF-29) sin que nadie se de cuenta.
     */
    protected static function booted(): void
    {
        static::saving(function (self $calificacion) {
            $nota = (float) $calificacion->nota;

            if ($nota < self::NOTA_MINIMA || $nota > self::NOTA_MAXIMA) {
                throw new InvalidArgumentException(
                    sprintf(
                        'La nota %s esta fuera del rango permitido (%s a %s).',
                        $nota,
                        self::NOTA_MINIMA,
                        self::NOTA_MAXIMA
                    )
                );
            }
        });
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /**
     * Nivel de desempeno de esta nota segun la escala del anio (Decreto 1290).
     *
     * Ojo: esto NO es el nivel de riesgo del EWS. RF-29 define una escala
     * distinta (bajo/medio/alto con puntuacion 0-100) que combina notas,
     * asistencia y observaciones. Son dos cosas separadas.
     */
    public function desempeno(): ?EscalaDesempeno
    {
        return EscalaDesempeno::paraNota(
            (float) $this->nota,
            $this->evaluacion->periodo->anio_lectivo_id
        );
    }
}
