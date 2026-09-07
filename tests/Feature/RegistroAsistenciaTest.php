<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Matricula;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RF-15: registro de asistencia diaria.
 *
 * Necesita el seeder academico completo porque la pantalla trabaja sobre
 * cursos y matriculas reales.
 */
class RegistroAsistenciaTest extends TestCase
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

    private function guardar(User $como, array $datos)
    {
        return $this->actingAs($como)->put('/asistencia', $datos);
    }

    public function test_coordinador_ve_la_planilla_de_asistencia(): void
    {
        $curso = Curso::orderBy('id')->firstOrFail();

        $this->actingAs($this->usuario('coordinador@sjc.edu.co'))
            ->get("/asistencia?curso={$curso->id}")
            ->assertOk()
            ->assertSee('Planilla de asistencia', false)
            ->assertSee('Tardanza', false);
    }

    public function test_guardar_registra_el_estado_y_deja_rastro(): void
    {
        $curso = Curso::orderBy('id')->firstOrFail();
        $matricula = Matricula::where('curso_id', $curso->id)->activas()->firstOrFail();
        $coordinador = $this->usuario('coordinador@sjc.edu.co');

        $this->guardar($coordinador, [
            'curso' => $curso->id,
            'fecha' => now()->toDateString(),
            'estados' => [$matricula->id => 'tardanza'],
            'justificaciones' => [$matricula->id => 'Ingreso 15 min tarde'],
        ])->assertRedirect();

        $registro = Asistencia::where('matricula_id', $matricula->id)->firstOrFail();

        $this->assertSame('tardanza', $registro->estado);
        $this->assertSame('Ingreso 15 min tarde', $registro->justificacion);
        $this->assertSame($coordinador->id, $registro->registrado_por);
    }

    public function test_la_justificacion_se_descarta_si_el_estudiante_estuvo_presente(): void
    {
        $curso = Curso::orderBy('id')->firstOrFail();
        $matricula = Matricula::where('curso_id', $curso->id)->activas()->firstOrFail();

        $this->guardar($this->usuario('coordinador@sjc.edu.co'), [
            'curso' => $curso->id,
            'fecha' => now()->toDateString(),
            'estados' => [$matricula->id => 'presente'],
            // Texto sobrante de haber marcado ausente antes de corregir.
            'justificaciones' => [$matricula->id => 'Cita medica'],
        ])->assertRedirect();

        $registro = Asistencia::where('matricula_id', $matricula->id)->firstOrFail();

        $this->assertSame('presente', $registro->estado);
        $this->assertNull($registro->justificacion);
    }

    public function test_no_se_puede_registrar_asistencia_de_una_fecha_futura(): void
    {
        $curso = Curso::orderBy('id')->firstOrFail();
        $matricula = Matricula::where('curso_id', $curso->id)->activas()->firstOrFail();

        $this->guardar($this->usuario('coordinador@sjc.edu.co'), [
            'curso' => $curso->id,
            'fecha' => now()->addDay()->toDateString(),
            'estados' => [$matricula->id => 'presente'],
        ])->assertSessionHasErrors('fecha');

        $this->assertDatabaseCount('asistencias', 0);
    }

    public function test_un_estado_invalido_se_rechaza(): void
    {
        $curso = Curso::orderBy('id')->firstOrFail();
        $matricula = Matricula::where('curso_id', $curso->id)->activas()->firstOrFail();

        $this->guardar($this->usuario('coordinador@sjc.edu.co'), [
            'curso' => $curso->id,
            'fecha' => now()->toDateString(),
            'estados' => [$matricula->id => 'enfermo'],
        ])->assertSessionHasErrors("estados.{$matricula->id}");

        $this->assertDatabaseCount('asistencias', 0);
    }

    public function test_docente_solo_ve_los_cursos_que_dirige(): void
    {
        $docenteUser = $this->usuario('docente@sjc.edu.co');
        $ficha = $docenteUser->docente;

        $this->assertNotNull($ficha, 'El seeder debe enlazar la cuenta docente a una ficha.');

        $propio = Curso::where('director_id', $ficha->id)->firstOrFail();
        $ajeno = Curso::where('director_id', '!=', $ficha->id)->first();

        $respuesta = $this->actingAs($docenteUser)->get('/asistencia')->assertOk();
        $respuesta->assertSee($propio->nombre, false);

        if ($ajeno) {
            // El curso ajeno no debe aparecer ni siquiera como opcion del filtro.
            $this->assertStringNotContainsString(
                'value="' . $ajeno->id . '"',
                $respuesta->getContent()
            );
        }
    }

    public function test_docente_no_puede_registrar_asistencia_de_un_curso_ajeno(): void
    {
        $docenteUser = $this->usuario('docente@sjc.edu.co');
        $ficha = $docenteUser->docente;

        $ajeno = Curso::where('director_id', '!=', $ficha->id)->firstOrFail();
        $matricula = Matricula::where('curso_id', $ajeno->id)->activas()->firstOrFail();

        // El rol docente tiene el permiso 'asistencia-y-puntualidad', asi que
        // el middleware lo deja pasar: quien corta aqui es el filtro por
        // director del controller.
        $this->guardar($docenteUser, [
            'curso' => $ajeno->id,
            'fecha' => now()->toDateString(),
            'estados' => [$matricula->id => 'ausente'],
        ])->assertForbidden();

        $this->assertDatabaseCount('asistencias', 0);
    }
}
