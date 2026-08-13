<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RF-06 a RF-08: gestion de usuarios del sistema. La tesis pide nombre y
 * apellido separados, documento de identidad e informacion de contacto —
 * mismo patron que ya usan Docente y Estudiante (tipo_documento/documento/
 * nombres/apellidos), asi que se replica aqui en vez de inventar uno nuevo.
 *
 * Se reemplaza 'name' (un solo campo) en vez de renombrarlo: la unica fila
 * que lo usaba es de seeders de desarrollo, no hay dato real que preservar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres', 100)->after('id');
            $table->string('apellidos', 100)->after('nombres');
            $table->string('tipo_documento', 5)->default('CC')->after('role_id');
            $table->string('documento', 20)->nullable()->unique()->after('tipo_documento');
            $table->string('telefono', 30)->nullable()->after('documento');
            $table->boolean('activo')->default(true)->after('telefono');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn(['nombres', 'apellidos', 'tipo_documento', 'documento', 'telefono', 'activo']);
        });
    }
};
