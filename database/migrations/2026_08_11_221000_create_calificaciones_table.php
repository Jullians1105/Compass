<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Calificaciones. El corazon del modulo de gestion academica (RF-13, RF-14).
 *
 * Una fila = la nota de un estudiante en una evaluacion concreta.
 *
 * Se apunta a matricula_id y no a estudiante_id a proposito: la matricula ya
 * lleva dentro el curso y el anio, asi que es imposible registrarle una nota a
 * un estudiante que no esta matriculado o guardarla en el anio equivocado.
 *
 * La evaluacion aporta el resto del contexto (asignatura, periodo, tipo, peso),
 * asi que aqui no se repiten esas columnas.
 *
 * Ni la nota del periodo ni la definitiva se guardan: se calculan ponderando.
 * Guardarlas se desincroniza en cuanto alguien corrige una nota, y en un
 * boletin eso significa reclamos de acudientes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete();

            // decimal(3,2) cubre 0.00 a 5.00 exacto. No usar float: los
            // promedios con float arrastran errores de redondeo.
            // El rango 0-5 se valida ademas en el modelo y en el FormRequest;
            // aqui solo se garantiza la precision (RNF-16, RNF-17).
            $table->decimal('nota', 3, 2);

            $table->text('observacion')->nullable();

            // Trazabilidad de quien registro y quien modifico. La auditoria
            // completa con historial de cambios es RNF-11 y queda pendiente.
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Un estudiante tiene una sola nota por evaluacion. Es lo que evita
            // notas duplicadas por doble clic en el formulario.
            $table->unique(['matricula_id', 'evaluacion_id'], 'calificaciones_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
