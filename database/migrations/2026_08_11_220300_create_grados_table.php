<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Grados (6°, 7°, ... 11°).
 *
 * Se separa de "cursos" porque el grado es un concepto estable -siempre existe
 * el grado 10- mientras que el curso es la instancia de ese grado en un anio
 * concreto (10°A de 2026). Sin esta separacion no se podria comparar el
 * rendimiento del grado 10 entre anios distintos, que es justo lo que necesita
 * el modulo de BI.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique();

            // Numero del grado (6 a 11). Sirve para ordenar y para filtros por
            // nivel sin tener que parsear el nombre.
            $table->unsignedTinyInteger('nivel');

            // Primaria / Basica secundaria / Media. Lo pide la Ley 115 de 1994
            // para los reportes agregados.
            $table->string('ciclo', 30);

            $table->timestamps();

            $table->index('nivel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
