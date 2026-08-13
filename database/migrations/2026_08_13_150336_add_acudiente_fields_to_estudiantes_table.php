<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RF-09: el registro de un estudiante pide "contacto de acudiente" como
 * entrada, y RF-11 lo pide de vuelta como salida del perfil. Nullable porque
 * los estudiantes ya sembrados (AcademicoSeeder) no lo tienen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->string('acudiente_nombre', 150)->nullable()->after('direccion');
            $table->string('acudiente_telefono', 30)->nullable()->after('acudiente_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn(['acudiente_nombre', 'acudiente_telefono']);
        });
    }
};
