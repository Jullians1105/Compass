<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asignaturas (Matematicas, Lengua Castellana, Ciencias Sociales...).
 *
 * Son el catalogo institucional, independiente del curso y del docente. Quien
 * dicta que a quien se resuelve en la tabla "asignaciones".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaturas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);

            // Area obligatoria segun el articulo 23 de la Ley 115 de 1994.
            // Los boletines se agrupan por area, no por asignatura suelta.
            $table->string('area', 100);

            $table->unsignedTinyInteger('intensidad_horaria')->default(1);
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index('area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaturas');
    }
};
