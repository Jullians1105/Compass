<?php

use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Support\Modulos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// RF-01: autenticacion. 'login' es el nombre que el middleware 'auth' de
// Laravel usa por defecto para redirigir a un usuario no autenticado.
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);

    // RF-03: recuperacion de contrasena. Nombres de ruta (password.email,
    // password.reset, password.update) son los que Laravel espera por
    // convencion para que la notificacion ResetPassword arme el enlace sola.
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// RF-02: cierre de sesion.
Route::post('/logout', [SessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // Landing temporal de verificacion del entorno.
    Route::get('/', function () {
        return view('welcome', [
            'baseDatos' => DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
            // Los modulos con ruta ya son navegables; el resto siguen pendientes.
            'modulos' => Modulos::todos(),
        ]);
    })->name('inicio');

    // Modulo 1 - Gestion de Calificaciones. RF-14 es la consulta de solo
    // lectura; RF-13 es la planilla editable por periodo.
    // RF-05: solo roles con el permiso 'gestion-de-calificaciones' (ver RolePermissionSeeder).
    // Dentro de RF-13 hay un segundo filtro por docente, en el controller:
    // el permiso solo dice quien entra, no que asignaturas puede calificar.
    Route::middleware('permission:gestion-de-calificaciones')->group(function () {
        Route::get('/calificaciones', [CalificacionController::class, 'index'])
            ->name('calificaciones.index');

        Route::get('/calificaciones/planilla', [CalificacionController::class, 'planilla'])
            ->name('calificaciones.planilla');

        Route::put('/calificaciones/planilla', [CalificacionController::class, 'guardar'])
            ->name('calificaciones.guardar');
    });

    // Modulo 2 - Asistencia y Puntualidad (RF-15: registro diario).
    // Igual que en calificaciones, el permiso decide quien entra y el
    // controller decide de que cursos: un docente solo toma asistencia de los
    // cursos que dirige.
    Route::middleware('permission:asistencia-y-puntualidad')->group(function () {
        Route::get('/asistencia', [AsistenciaController::class, 'index'])
            ->name('asistencia.index');

        Route::put('/asistencia', [AsistenciaController::class, 'guardar'])
            ->name('asistencia.guardar');
    });
});

// RF-04: gestion de roles del sistema. RF-05: solo roles con el permiso
// 'roles-y-permisos' (admin, por defecto — ver RolePermissionSeeder).
Route::middleware(['auth', 'permission:roles-y-permisos'])->prefix('roles')->name('roles.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/crear', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{role}/editar', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
});

// RF-06 a RF-08: gestion de usuarios del sistema. RF-05: solo roles con el
// permiso 'gestion-de-usuarios' (admin, por defecto).
Route::middleware(['auth', 'permission:gestion-de-usuarios'])->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/crear', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{usuario}/editar', [UserController::class, 'edit'])->name('edit');
    Route::put('/{usuario}', [UserController::class, 'update'])->name('update');
});

// RF-09/RF-10: alta y edicion (solo Administrativo). Va ANTES del grupo de
// consulta: '/estudiantes/crear' tiene que resolver antes que el comodin
// '/estudiantes/{estudiante}' de abajo, si no Laravel intenta usar "crear"
// como el ID del estudiante.
Route::middleware(['auth', 'permission:gestion-de-estudiantes'])->prefix('estudiantes')->name('estudiantes.')->group(function () {
    Route::get('/crear', [EstudianteController::class, 'create'])->name('create');
    Route::post('/', [EstudianteController::class, 'store'])->name('store');
    Route::get('/{estudiante}/editar', [EstudianteController::class, 'edit'])->name('edit');
    Route::put('/{estudiante}', [EstudianteController::class, 'update'])->name('update');
});

// RF-11: consulta (Docente / Administrativo).
Route::middleware(['auth', 'permission:consulta-de-estudiantes'])->prefix('estudiantes')->name('estudiantes.')->group(function () {
    Route::get('/', [EstudianteController::class, 'index'])->name('index');
    Route::get('/{estudiante}', [EstudianteController::class, 'show'])->name('show');
});

// RNF-11: log de auditoria. Solo el permiso 'auditoria' (admin, por defecto).
Route::middleware(['auth', 'permission:auditoria'])->prefix('auditoria')->name('auditoria.')->group(function () {
    Route::get('/', [AuditoriaController::class, 'index'])->name('index');
});
