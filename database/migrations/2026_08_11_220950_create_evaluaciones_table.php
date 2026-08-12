<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Evaluaciones: cada actividad calificable dentro de un periodo.
 *
 * RF-13 exige registrar el "tipo de evaluacion (parcial, final, etc.)", lo que
 * implica que un estudiante tiene VARIAS notas por periodo en una misma
 * asignatura, no una sola.
 *
 * Se modela como entidad propia en vez de agregarle una columna
 * "tipo_evaluacion" a calificaciones por dos razones:
 *
 * 1. Con una columna suelta, dos parciales del mismo periodo serian
 *    indistinguibles entre si y no habria como referirse a "el primer parcial".
 * 2. El nombre, el peso y la fecha de la evaluacion son propiedades de la
 *    ACTIVIDAD, no de la nota de cada estudiante. Repetirlos en las 28 filas
 *    de un curso obliga a actualizar 28 registros para corregir un titulo.
 *
 * La evaluacion pertenece a una asignacion (curso + asignatura + docente) y a
 * un periodo. El docente la crea una vez y luego califica a sus estudiantes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->constrained('asignaciones')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();

            $table->string('nombre', 100);

            // parcial | final | quiz | taller | proyecto | exposicion | otro
            $table->string('tipo', 30)->default('parcial');

            // Peso de esta evaluacion DENTRO del periodo. La nota del periodo es
            // el promedio ponderado de sus evaluaciones; la definitiva del anio
            // pondera despues esas notas de periodo por el peso del periodo.
            $table->decimal('porcentaje', 5, 2)->default(100.00);

            $table->date('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();

            // Dos evaluaciones no pueden llamarse igual en el mismo periodo y
            // asignatura; es la forma de evitar duplicados por doble registro.
            $table->unique(['asignacion_id', 'periodo_id', 'nombre'], 'evaluaciones_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
