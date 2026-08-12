<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Periodos academicos.
 *
 * El prototipo de Stitch (docs/prototipos/pantallas/02-registro-de-calificaciones.html)
 * muestra 4 periodos con 25% cada uno. No se quema ese 25% en el codigo: el
 * porcentaje vive aqui porque el Decreto 1290 deja que cada institucion defina
 * su propia distribucion, y un colegio puede usar 3 periodos o pesos desiguales.
 *
 * La suma de porcentajes de un mismo anio deberia dar 100. Eso se valida en la
 * capa de aplicacion, no en la BD, para poder guardar estados intermedios
 * mientras el coordinador configura el anio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos')->cascadeOnDelete();

            $table->unsignedTinyInteger('numero');
            $table->string('nombre', 50);
            $table->decimal('porcentaje', 5, 2);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            // Cerrado = ya no se pueden registrar ni editar notas de ese periodo.
            $table->boolean('cerrado')->default(false);

            $table->timestamps();

            // No puede haber dos "periodo 1" en el mismo anio.
            $table->unique(['anio_lectivo_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos');
    }
};
