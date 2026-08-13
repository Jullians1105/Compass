<?php

namespace Tests\Feature;

use App\Models\Auditoria;
use App\Models\Estudiante;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RNF-11: registro de auditoria de accesos y modificaciones.
 * RNF-10: el acceso a un perfil de estudiante (dato sensible de menor)
 * tambien queda auditado.
 */
class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_login_exitoso_queda_auditado(): void
    {
        $usuario = User::factory()->create([
            'role_id' => Role::where('slug', 'admin')->value('id'),
        ]);

        $this->post('/login', ['email' => $usuario->email, 'password' => 'password']);

        $this->assertDatabaseHas('auditorias', [
            'user_id' => $usuario->id,
            'evento' => 'login',
        ]);
    }

    public function test_login_fallido_queda_auditado_con_el_email_intentado(): void
    {
        $this->post('/login', ['email' => 'nadie@sjc.edu.co', 'password' => 'lo-que-sea']);

        $this->assertDatabaseHas('auditorias', [
            'evento' => 'login_fallido',
        ]);

        $evento = Auditoria::where('evento', 'login_fallido')->first();
        $this->assertSame('nadie@sjc.edu.co', $evento->detalle['email']);
        $this->assertNull($evento->user_id);
    }

    public function test_crear_estudiante_queda_auditado_sin_exponer_datos_sensibles(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('slug', 'admin')->value('id')]);

        $estudiante = Estudiante::create([
            'tipo_documento' => 'TI',
            'documento' => '1234567890',
            'nombres' => 'Juan',
            'apellidos' => 'Perez',
            'direccion' => 'Calle falsa 123',
            'acudiente_nombre' => 'Maria Perez',
            'acudiente_telefono' => '3000000000',
        ]);

        $evento = Auditoria::where('evento', 'creado')
            ->where('auditable_type', $estudiante->getMorphClass())
            ->where('auditable_id', $estudiante->id)
            ->first();

        $this->assertNotNull($evento);
        $this->assertContains('direccion', $evento->detalle['atributos']);
        // RNF-10: solo el nombre del campo, nunca el valor real.
        $this->assertNotContains('Calle falsa 123', $evento->detalle['atributos']);
    }

    public function test_consultar_perfil_de_estudiante_queda_auditado(): void
    {
        $docente = User::factory()->create(['role_id' => Role::where('slug', 'docente')->value('id')]);
        $estudiante = Estudiante::factory()->create();

        $this->actingAs($docente)->get("/estudiantes/{$estudiante->id}")->assertOk();

        $this->assertDatabaseHas('auditorias', [
            'user_id' => $docente->id,
            'evento' => 'consultado',
            'auditable_type' => $estudiante->getMorphClass(),
            'auditable_id' => $estudiante->id,
        ]);
    }
}
