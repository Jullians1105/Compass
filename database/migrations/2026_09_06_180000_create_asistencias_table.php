<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Asistencia diaria (RF-15, RF-16).
 *
 * Una fila = el estado de un estudiante en una fecha. Se registra UNA vez al
 * dia y no por asignatura: RF-15 define la entrada como "ID del estudiante,
 * fecha, estado y justificacion", sin materia. El prototipo de Figma filtra
 * por "Materia", pero eso multiplicaria las filas por cada clase del dia y se
 * aparta del requisito aprobado. Si el equipo cambia de opinion habra que
 * agregar asignacion_id y rehacer el indice unico.
 *
 * Se apunta a matricula_id, igual que calificaciones: la matricula ya trae el
 * curso y el anio, asi que no se puede registrar asistencia de un estudiante
 * que no esta matriculado ni guardarla en el anio equivocado.
 *
 * El porcentaje de asistencia no se guarda: se calcula contando filas. Igual
 * que con las notas definitivas, un acumulado almacenado se desincroniza en
 * cuanto alguien corrige un registro.
 */
return new class extends Migration
{
    /** Estados posibles de RF-15. */
    private const ESTADOS = ['presente', 'ausente', 'tardanza'];

    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->cascadeOnDelete();
            $table->date('fecha');

            // enum y no string: los tres estados salen de RF-15 y no se esperan
            // mas. Con string, un typo ("presnte") entraria sin quejarse y
            // romperia el conteo de ausencias en silencio.
            $table->enum('estado', self::ESTADOS);

            // RF-15: "justificacion (si aplica)". Solo tiene sentido en ausente
            // o tardanza; se valida en el controller, no aqui, porque una
            // restriccion de BD no puede dar un mensaje legible al docente.
            $table->text('justificacion')->nullable();

            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actualizado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Un estudiante tiene un solo estado por dia. Evita duplicados por
            // doble envio del formulario.
            $table->unique(['matricula_id', 'fecha'], 'asistencias_unica');

            // La consulta de RF-16 ("historial de un estudiante entre fechas")
            // y la pantalla diaria filtran por fecha; sin este indice cada
            // carga recorre la tabla entera.
            $table->index('fecha', 'asistencias_fecha');
        });

        // Respaldo a nivel de BD del catalogo de estados, por los mismos
        // caminos que se saltan el modelo: seeders, inserciones masivas y la
        // futura importacion de RF-21. MySQL ya lo garantiza con el enum; este
        // CHECK deja la regla explicita y cubre motores donde enum es un texto.
        $lista = "'" . implode("','", self::ESTADOS) . "'";

        DB::statement(
            'ALTER TABLE asistencias ADD CONSTRAINT chk_asistencias_estado '
            . "CHECK (estado IN ({$lista}))"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
