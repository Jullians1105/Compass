<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cursos: la instancia de un grado en un anio concreto (10°A de 2026).
 *
 * El prototipo muestra el selector con "10°A", "10°B", "11°A", que es
 * exactamente grado + seccion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anio_lectivo_id')->constrained('anios_lectivos')->cascadeOnDelete();
            $table->foreignId('grado_id')->constrained('grados')->restrictOnDelete();

            $table->string('seccion', 5);

            // Nombre denormalizado ("10°A") para no armarlo en cada consulta.
            // Los listados y filtros lo usan muchisimo.
            $table->string('nombre', 30);

            // Director de grupo. Nullable porque al crear el anio todavia no
            // se ha hecho la asignacion de directores.
            $table->foreignId('director_id')->nullable()->constrained('docentes')->nullOnDelete();

            $table->unsignedSmallInteger('cupo')->default(40);
            $table->timestamps();

            // No puede existir dos veces el 10°A del mismo anio.
            $table->unique(['anio_lectivo_id', 'grado_id', 'seccion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
