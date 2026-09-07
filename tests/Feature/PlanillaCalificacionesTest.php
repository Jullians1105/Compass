<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Evaluacion;
use App\Models\Matricula;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RF-13: registro y edicion de calificaciones desde la planilla.
 *
 * A diferencia de RoleAccessControlTest, aqui si hace falta el seeder
 * academico completo: la pantalla necesita cursos, asignaciones, periodos y
 * evaluaciones reales para tener columnas donde escribir.
 */
class PlanillaCalificacionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function usuario(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    /** Primera evaluacion existente, con su asignacion, curso y matricula. */
    private function contexto(): array
    {
        $evaluacion = Evaluacion::orderBy('id')->firstOrFail();
        $asignacion = $evaluacion->asignacion;
        $curso = $asignacion->curso;
        $matricula = Matricula::where('curso_id', $curso->id)->activas()->firstOrFail();

        return [$evaluacion, $asignacion, $curso, $matricula];
    }

    private function guardar(User $como, array $datos)
    {
        return $this->actingAs($como)->put('/calificaciones/planilla', $datos);
    }

    public function test_coordinador_ve_la_planilla_con_las_evaluaciones_del_periodo(): void
    {
        [$evaluacion, $asignacion, $curso] = $this->contexto();

        $this->actingAs($this->usuario('coordinador@sjc.edu.co'))
            ->get("/calificaciones/planilla?curso={$curso->id}&asignatura={$asignacion->id}&periodo={$evaluacion->periodo_id}")
            ->assertOk()
            ->assertSee('Planilla de calificaciones', false)
            ->assertSee($evaluacion->nombre, false);
    }

    public function test_guardar_registra_la_nota_y_deja_rastro_de_quien_la_puso(): void
    {
        [$evaluacion, $asignacion, $curso, $matricula] = $this->contexto();
        $coordinador = $this->usuario('coordinador@sjc.edu.co');

        Calificacion::where('matricula_id', $matricula->id)
            ->where('evaluacion_id', $evaluacion->id)
            ->delete();

        $this->guardar($coordinador, [
            'curso' => $curso->id,
            'asignatura' => $asignacion->id,
            'periodo' => $evaluacion->periodo_id,
            'notas' => [$matricula->id => [$evaluacion->id => '4.25']],
        ])->assertRedirect();

        $guardada = Calificacion::where('matricula_id', $matricula->id)
            ->where('evaluacion_id', $evaluacion->id)
            ->firstOrFail();

        $this->assertSame('4.25', (string) $guardada->nota);
        $this->assertSame($coordinador->id, $guardada->registrado_por);
    }

    public function test_celda_vacia_borra_la_nota_en_vez_de_guardar_cero(): void
    {
        [$evaluacion, $asignacion, $curso, $matricula] = $this->contexto();

        Calificacion::updateOrCreate(
            ['matricula_id' => $matricula->id, 'evaluacion_id' => $evaluacion->id],
            ['nota' => 3.5]
        );

        $this->guardar($this->usuario('coordinador@sjc.edu.co'), [
            'curso' => $curso->id,
            'asignatura' => $asignacion->id,
            'periodo' => $evaluacion->periodo_id,
            'notas' => [$matricula->id => [$evaluacion->id => '']],
        ])->assertRedirect();

        // Borrada, no guardada como 0: "sin calificar" y "saco cero" son
        // cosas distintas para el promedio y para el riesgo (RF-29).
        $this->assertDatabaseMissing('calificaciones', [
            'matricula_id' => $matricula->id,
            'evaluacion_id' => $evaluacion->id,
        ]);
    }

    public function test_nota_fuera_de_rango_se_rechaza_sin_guardar_nada(): void
    {
        [$evaluacion, $asignacion, $curso, $matricula] = $this->contexto();

        Calificacion::where('matricula_id', $matricula->id)
            ->where('evaluacion_id', $evaluacion->id)
            ->delete();

        $this->guardar($this->usuario('coordinador@sjc.edu.co'), [
            'curso' => $curso->id,
            'asignatura' => $asignacion->id,
            'periodo' => $evaluacion->periodo_id,
            'notas' => [$matricula->id => [$evaluacion->id => '7']],
        ])->assertSessionHasErrors("notas.{$matricula->id}.{$evaluacion->id}");

        $this->assertDatabaseMissing('calificaciones', [
            'matricula_id' => $matricula->id,
            'evaluacion_id' => $evaluacion->id,
        ]);
    }

    public function test_docente_solo_ve_sus_propias_asignaciones(): void
    {
        $docenteUser = $this->usuario('docente@sjc.edu.co');
        $ficha = $docenteUser->docente;

        $this->assertNotNull($ficha, 'El seeder debe enlazar la cuenta docente a una ficha.');

        $propia = Asignacion::where('docente_id', $ficha->id)->with('asignatura')->firstOrFail();
        $ajena = Asignacion::where('docente_id', '!=', $ficha->id)
            ->where('curso_id', $propia->curso_id)
            ->with('asignatura')
            ->first();

        $respuesta = $this->actingAs($docenteUser)
            ->get("/calificaciones/planilla?curso={$propia->curso_id}")
            ->assertOk();

        $respuesta->assertSee($propia->asignatura->nombre, false);

        if ($ajena) {
            $respuesta->assertDontSee($ajena->asignatura->nombre, false);
        }
    }

    public function test_docente_no_puede_guardar_notas_de_una_asignatura_ajena(): void
    {
        $docenteUser = $this->usuario('docente@sjc.edu.co');
        $ficha = $docenteUser->docente;

        $ajena = Asignacion::where('docente_id', '!=', $ficha->id)->firstOrFail();
        $evaluacion = Evaluacion::where('asignacion_id', $ajena->id)->firstOrFail();
        $matricula = Matricula::where('curso_id', $ajena->curso_id)->activas()->firstOrFail();

        Calificacion::where('matricula_id', $matricula->id)
            ->where('evaluacion_id', $evaluacion->id)
            ->delete();

        // El permiso 'gestion-de-calificaciones' lo tiene el rol docente, asi
        // que el middleware lo deja pasar: quien corta aqui es el filtro por
        // docente del controller. Sin el, un POST armado a mano bastaria para
        // calificar el curso de un colega.
        $this->guardar($docenteUser, [
            'curso' => $ajena->curso_id,
            'asignatura' => $ajena->id,
            'periodo' => $evaluacion->periodo_id,
            'notas' => [$matricula->id => [$evaluacion->id => '5.0']],
        ])->assertForbidden();

        $this->assertDatabaseMissing('calificaciones', [
            'matricula_id' => $matricula->id,
            'evaluacion_id' => $evaluacion->id,
        ]);
    }
}
