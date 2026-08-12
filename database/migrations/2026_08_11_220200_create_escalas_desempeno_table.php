<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Escala de desempeno (Decreto 1290 de 2009, articulo 5).
 *
 * El decreto define cuatro niveles nacionales -Superior, Alto, Basico y Bajo-
 * pero deja que cada institucion fije sus propios cortes numericos. Por eso
 * los rangos viven en tabla y no en constantes: si el colegio San Jose de
 * Calasanz decide que "Alto" empieza en 4.0 y otro colegio en 4.2, ambos
 * funcionan sin tocar codigo.
 *
 * Es la tabla que sostiene la columna ESTADO del prototipo de calificaciones
 * y alimenta despues las reglas del EWS (RF-46).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalas_desempeno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos')->cascadeOnDelete();

            $table->string('nombre', 50);
            $table->decimal('nota_minima', 3, 2);
            $table->decimal('nota_maxima', 3, 2);

            // Si este nivel cuenta como aprobado. Segun el decreto, "Basico" ya
            // aprueba: significa que el estudiante alcanzo los aprendizajes
            // minimos, no que este en riesgo.
            $table->boolean('aprueba')->default(true);

            // Color para badges y graficos. Se guarda aqui para que el criterio
            // visual sea el mismo en toda la app y en los dashboards de BI.
            $table->string('color', 20)->default('secondary');

            $table->unsignedTinyInteger('orden');
            $table->timestamps();

            $table->unique(['anio_lectivo_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalas_desempeno');
    }
};
