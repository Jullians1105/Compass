# 🛠️ Estado de Desarrollo — Compass

Documento técnico: qué está construido, cómo levantarlo en local, y qué falta.
Complementa a `estadoProyecto.md` (que trackea sprints/RF a nivel de gestión de
proyecto); este se enfoca en el estado real del código.

**Última actualización:** 2026-08-13

---

## Stack instalado

- Laravel 12.66.0 / PHP 8.2+ (probado en 8.2.12 y 8.5.8)
- MySQL 8.0+
- Bootstrap 5.3.8 + Chart.js 4.5.1, compilados con Vite
- Sin frontend framework (Blade + Bootstrap; ver README para el debate
  Bootstrap vs Tailwind, sigue abierto para el tutor)

## Cómo levantar el entorno local

```bash
git fetch origin && git checkout feature/RF-13-modulo-calificaciones
composer install && npm install
cp .env.example .env
php artisan key:generate
# Editar DB_PASSWORD en .env si tu MySQL root tiene contraseña
mysql -u root -p -e "CREATE DATABASE compass_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate:fresh --seed
php artisan serve
npm run dev   # en otra terminal — recompila CSS/JS al vuelo; sin esto, los
              # cambios en resources/ no aparecen hasta correr npm run build
```

MAIL_MAILER=log por defecto: los correos (recuperación de contraseña,
contraseña temporal de usuarios nuevos) no se envían de verdad, van a
`storage/logs/laravel.log`.

Usuarios de prueba (sembrados por el seeder, solo local):

| Email | Contraseña | Rol | Permisos |
|---|---|---|---|
| admin@sjc.edu.co | admin | Administrador | Todos (incluye roles, usuarios y auditoría) |
| coordinador@sjc.edu.co | coordinador | Coordinador | Todos los módulos académicos, sin roles/usuarios/auditoría |
| docente@sjc.edu.co | docente | Docente | Gestión de Calificaciones + consulta de Estudiantes |

## Módulos implementados

### ✅ Autenticación y control de acceso (RF-01 a RF-08)

- **RF-01 (login) / RF-02 (logout)** — `App\Http\Controllers\Auth\SessionController`.
  Sesión en BD, CSRF, `redirect()->intended()`. El login rechaza cuentas con
  `activo = false` (ver RF-07).
- **RF-03 (recuperación de contraseña)** — password broker nativo de Laravel
  (`Illuminate\Support\Facades\Password`), rutas `password.request` /
  `password.email` / `password.reset` / `password.update`. Usa la tabla
  `password_reset_tokens` que ya traía el scaffold de Laravel.
- **RF-04 (gestión de roles del sistema)** — `/roles` (index/crear/editar),
  solo para quien tenga el permiso `roles-y-permisos`. Modelo: `roles` /
  `permissions` / `permission_role` (pivot). El catálogo de permisos sale de
  `App\Support\Modulos` (los 9 módulos, cada uno con su slug `permiso`) más
  `roles-y-permisos` y `gestion-de-usuarios`, que son pantallas de
  administración, no módulos académicos. `database/seeders/RolePermissionSeeder.php`
  crea los 3 roles semilla (admin, coordinador, docente) con sus permisos por
  defecto.
- **RF-05 (control de acceso según rol)** — middleware `permission:<slug>`
  (`App\Http\Middleware\CheckPermission`, alias `permission` en
  `bootstrap/app.php`), aplicado por ruta (`/calificaciones`, `/roles`,
  `/usuarios`). El sidebar (`layouts/app.blade.php`) y la landing
  (`welcome.blade.php`) ocultan/deshabilitan los módulos que el rol no puede
  ver, usando `User::tienePermiso($slug)`.
- **RF-06 (registro de usuarios) / RF-07 (actualización) / RF-08 (consulta)**
  — `/usuarios` (index con filtros rol/estado/búsqueda + paginación,
  crear, editar), solo para quien tenga el permiso `gestion-de-usuarios`.
  El alta genera una contraseña temporal (`Str::password(12)`) y la manda por
  `App\Notifications\CuentaCreada`. La edición solo toca nombre/apellido/
  email/rol/estado — el documento de identidad no se edita después del alta
  (así lo especifica la tesis para RF-07).
- **Esquema de `users`:** `nombres`, `apellidos`, `email`, `role_id` (FK a
  `roles`), `tipo_documento`, `documento` (único), `telefono`, `activo`.
  Mismo patrón que ya usaban `Docente`/`Estudiante` — se reemplazó la columna
  `name` (un solo campo) para no tener dos convenciones distintas en el
  mismo esquema.

### ✅ Gestión de Calificaciones — consulta (RF-14)

- Esquema completo (12 tablas): años lectivos, períodos, evaluaciones,
  calificaciones, escalas de desempeño, grados, cursos, asignaciones,
  estudiantes, matrículas, docentes, asignaturas.
- `/calificaciones`: tabla por curso/asignatura con las 4 notas de período,
  definitiva ponderada y estado de desempeño (Decreto 1290), más un gráfico
  de distribución (Chart.js). Detrás del permiso `gestion-de-calificaciones`.
- Responsive: tabla normal en desktop, tarjetas con desplegable por período
  en celular.
- Diseño visual ajustado al prototipo de Stitch (`docs/prototipos/`), en
  Bootstrap en vez de Tailwind.
- CHECK de rango 0-5 a nivel de BD en `calificaciones.nota` (respaldo de la
  validación del modelo, que las inserciones masivas se saltan).
- **Falta:** registro/edición de notas (**RF-13**) — solo existe el esquema
  y datos de seeder, no hay pantalla. Con RF-05 ya resuelto, un docente
  debería poder calificar solo sus propias asignaciones — esa regla todavía
  no está escrita en ningún lado.

