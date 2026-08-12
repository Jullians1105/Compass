<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Anio lectivo: el contenedor de todo lo demas.
 *
 * Casi todas las tablas academicas cuelgan de aqui porque en un colegio los
 * datos se reinician cada anio: los cursos se renumeran, los estudiantes
 * cambian de grado y los docentes cambian de asignacion. Sin esta tabla no
 * se podria consultar el historico de un estudiante sin mezclar anios.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anios_lectivos', function (Blueprint $table) {
            $table->id();
            $table->year('anio')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // Solo un anio deberia estar activo a la vez. La app lo usa para
            // saber en que anio registrar cuando el usuario no lo especifica.
            $table->boolean('activo')->default(false);

            // Una vez cerrado no se aceptan mas calificaciones (RF-03).
            $table->boolean('cerrado')->default(false);

            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anios_lectivos');
    }
};
