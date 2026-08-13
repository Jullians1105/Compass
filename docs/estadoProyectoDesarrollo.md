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
| admin@sjc.edu.co | admin | Administrador | Todos (incluye roles y usuarios) |
| coordinador@sjc.edu.co | coordinador | Coordinador | Todos los módulos académicos, sin roles/usuarios |
| docente@sjc.edu.co | docente | Docente | Solo Gestión de Calificaciones |

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

## Pendiente técnico conocido

- Tests automatizados: hoy solo hay smoke tests de auth
  (`tests/Feature/ExampleTest.php`). Todo el flujo de RF-01 a RF-08 se probó
  manualmente con curl en esta sesión, no queda como test de regresión.
- Auditoría de cambios (RNF-11) — hoy solo `registrado_por`/`actualizado_por`
  en calificaciones; nada audita altas/bajas de usuarios ni cambios de rol.
- `Docente.user_id` sigue sin usarse — no hay vínculo entre la ficha
  académica de un profesor y su cuenta de login. Un futuro RF de "mis
  cursos" para el rol docente lo va a necesitar.
- El warning de PHP 8.5 en `config/database.php` ya se corrigió (constante
  `PDO::MYSQL_ATTR_SSL_CA` vs `Pdo\Mysql::ATTR_SSL_CA` según versión de PHP).
