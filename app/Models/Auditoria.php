<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * RNF-11 / RNF-10: log de auditoria, append-only (ver migracion).
 */
class Auditoria extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'evento',
        'auditable_type',
        'auditable_id',
        'detalle',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'detalle' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Version legible de `detalle` para la pantalla de auditoria — evita
     * volcar el JSON crudo, cuyas claves varian segun quien llamo a
     * registrar() (login_fallido trae email/motivo, creado/actualizado traen
     * atributos/cambios como lista o como mapa segun `$auditableSoloClaves`,
     * permisos_actualizados trae una lista de slugs).
     */
    public function resumen(): ?string
    {
        $detalle = $this->detalle;

        if (! $detalle) {
            return null;
        }

        if (isset($detalle['atributos'])) {
            return 'Campos registrados: ' . $this->formatearValores($detalle['atributos']);
        }

        if (isset($detalle['cambios'])) {
            return 'Campos modificados: ' . $this->formatearValores($detalle['cambios']);
        }

        if (isset($detalle['permisos'])) {
            return $detalle['permisos'] === []
                ? 'Sin permisos asignados'
                : 'Permisos: ' . implode(', ', $detalle['permisos']);
        }

        if (isset($detalle['email'])) {
            $motivo = $detalle['motivo'] ?? null;

            return match ($motivo) {
                'cuenta_inactiva' => "Intento de {$detalle['email']} (cuenta inactiva)",
                default => "Intento de {$detalle['email']}",
            };
        }

        return $this->formatearValores($detalle);
    }

    /**
     * Una lista simple de nombres de campo (soloClaves) se muestra separada
     * por comas; un mapa campo => valor se muestra como "campo: valor".
     */
    private function formatearValores(array $valores): string
    {
        if (array_is_list($valores)) {
            return implode(', ', $valores);
        }

        $partes = [];

        foreach ($valores as $campo => $valor) {
            $partes[] = $campo . ': ' . (is_scalar($valor) || $valor === null
                ? (string) $valor
                : json_encode($valor, JSON_UNESCAPED_UNICODE));
        }

        return implode(', ', $partes);
    }

    /**
     * Registrar un evento. `auth()->id()` puede ser null (login fallido: el
     * usuario todavia no esta autenticado).
     */
    public static function registrar(string $evento, ?Model $auditable = null, array $detalle = []): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'evento' => $evento,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'detalle' => $detalle,
            'ip' => request()?->ip(),
        ]);
    }
}