### ✅ Estudiantes (RF-09 a RF-11)

- No es uno de los 9 módulos del README — es una entidad compartida (como
  `Docente`), usada por varios módulos futuros (Asistencia, Convivencia...).
- **RF-09 (registro)** — `/estudiantes/crear`. Crea el `Estudiante` y su
  `Matricula` en el curso del grado solicitado dentro del año lectivo activo;
  la confirmación devuelve el ID de esa matrícula ("número de matrícula
  preliminar").
- **RF-10 (actualización)** — `/estudiantes/{id}/editar`. Datos personales/
  contacto + reasignación de grado (mueve o crea la matrícula vigente). El
  documento de identidad no se edita después del alta.
- **RF-11 (consulta de perfil)** — `/estudiantes/{id}`, actor Docente o
  Administrativo. `/estudiantes` (listado + búsqueda por nombre/documento)
  es la plomería para llegar al ID, no un RF en sí mismo.
- Esquema: se agregaron `acudiente_nombre`/`acudiente_telefono` a
  `estudiantes` (antes no existían).
- Permisos: `gestion-de-estudiantes` (admin + coordinador) para RF-09/RF-10,
  `consulta-de-estudiantes` (+ docente) para RF-11 — dos permisos porque los
  actores de la tesis son distintos.

### ⏳ Sin empezar

Asistencia, Convivencia Escolar, Observador Académico, Reportes de Período,
Alertas Tempranas (EWS / RF-29 riesgo), Portal de Acudientes, Matrículas,
Dashboard Financiero. Ver README.md para la lista completa de 9 módulos.

Con el texto completo de la tesis ya en el repo (`docs/RF y no RF.txt`, 46 RF
+ 26 RNF), estos módulos ya se pueden definir con precisión — antes no había
para RF-02 a RF-12.

## Decisiones de datos de prueba

- El seeder cubre solo grados 9°, 10° y 11°, **un curso por grado** (sin
  secciones A/B — el colegio piloto no tiene paralelos).
- Los estudiantes tienen perfiles de rendimiento (no notas al azar), para que
  el futuro módulo de riesgo tenga algo real que detectar.
- ~15% de los estudiantes no tiene consentimiento de tratamiento de datos
  (Ley 1581), a propósito, para poder probar que se excluyen de la analítica.
- `RolePermissionSeeder` corre antes que los usuarios en `DatabaseSeeder`
  (los usuarios necesitan `role_id`). El orden importa si se agregan más
  seeders.

## ✅ Auditoría (RNF-11) y protección de datos personales (RNF-10)

- Tabla `auditorias` (append-only, sin `updated_at`): `user_id` nullable,
  `evento`, `auditable_type`/`auditable_id` (morph, opcional), `detalle`
  (JSON), `ip`.
- `App\Models\Auditoria::registrar()` es el punto de entrada único para crear
  un evento; usa `auth()->id()` y `request()->ip()` automáticamente.
- `App\Models\Concerns\Auditable` (trait) engancha `created`/`updated` de
  Eloquent — usado por `User`, `Role` y `Estudiante`. No cubre `delete()`
  porque ningún controller expone borrar estos modelos todavía.
- `Estudiante` define `$auditableSoloClaves = true`: la auditoría de un
  estudiante guarda solo **qué campos cambiaron**, nunca sus valores (Ley
  1581 — no duplicar datos de un menor en una segunda tabla). `User`/`Role`
  sí guardan valores, excepto `password`/`remember_token` (excluidos siempre
  por el trait).
- `RoleController` audita el cambio de permisos aparte del `update()` del
  rol — `permissions()->sync()` es una tabla pivote y no dispara el evento
  `updated` de Eloquent.
- `SessionController` audita `login`, `login_fallido` (con el email
  intentado, para detectar fuerza bruta) y `logout`.
- `EstudianteController@show` audita `consultado` — RNF-10: acceder al
  perfil de un menor queda trazado, no solo modificarlo.
- Pantalla `/auditoria` (filtros por evento/usuario/rango de fechas,
  paginada), detrás del permiso `auditoria` (solo admin, igual criterio que
  `roles-y-permisos` y `gestion-de-usuarios`).

## Testing

- **BD de testing separada:** `compass_test` (MySQL — no hay extensión
  `sqlite` en este entorno para usar `:memory:`). Configurada directo en
  `phpunit.xml` (`DB_DATABASE=compass_test`). Nunca corras
  `php artisan test` sin esto: `RefreshDatabase` hace `migrate:fresh`, y si
  apuntara a `compass_dev` borraría los datos sembrados de desarrollo.
- 16 tests automatizados (`tests/Feature/`): `PasswordHashingTest` (RNF-08),
  `RoleAccessControlTest` (RNF-09), `AuditLogTest` (RNF-10/RNF-11), más el
  smoke test original de auth. Cubren RF-01 a RF-11 solo parcialmente —
  la mayoría de esta sesión se validó manualmente con curl, no queda como
  test de regresión.

## Pendiente técnico conocido

- Tests automatizados: falta cobertura de RF-01 a RF-08 más allá de lo que
  tocan `RoleAccessControlTest`/`AuditLogTest` de forma indirecta (no hay
  tests de `UserController`, `RoleController` ni `PasswordReset*` en sí).
- `Docente.user_id` sigue sin usarse — no hay vínculo entre la ficha
  académica de un profesor y su cuenta de login. Un futuro RF de "mis
  cursos" para el rol docente lo va a necesitar.
- El warning de PHP 8.5 en `config/database.php` ya se corrigió (constante
  `PDO::MYSQL_ATTR_SSL_CA` vs `Pdo\Mysql::ATTR_SSL_CA` según versión de PHP).
