<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Roles para control de acceso (RF-05). Cadena libre y no un enum de BD: la
 * lista de roles todavia puede cambiar (RF-34 a RF-46 va a necesitar un rol
 * "administrativo" para el modulo financiero, que no existe todavia).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('docente')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
