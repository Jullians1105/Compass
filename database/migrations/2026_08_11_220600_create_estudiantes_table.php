<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Estudiantes.
 *
 * DATOS SENSIBLES DE MENORES. Aplica la Ley 1581 de 2012 y la Ley 1098 de 2006:
 * - Nunca subir datos reales al repositorio (ver README).
 * - Los seeders generan datos ficticios.
 * - Todo acceso a esta tabla deberia quedar auditado (RF-39, pendiente).
 *
 * El campo "documento" es tipicamente Tarjeta de Identidad (TI) porque son
 * menores; Registro Civil (RC) en primaria y PPT/PEP en poblacion migrante.
 * Por eso el tipo es variable y no se asume CC.
 *
 * Ojo: aqui NO va el curso. Un estudiante pertenece a un curso a traves de la
 * matricula, que es por anio lectivo. Poner curso_id aqui haria imposible
 * consultar el historico academico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();

            $table->string('tipo_documento', 5)->default('TI');
            $table->string('documento', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 20)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 200)->nullable();

            // Consentimiento informado del acudiente para el tratamiento de
            // datos (Ley 1581). Sin esto no deberian mostrarse alertas del EWS.
            $table->boolean('consentimiento_datos')->default(false);
            $table->date('fecha_consentimiento')->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['apellidos', 'nombres']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
