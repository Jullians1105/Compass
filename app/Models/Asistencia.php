<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Asistencia extends Model
{
    use HasFactory;

    // "Asistencia" pluralizaria a "asistencias" correctamente, pero se fija a
    // mano igual que en los demas modelos del proyecto para no depender del
    // pluralizador en ingles.
    protected $table = 'asistencias';

    /** Estados de RF-15, con su etiqueta y el color de Bootstrap que les toca. */
    public const ESTADOS = [
        'presente' => ['nombre' => 'Presente', 'color' => 'success', 'icono' => 'check_circle'],
        'ausente' => ['nombre' => 'Ausente', 'color' => 'danger', 'icono' => 'cancel'],
        'tardanza' => ['nombre' => 'Tardanza', 'color' => 'warning', 'icono' => 'schedule'],
    ];

    protected $fillable = [
        'matricula_id',
        'fecha',
        'estado',
        'justificacion',
        'registrado_por',
        'actualizado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Guarda de ultimo recurso para el catalogo de estados, con el mismo
     * criterio que Calificacion::booted(): la validacion legible vive en el
     * controller, pero hay caminos que no pasan por un formulario (seeders,
     * inserciones masivas, la importacion de RF-21). Un estado invalido no
     * reventaria nada visible, solo dejaria de contarse en el porcentaje de
     * asistencia — que es peor, porque nadie se entera.
     */
    protected static function booted(): void
    {
        static::saving(function (self $asistencia) {
            if (! array_key_exists($asistencia->estado, self::ESTADOS)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Estado de asistencia invalido: "%s". Permitidos: %s.',
                        $asistencia->estado,
                        implode(', ', array_keys(self::ESTADOS))
                    )
                );
            }
        });
    }

    public function matricula()
    {
        return $this->belongsTo(Matricula::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function scopeDelDia($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    /** Cuenta como asistido tanto "presente" como "tardanza" (RF-16). */
    public function scopeAsistio($query)
    {
        return $query->whereIn('estado', ['presente', 'tardanza']);
    }

    public function getEstadoLegibleAttribute(): string
    {
        return self::ESTADOS[$this->estado]['nombre'] ?? $this->estado;
    }

    public function getColorAttribute(): string
    {
        return self::ESTADOS[$this->estado]['color'] ?? 'secondary';
    }
}
