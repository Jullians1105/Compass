<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RNF-11: registro de auditoria de accesos y modificaciones.
 * RNF-10: el acceso a datos sensibles (perfil de estudiante, menor de edad)
 * tambien se audita aqui — es el mecanismo de trazabilidad que pide la
 * Ley 1581 para datos personales.
 *
 * Tabla de solo insercion (append-only): nunca se actualiza ni se borra una
 * fila, por eso no tiene 'updated_at'. Si se borrara el usuario que generó el
 * evento, la fila se queda con user_id en null en vez de desaparecer — un log
 * de auditoria que se puede borrar no sirve de mucho.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // login | login_fallido | logout | creado | actualizado | consultado
            $table->string('evento', 30);

            // Modelo afectado (User, Role, Estudiante...) cuando aplica.
            $table->string('auditable_type', 100)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            // Contexto libre: campos cambiados, email de un intento de login
            // fallido, etc. JSON para no tener una tabla con 15 columnas
            // nullable distintas segun el tipo de evento.
            $table->json('detalle')->nullable();

            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('evento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
