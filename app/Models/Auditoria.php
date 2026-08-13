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
