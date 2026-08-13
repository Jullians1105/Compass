<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RNF-09: el sistema debe restringir o permitir el acceso a funcionalidades
 * segun el rol del usuario. Cubre el middleware CheckPermission (RF-05)
 * sobre las pantallas de administracion (RF-04/RF-06 a RF-08), que no
 * dependen de datos academicos — a diferencia de /calificaciones, que
 * necesita cursos sembrados y por eso no se prueba aqui.
 */
class RoleAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function usuarioConRol(string $slug): User
    {
        return User::factory()->create([
            'role_id' => Role::where('slug', $slug)->value('id'),
        ]);
    }

    public function test_admin_accede_a_roles_usuarios_y_estudiantes(): void
    {
        $admin = $this->usuarioConRol('admin');

        $this->actingAs($admin)->get('/roles')->assertOk();
        $this->actingAs($admin)->get('/usuarios')->assertOk();
        $this->actingAs($admin)->get('/estudiantes')->assertOk();
        $this->actingAs($admin)->get('/estudiantes/crear')->assertOk();
    }

    public function test_coordinador_no_accede_a_roles_ni_usuarios(): void
    {
        $coordinador = $this->usuarioConRol('coordinador');

        $this->actingAs($coordinador)->get('/roles')->assertForbidden();
        $this->actingAs($coordinador)->get('/usuarios')->assertForbidden();
    }

    public function test_coordinador_si_accede_a_estudiantes(): void
    {
        $coordinador = $this->usuarioConRol('coordinador');

        $this->actingAs($coordinador)->get('/estudiantes')->assertOk();
        $this->actingAs($coordinador)->get('/estudiantes/crear')->assertOk();
    }

    public function test_docente_solo_consulta_estudiantes_no_los_gestiona(): void
    {
        $docente = $this->usuarioConRol('docente');

        $this->actingAs($docente)->get('/estudiantes')->assertOk();
        $this->actingAs($docente)->get('/estudiantes/crear')->assertForbidden();
    }

    public function test_docente_no_accede_a_roles_ni_usuarios(): void
    {
        $docente = $this->usuarioConRol('docente');

        $this->actingAs($docente)->get('/roles')->assertForbidden();
        $this->actingAs($docente)->get('/usuarios')->assertForbidden();
    }

    public function test_cuenta_inactiva_no_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->create([
            'role_id' => Role::where('slug', 'docente')->value('id'),
            'activo' => false,
        ]);

        $respuesta = $this->post('/login', [
            'email' => $usuario->email,
            'password' => 'password',
        ]);

        $respuesta->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
