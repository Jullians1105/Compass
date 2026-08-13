<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * RNF-08: las contraseñas deben almacenarse con un algoritmo de cifrado
 * seguro. `User::casts()` marca 'password' => 'hashed', que Laravel resuelve
 * con Bcrypt por defecto (config/hashing.php).
 */
class PasswordHashingTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_never_stored_in_plain_text(): void
    {
        $usuario = User::factory()->create(['password' => 'clave-super-secreta']);

        $this->assertNotEquals('clave-super-secreta', $usuario->getRawOriginal('password'));
    }

    public function test_password_is_hashed_with_bcrypt(): void
    {
        $usuario = User::factory()->create(['password' => 'clave-super-secreta']);

        $info = password_get_info($usuario->getRawOriginal('password'));

        $this->assertSame('bcrypt', $info['algoName']);
    }

    public function test_stored_hash_verifies_against_the_original_password(): void
    {
        $usuario = User::factory()->create(['password' => 'clave-super-secreta']);

        $this->assertTrue(Hash::check('clave-super-secreta', $usuario->getRawOriginal('password')));
    }
}
