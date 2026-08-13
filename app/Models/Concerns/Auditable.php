<?php

namespace App\Models\Concerns;

use App\Models\Auditoria;

/**
 * RNF-11: registra en `auditorias` cada alta y modificacion de un modelo.
 * Usado por User, Role y Estudiante — los tres CRUD de administracion que se
 * construyeron en esta sesion (RF-04, RF-06/RF-07, RF-09/RF-10).
 *
 * No audita `delete()` porque ningun controller de la app expone borrar
 * estos modelos todavia (ver RoleController/UserController/
 * EstudianteController: solo index/create/store/edit/update).
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            Auditoria::registrar('creado', $model, [
                'atributos' => $model->atributosAuditables($model->getAttributes()),
            ]);
        });

        static::updated(function ($model) {
            $cambios = $model->atributosAuditables($model->getChanges());

            if ($cambios !== []) {
                Auditoria::registrar('actualizado', $model, ['cambios' => $cambios]);
            }
        });
    }

    /**
     * Nunca se audita en texto plano un password ni un remember_token, ni
     * siquiera su hash.
     *
     * Si el modelo define `$auditableSoloClaves = true` (ej. Estudiante,
     * datos sensibles de menores — Ley 1581), la auditoria solo guarda QUE
     * campos cambiaron, no sus valores: alcanza para trazabilidad ("quien
     * modifico la direccion de este estudiante y cuando") sin duplicar el
     * dato sensible en una segunda tabla.
     */
    protected function atributosAuditables(array $atributos): array
    {
        $excluidos = property_exists($this, 'auditableExcluir')
            ? $this->auditableExcluir
            : ['password', 'remember_token'];

        $filtrados = collect($atributos)->except($excluidos)->all();

        if (property_exists($this, 'auditableSoloClaves') && $this->auditableSoloClaves) {
            return array_values(array_keys($filtrados));
        }

        return $filtrados;
    }
}
