<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Docentes.
 *
 * Se separa de "users" a proposito. Un docente puede existir en el sistema
 * antes de tener credenciales (lo carga el coordinador desde un CSV, RF-41),
 * y un usuario puede ser coordinador o acudiente sin ser docente. Por eso
 * user_id es nullable: la ficha del docente y su cuenta son cosas distintas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tipo_documento', 5)->default('CC');
            $table->string('documento', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 150)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['apellidos', 'nombres']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
