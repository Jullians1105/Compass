<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asignacion academica: que docente dicta que asignatura en que curso.
 *
 * Es el cruce de tres entidades y resuelve la pregunta "quien puede registrar
 * notas de Matematicas en 10°A". El control de permisos del modulo 1 se apoya
 * en esta tabla: un docente solo deberia poder calificar sus asignaciones.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos')->cascadeOnDelete();
            $table->foreignId('curso_id')->constrained('cursos')->cascadeOnDelete();
            $table->foreignId('asignatura_id')->constrained('asignaturas')->restrictOnDelete();

            // Nullable: puede quedar una asignatura sin docente asignado al
            // inicio del anio o cuando un profesor renuncia a mitad de periodo.
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();

            $table->timestamps();

            // Una asignatura se dicta una sola vez por curso y anio.
            $table->unique(['anio_lectivo_id', 'curso_id', 'asignatura_id'], 'asignaciones_unica');
            $table->index('docente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
