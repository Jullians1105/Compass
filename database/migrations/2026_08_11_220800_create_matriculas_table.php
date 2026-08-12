<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Matricula: vincula un estudiante con un curso en un anio lectivo.
 *
 * Es la pieza que permite el historico. Juan estuvo en 10°A en 2025 y en 11°A
 * en 2026: son dos matriculas, y sus notas cuelgan de la matricula
 * correspondiente. Asi las notas de 2025 no se mezclan con las de 2026 aunque
 * sea el mismo estudiante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete();
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos')->cascadeOnDelete();

            $table->date('fecha_matricula');

            // activa | retirada | trasladada | graduada
            $table->string('estado', 20)->default('activa');
            $table->date('fecha_retiro')->nullable();
            $table->string('motivo_retiro', 200)->nullable();

            $table->timestamps();

            // Un estudiante solo puede estar matriculado una vez por anio.
            $table->unique(['estudiante_id', 'anio_lectivo_id']);
            $table->index(['curso_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
