<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email')->constrained()->nullOnDelete();
        });

        // El dato de la columna vieja es solo de los seeders de desarrollo
        // (ver database/seeders/DatabaseSeeder.php) — no hay usuarios reales
        // que migrar. RolePermissionSeeder crea los roles y reasigna
        // role_id; requiere migrate:fresh --seed despues de esta migracion.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('docente')->after('email');
            $table->dropConstrainedForeignId('role_id');
        });
    }
};
