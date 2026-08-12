# 🛠️ Estado de Desarrollo — Compass

Documento técnico: qué está construido, cómo levantarlo en local, y qué falta.
Complementa a `estadoProyecto.md` (que trackea sprints/RF a nivel de gestión de
proyecto); este se enfoca en el estado real del código.

**Última actualización:** 2026-08-12

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
npm run dev   # en otra terminal, para Bootstrap/Chart.js
```

Usuarios de prueba (sembrados por el seeder, solo local):

| Email | Contraseña | Rol |
|---|---|---|
| admin@sjc.edu.co | admin | admin |
| coordinador@sjc.edu.co | coordinador | coordinador |

## Módulos implementados

### ✅ Autenticación (RF-01) y roles básicos (RF-05)
- Login/logout reales (`App\Http\Controllers\Auth\SessionController`), sesión
  en BD, CSRF, `redirect()->intended()`.
- Columna `role` en `users` (string libre, no enum — van a faltar roles como
  "administrativo" para el módulo financiero).
- Todas las rutas de la app quedaron detrás del middleware `auth`.
- **Falta:** RF-02 a RF-08 (recuperación de contraseña, gestión de usuarios
  desde la UI, bloqueo de cuentas, auditoría de accesos) — no están definidos
  con precisión en el repo, se necesita el texto exacto de la tesis para no
  construir algo que no coincida con lo pedido.

### ✅ Gestión de Calificaciones — consulta (RF-14)
- Esquema completo (12 tablas): años lectivos, períodos, evaluaciones,
  calificaciones, escalas de desempeño, grados, cursos, asignaciones,
  estudiantes, matrículas, docentes, asignaturas.
- `/calificaciones`: tabla por curso/asignatura con las 4 notas de período,
  definitiva ponderada y estado de desempeño (Decreto 1290), más un gráfico
  de distribución (Chart.js).
- Responsive: tabla normal en desktop, tarjetas con desplegable por período
  en celular.
- Diseño visual ajustado al prototipo de Stitch (`docs/prototipos/`), en
  Bootstrap en vez de Tailwind.
- CHECK de rango 0-5 a nivel de BD en `calificaciones.nota` (respaldo de la
  validación del modelo, que las inserciones masivas se saltan).
- **Falta:** registro/edición de notas (RF-13) — depende de que RF-05 tenga
  reglas reales de "un docente solo califica sus asignaciones".

### ⏳ Sin empezar
Asistencia, Convivencia Escolar, Observador Académico, Reportes de Período,
Alertas Tempranas (EWS / RF-29 riesgo), Portal de Acudientes, Matrículas,
Dashboard Financiero. Ver README.md para la lista completa de 9 módulos.

## Decisiones de datos de prueba

- El seeder cubre solo grados 9°, 10° y 11°, **un curso por grado** (sin
  secciones A/B — el colegio piloto no tiene paralelos).
- Los estudiantes tienen perfiles de rendimiento (no notas al azar), para que
  el futuro módulo de riesgo tenga algo real que detectar.
- ~15% de los estudiantes no tiene consentimiento de tratamiento de datos
  (Ley 1581), a propósito, para poder probar que se excluyen de la analítica.

## Pendiente técnico conocido

- Tests automatizados del cálculo de notas (hoy solo hay smoke tests de auth).
- Auditoría de cambios (RNF-11) — hoy solo `registrado_por`/`actualizado_por`.
- El warning de PHP 8.5 en `config/database.php` ya se corrigió (constante
  `PDO::MYSQL_ATTR_SSL_CA` vs `Pdo\Mysql::ATTR_SSL_CA` según versión de PHP).
