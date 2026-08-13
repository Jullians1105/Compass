<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DATOS SENSIBLES DE MENORES (Ley 1581 de 2012 / Ley 1098 de 2006).
 * No exponer este modelo completo en respuestas publicas ni en logs.
 */
class Estudiante extends Model
{
    use HasFactory, Auditable;

    /**
     * RNF-10/RNF-11: la auditoria de este modelo guarda solo los NOMBRES de
     * los campos que cambiaron, nunca sus valores (ver Auditable).
     */
    protected bool $auditableSoloClaves = true;

    protected $fillable = [
        'tipo_documento',
        'documento',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion',
        'acudiente_nombre',
        'acudiente_telefono',
        'consentimiento_datos',
        'fecha_consentimiento',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_consentimiento' => 'date',
            'consentimiento_datos' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    /**
     * La matricula del anio activo. Es la que usan casi todas las pantallas.
     */
    public function matriculaVigente()
    {
        return $this->hasOne(Matricula::class)
            ->whereHas('anioLectivo', fn ($q) => $q->where('activo', true));
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    /**
     * Sin consentimiento del acudiente no deberian generarse ni mostrarse
     * alertas del EWS sobre este estudiante (README, consideraciones eticas).
     */
    public function puedeSerAnalizado(): bool
    {
        return $this->consentimiento_datos;
    }
}
