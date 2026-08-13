# 📊 Estado del Proyecto Compass

**Última actualización:** 2026-08-13  
**Frecuencia de actualización:** Diaria (fin de día) + Fin de sprint  
**Responsable de compilar:** Jullians Mauricio Amado Gutiérrez

---

## 📋 Tabla de Contenidos

1. [Resumen ejecutivo](#-resumen-ejecutivo)
2. [Fases del proyecto](#-fases-del-proyecto)
3. [Requisitos funcionales (RF) - Estado](#-requisitos-funcionales-rf---estado)
4. [Requisitos no funcionales (RNF) - Estado](#-requisitos-no-funcionales-rnf---estado)
5. [Módulos - Progreso](#-módulos---progreso)
6. [Bloqueadores y riesgos](#-bloqueadores-y-riesgos)
7. [Aprendizajes y cambios](#-aprendizajes-y-cambios)
8. [Próximos pasos (Sprint actual)](#-próximos-pasos-sprint-actual)
9. [Registro diario de cambios](#-registro-diario-de-cambios)

---

## 🎯 Resumen ejecutivo

| Métrica | Valor | Comentario |
|---------|-------|-----------|
| **Fase actual** | Desarrollo Sprint 1 | Calificaciones (consulta) y autenticación funcionando en local |
| **Progreso general** | 30% | Modulo 1 (Calificaciones) y auth/roles basicos construidos |
| **Módulos iniciados** | 1/9 | Gestión de Calificaciones (consulta, RF-14). Ver README para los 9 módulos reales |
| **RF completados** | 12/46 (RF-01 a RF-11, RF-14) | RF-13 solo tiene base construida (esquema + seeder), falta la pantalla de registro/edición |
| **RNF completados** | 4/26 validados (RNF-08 a RNF-11) | Primeros con test automatizado; varios más ya implementados sin validar (RNF-07, RNF-12, RNF-14, RNF-16/17) |
| **Riesgos activos** | 1 | Stack confirmado, pero entorno no 100% validado |
| **Bloqueadores** | 0 | Ninguno crítico en este momento |

> ⚠️ **Nota (2026-08-13):** RF-05 y RF-13 estaban marcados como completados y no lo
> están. Verificado contra el código, no solo contra este documento:
> - **RF-05 (roles):** existe el campo `role` en `User` y `esAdmin()`, pero
>   ningún rol restringe nada en la práctica — lo dice el propio código
>   (`app/Models/User.php`). Es una base, no el requisito cumplido.
> - **RF-13 (registrar/modificar calificación):** el commit "RF-13" trajo
>   esquema de BD, modelos y datos de seeder — no hay pantalla ni controlador
>   para que un docente registre o edite una nota. `CalificacionController`
>   lo marca explícitamente como pendiente.
>
> Completos de verdad hoy: solo **RF-01** (login/logout) y **RF-14** (consulta
> de calificaciones).
>
> **Actualización 2026-08-13 (tarde):** con el texto real de la tesis se
> construyeron **RF-02** (cierre de sesión — ya estaba cubierto por el logout
> de RF-01), **RF-03** (recuperación de contraseña), **RF-04** (gestión de
> roles del sistema: roles y permisos ahora son tablas reales, con CRUD para
> el actor Administrativo en `/roles`) y **RF-05** (control de acceso según
> rol: middleware `permission:<slug>` en `/calificaciones` y `/roles`, y el
> sidebar/landing ocultan los módulos que el rol no puede usar). Probado con
> los tres roles semilla (admin, coordinador, docente@sjc.edu.co/docente).
>
> **Actualización 2026-08-13 (noche):** se completaron también **RF-06**
> (registro de usuarios, con contraseña temporal por notificación), **RF-07**
> (actualización: nombre/email/rol/estado de cuenta) y **RF-08** (consulta
> con filtros por rol/estado/búsqueda + paginación), todos en `/usuarios`,
> solo para el actor Administrativo (permiso `gestion-de-usuarios`).
>
> Único pendiente de este bloque: **RF-13**, que solo tiene esquema de BD y
> datos de seeder — falta la pantalla para que un docente registre/edite una
> nota.

---

## 📅 Fases del proyecto

| # | Fase | Descripción | Estado | Fecha Inicio | Fecha Fin Estimada | % Completado |
|---|------|-------------|--------|---|---|---|
| 1 | **Propuesta** | Marco teórico, requisitos, justificación | ✅ Completo | Ago 2025 | Ago 2025 | 100% |
| 2 | **Diseño** | Prototipos UI, arquitectura, modelo de BD | 🟡 En curso | Sep 2025 | Sep 2025 | 40% |
| 3 | **Desarrollo Sprint 1** | Backend básico + Base de datos | ⏳ Planificado | Oct 2025 | Oct 2025 | 0% |
| 4 | **Desarrollo Sprint 2** | Frontend + ETL | ⏳ Planificado | Nov 2025 | Nov 2025 | 0% |
| 5 | **Testing & Validación** | Pruebas unitarias, integración, UAT en colegio | ⏳ Planificado | Dic 2025 | Dic 2025 | 0% |
| 6 | **Entrega Final** | Ajustes finales, documentación, presentación | ⏳ Planificado | Ene 2026 | Ene 2026 | 0% |

---

## 📋 Requisitos funcionales (RF) - Estado

> Numeración real de la tesis, contra `docs/RF y no RF.txt` (fuente de verdad
> textual completa — descripción/actor/entrada/salida de cada RF vive ahí,
> aquí solo el título y el estado). Reescrito el 2026-08-13; hasta esa fecha
> este checklist tenía la numeración vieja e incorrecta.

**Total: 46 RF**  
**Completados: 12 (26%)**  
**En progreso: 0**  
**No iniciados: 34 (74%)**

### RF-01 a RF-08 — Autenticación, roles y gestión de usuarios
- [x] RF-01: Autenticación de usuarios
- [x] RF-02: Cierre de sesión del sistema
- [x] RF-03: Recuperación de contraseña
- [x] RF-04: Gestión de roles del sistema
- [x] RF-05: Control de acceso según rol
- [x] RF-06: Registro de usuarios del sistema
- [x] RF-07: Actualización de usuarios del sistema
- [x] RF-08: Consulta de usuarios del sistema

**Subtotal:** 8/8 ✅

### RF-09 a RF-12 — Estudiantes
- [x] RF-09: Registro de estudiantes
- [x] RF-10: Actualización de información de estudiantes
- [x] RF-11: Consulta del perfil del estudiante
- [ ] RF-12: Consulta del historial académico del estudiante

**Subtotal:** 3/4 ✅

### RF-13 a RF-14 — Calificaciones
- [ ] RF-13: Registro de calificaciones — solo esquema de BD + datos de seeder, falta la pantalla
- [x] RF-14: Consulta de calificaciones por estudiante

**Subtotal:** 1/2 ✅

### RF-15 a RF-16 — Asistencia
- [ ] RF-15: Registro de asistencia
- [ ] RF-16: Consulta de asistencia por estudiante

**Subtotal:** 0/2 ✅

### RF-17 a RF-18 — Observaciones disciplinarias
- [ ] RF-17: Registro de observaciones disciplinarias
- [ ] RF-18: Consulta del observador del estudiante

**Subtotal:** 0/2 ✅

### RF-19 a RF-20 — Boletines e informes institucionales
- [ ] RF-19: Generación de boletines académicos
- [ ] RF-20: Generación de informes académicos institucionales

**Subtotal:** 0/2 ✅

### RF-21 a RF-24 — Importación, validación y almacenamiento de datos
- [ ] RF-21: Importación de datos académicos desde archivos
- [ ] RF-22: Validación de datos importados
- [ ] RF-23: Almacenamiento de datos académicos en la base de datos
- [ ] RF-24: Actualización de registros académicos importados

**Subtotal:** 0/4 ✅

### RF-25 a RF-28 — Indicadores académicos y dashboard
- [ ] RF-25: Cálculo de indicadores académicos
- [ ] RF-26: Visualización de indicadores académicos en Dashboard
- [ ] RF-27: Visualización del rendimiento académico por curso
- [ ] RF-28: Visualización del rendimiento académico por estudiante

**Subtotal:** 0/4 ✅

### RF-29 a RF-33 — Riesgo académico (EWS)
- [ ] RF-29: Clasificación automática de riesgo académico
- [ ] RF-30: Visualización de estudiantes en riesgo académico
- [ ] RF-31: Consulta de estudiantes en riesgo por curso
- [ ] RF-32: Consulta de estudiantes en riesgo por bienestar estudiantil
- [ ] RF-33: Seguimiento académico de estudiantes en riesgo

**Subtotal:** 0/5 ✅

### RF-34 a RF-46 — Matrículas, pagos, mora y KPIs financieros
- [ ] RF-34: Gestión de matrículas
- [ ] RF-35: Gestión de pagos de mensualidades
- [ ] RF-36: Inscripción de matrícula
- [ ] RF-37: Formalización de matrícula
- [ ] RF-38: Registro de pago de arancel
- [ ] RF-39: Generación de comprobante de pago
- [ ] RF-40: Consulta de estado de matrícula
- [ ] RF-41: Filtrado de estudiantes en mora
- [ ] RF-42: Generación de reporte de estudiantes en mora
- [ ] RF-43: Envío de alertas de pago pendiente
- [ ] RF-44: Cálculo de KPIs financieros
- [ ] RF-45: Visualización de estado de matrículas en Dashboard
- [ ] RF-46: Visualización de ingresos financieros en Dashboard

**Subtotal:** 0/13 ✅

---

## 🎓 Requisitos no funcionales (RNF) - Estado

> Numeración real de la tesis (26 RNF), contra `docs/RF y no RF.txt`.
> Reescrito el 2026-08-13 — antes este documento listaba 12 RNF que no
> coincidían con la tesis.

**Total: 26 RNF**  
**Validados con test automatizado: 4 (RNF-08, RNF-09, RNF-10, RNF-11)**  
**Implementados sin validación formal: RNF-07, RNF-12, RNF-14, RNF-16, RNF-17, RNF-20**  
**No iniciados: el resto (ver tabla)**

> 📌 "Validado" en la columna de abajo significa que hay un test automatizado
> (`tests/Feature/`) que lo prueba explícitamente, no solo que el código lo
> toque de pasada. RNF-08/09/10/11 son los primeros con esa validación
> (2026-08-13) — el resto de los 🟡 "implementados" todavía no tiene test.

| ID | Requisito | Target | Estado | Validado |
|----|-----------|--------|--------|----------|
| RNF-01 | Tiempo de respuesta del sistema | < 3 segundos | ⏳ Pendiente | No |
| RNF-02 | Tiempo de carga del Dashboard | < 5 segundos | ⏳ Pendiente | No |
| RNF-03 | Soporte de usuarios concurrentes | 10+ simultáneos | ⏳ Pendiente | No |
| RNF-04 | Disponibilidad del sistema | 99% horario académico | ⏳ Pendiente | No |
| RNF-05 | Copia de seguridad automática | Diaria | ⏳ Pendiente | No |
| RNF-06 | Recuperación ante fallos | Restaurar desde backup | ⏳ Pendiente | No |
| RNF-07 | Autenticación segura | Credenciales + HTTPS | 🟡 Parcial (falta HTTPS en prod) | No |
| RNF-08 | Cifrado de contraseñas | Algoritmo seguro | ✅ Validado (Bcrypt, Laravel) | **Sí** — `PasswordHashingTest` |
| RNF-09 | Control de acceso por roles | Restringir por rol | ✅ Validado (RF-04/RF-05) | **Sí** — `RoleAccessControlTest` |
| RNF-10 | Protección de datos personales | Normativa colombiana (Ley 1581) | ✅ Validado (acceso a perfil auditado, cambios sin exponer valores) | **Sí** — `AuditLogTest` |
| RNF-11 | Registro de auditoría | Logs de accesos/cambios | ✅ Validado (tabla `auditorias`: login/logout/login fallido/CRUD de usuarios, roles y estudiantes) | **Sí** — `AuditLogTest` |
| RNF-12 | Diseño responsive | Adaptable a pantallas | 🟡 Implementado (sidebar/tablas) | No |
| RNF-13 | Facilidad de uso | Interfaz intuitiva | ⏳ Pendiente | No |
| RNF-14 | Consistencia visual | Interfaz uniforme | 🟡 Implementado (Bootstrap 5) | No |
| RNF-15 | Compatibilidad de navegadores | Chrome, Edge, Firefox | ⏳ Pendiente | No |
| RNF-16 | Integridad de datos | Sin duplicados, consistentes | 🟡 Parcial (constraints únicos en BD) | No |
| RNF-17 | Validación de datos | Validar antes de almacenar | 🟡 Implementado (validación por form) | No |
| RNF-18 | Escalabilidad del sistema | Crecer sin degradar | ⏳ Pendiente | No |
| RNF-19 | Mantenibilidad del software | Arquitectura modular | ⏳ Pendiente | No |
| RNF-20 | Documentación del sistema | Técnica y de usuario | 🟡 En progreso (estos documentos) | No |
| RNF-21 | Exportación de datos | PDF o Excel | ⏳ Pendiente | No |
| RNF-22 | Consistencia en tiempo real (matrículas) | Bloqueo optimista/pesimista | ⏳ Pendiente | No |
| RNF-23 | Precisión en KPIs financieros | 2 decimales, sin redondeo | ⏳ Pendiente | No |
| RNF-24 | Integración segura de alertas (email/SMS) | Credenciales cifradas | ⏳ Pendiente | No |
| RNF-25 | Retención de datos financieros | Registro inmutable | ⏳ Pendiente | No |
| RNF-26 | Performance en reportes voluminosos | 500+ registros en < 10s | ⏳ Pendiente | No |

---

## 🔧 Módulos - Progreso

> ⚠️ Estos son los 9 módulos de UI del README (pantallas), que no coinciden
> 1 a 1 con los bloques de RF de la tesis — por ejemplo la tesis junta
> "Convivencia Escolar" y "Observador Académico" en un solo bloque (RF-17 a
> RF-18), y no tiene un bloque de RF propio para "Portal de Acudientes" (los
> acudientes aparecen como actor dentro de otros RF, no como módulo aparte).
> Los rangos de abajo son los que sí están claros; donde no, se deja explícito
> en vez de inventar una división que no está en `docs/RF y no RF.txt`.

### 📌 Módulo 1: Gestión de Calificaciones
- **Estado:** 🟡 En progreso (consulta lista, registro pendiente)
- **RF asociados:** RF-13 a RF-14 (2 requisitos) — RF-14 ✅, RF-13 ⏳
- **% Completado:** 50%
- **Responsable:** Jullians / Mateo
- **Notas:**
  - Validaciones según Decreto 1290 ya implementadas en la consulta

### 📌 Módulo 2: Asistencia y Puntualidad
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-15 a RF-16 (2 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Integración con calendario escolar
  - Cálculo automático de porcentajes

### 📌 Módulo 3: Convivencia Escolar
- **Estado:** ⏳ No iniciado
- **RF asociados:** parte de RF-17 a RF-18 (ver nota de arriba — compartido con Módulo 4)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Seguir Manual de Convivencia del colegio
  - Clasificación de eventos según norma

### 📌 Módulo 4: Observador Académico
- **Estado:** ⏳ No iniciado
- **RF asociados:** parte de RF-17 a RF-18 (ver nota de arriba — compartido con Módulo 3)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Campo de texto libre para docentes
  - Búsqueda y filtrado de observaciones

### 📌 Módulo 5: Reportes de Período
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-19 a RF-20 (2 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Generación de PDF
  - Distribución automática a acudientes

### 📌 Módulo 6: Sistema de Alertas Tempranas (EWS)
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-25 a RF-33 (9 requisitos: indicadores + riesgo)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Crítico:** ⚠️ Módulo principal del proyecto
- **Notas:**
  - Algoritmo de scoring transparente (no "caja negra")
  - Validación con coordinadores en UAT
  - Documento de especificación de reglas: `docs/ESPECIFICACION_EWS.md`

### 📌 Módulo 7: Portal de Acudientes
- **Estado:** ⏳ No iniciado
- **RF asociados:** ninguno propio (ver nota de arriba) — reutiliza RF-14, RF-16, RF-18, RF-30 desde la perspectiva del acudiente
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Protección de datos: mostrar solo información del hijo/a
  - Cumplimiento Ley 1581

### 📌 Módulo 8: Gestión de Matrículas
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-34 a RF-43 (10 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]

### 📌 Módulo 9: Dashboard Financiero
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-44 a RF-46 (3 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]

---

## ⚠️ Bloqueadores y riesgos

### Bloqueadores actuales

**Críticos:**
- ❌ Ninguno en este momento

**Moderados:**
- ❌ Ninguno en este momento

**Menores:**
- ❌ Ninguno en este momento

---

### Riesgos identificados

| ID | Riesgo | Impacto | Probabilidad | Mitigación | Estado |
|----|--------|---------|--------------|-----------|--------|
| R-01 | Cambio de requisitos a mitad del proyecto | Alto | Media | Validar con tutor en cada sprint review | 🟡 Activo |
| R-02 | Datos reales del colegio no disponibles en tiempo | Alto | Media | Usar datos ficticios/anonimizados desde ya | 🟡 Activo |
| R-03 | Falta de tiempo para testing exhaustivo | Medio | Media | Empezar testing desde sprint 2 | 🟡 Activo |
| R-04 | Conflictos de merge en ramas | Bajo | Media | Rebase frecuente, PRs pequeños | ✅ Controlado |
| R-05 | Base de datos creciente ralentiza queries | Medio | Baja | Indexación desde diseño, monitoreo | ✅ Controlado |

---

## 💡 Aprendizajes y cambios

### Cambios tecnológicos confirmados

**Sprint Diseño (Actual):**
- ✅ Stack confirmado: PHP + Laravel + MySQL + Bootstrap + Chart.js + AWS (ver
  registro del 2026-08-11: la tesis no menciona Node.js, React ni PostgreSQL)
- ✅ Herramientas: Sitch para UI, GitHub para versionamiento
- ✅ Metodología: Scrum con sprints de 2 semanas
- ✅ Documentación: README + NORMAS_DESARROLLO.md + estadoProyecto.md

**Por validar:**
- ⏳ Framework de testing (Jest, Mocha)
- ⏳ Herramienta de BI (Power BI, Tableau, custom)
- ⏳ Servidor de deployment (AWS, Azure, Heroku)
- ⏳ Estrategia de seguridad (JWT, OAuth, 2FA)

### Decisiones arquitectónicas

| Decisión | Rationale | Fecha | Aprobado |
|----------|-----------|-------|----------|
| Modelo dimensional para DW | Optimizado para análisis educativo | Sep 2025 | ✅ Tutor |
| Sistema de alertas con reglas transparentes | Cumple requisito ético de no "caja negra" | Sep 2025 | ✅ Tutor |
| MySQL como motor relacional | Especificado en la tesis; datos estructurados, integridad referencial importante | Sep 2025 | ✅ Tutor |
| Laravel Blade para frontend | Especificado en la tesis; evita duplicar lógica de validación en un SPA separado | Sep 2025 | ✅ Tutor |

### Lecciones aprendidas

- **Sprint Diseño:**
  - La documentación clara desde el inicio ahorra tiempo después
  - Prototipos en Sitch ayudan a validar UX antes de código
  - Definir normas de desarrollo hace que PRs sean más rápidas de revisar

---

## 🚀 Próximos pasos (Sprint actual)

### Sprint 0 - Diseño (Sep 2025)

**Objetivos:**
1. Finalizar prototipos UI en Sitch (12 pantallas)
2. Esquema de base de datos completo y validado
3. Documentación técnica lista para desarrollo
4. Entorno local funcionando en todos los equipos

**Tareas por hacer:**
- [ ] Completar prototipos faltantes (8/12)
- [ ] Diseño ER de base de datos finalizado
- [ ] Documento de ESPECIFICACION_EWS.md
- [ ] Todos logran instalar y levantar el proyecto localmente
- [ ] Primer PR de prueba (cambio menor al README)

**Fin de Sprint:**
- [ ] Sprint review con tutor
- [ ] Demo de prototipos
- [ ] Validación de arquitectura

---

### Sprint 1 - Backend + BD (Oct 2025)

**Objetivos:**
1. Base de datos migrada a producción
2. API REST funcional para módulos 1-5
3. Autenticación y autorización implementadas
4. Primeros tests unitarios

**RF por implementar:**
- RF-01 a RF-21 (módulos 1-5)
- RF-35 a RF-40 (requisitos transversales básicos)

**RNF a validar:**
- RNF-9 (100% API documentada)
- RNF-11 (código comentado)

---

### Sprint 2 - Frontend + ETL (Nov 2025)

**Objetivos:**
1. Interfaz responsiva funcionando
2. Dashboard de alertas implementado
3. ETL procesando datos automáticamente

**RF por implementar:**
- RF-22 a RF-34 (módulos 6-7)
- RF-41 a RF-46 (integración y validaciones finales)

---

---

## 📝 Registro diario de cambios

> **Instrucciones:** Cada miembro del equipo agrega una entrada al final de esta sección.  
> **Formato:** Fecha | Quién | Qué hizo | Estado | Bloqueadores

### Formato de entrada

```markdown
**[YYYY-MM-DD] - [Quién]**
- ✅ Completado: [Descripción]
- 🟡 En progreso: [Descripción] (% estimado)
- ⏳ Próximo: [Descripción]
- 🚫 Bloqueador: [Descripción] (severidad: Alta/Media/Baja)
- 💡 Aprendizaje: [Descripción]
```

### Registros

#### [2025-09-01] - Inicio de Sprint Diseño

**Jullians Mauricio Amado Gutiérrez**
- ✅ Completado: Creación de repositorio GitHub
- ✅ Completado: Configuración inicial de Sitch para prototipos
- 🟡 En progreso: Diseño de 12 pantallas (4/12 completadas)
- ⏳ Próximo: Validar prototipos con tutor, feedback y ajustes
- 💡 Aprendizaje: Sitch permite exportar directamente a componentes React (ahorraría tiempo)

**Thomas Leonardo Castillo Castro**
- ✅ Completado: Diseño ER inicial de base de datos
- 🟡 En progreso: Modelo dimensional (60% completado)
- ⏳ Próximo: Validar con equipo, crear scripts de migración
- 💡 Aprendizaje: Es crítico definir bien índices desde el inicio para performance

**Mateo Antonio Chica Parra**
- ✅ Completado: Repositorio clonable en local, entorno funcionando en 3 equipos
- ✅ Completado: Documento NORMAS_DESARROLLO.md
- 🟡 En progreso: Instrucciones de instalación para Mac (completadas)
- ⏳ Próximo: Validar en computadora con Windows
- 💡 Aprendizaje: La documentación clara evita 10 mensajes de apoyo después

---

#### [2026-08-11] - Mateo Antonio Chica Parra

- ✅ Completado: Scaffold inicial de Laravel en el repositorio (antes solo había docs)
- ✅ Completado: Entorno local Windows funcionando — PHP 8.2.12 (XAMPP), Composer 2.10.2, MySQL 9.4, Node 24
- ✅ Completado: BD `compass_dev` creada (utf8mb4) y migraciones base ejecutadas
- ✅ Completado: Bootstrap 5.3.8 + Chart.js 4.5.1 integrados vía Vite, compilando desde `resources/css/app.scss`
- ✅ Completado: Landing de verificación en `/` que confirma Laravel + PHP + MySQL + Bootstrap + Chart.js
- ✅ **RESUELTO — Contradicción de stack.** Se contrastó contra el documento de tesis
  (Cap. II, "Bases o fundamentos teóricos", sección de tecnologías). La tesis especifica
  textualmente: HTML5, CSS3, JavaScript, **Bootstrap 5**, PHP 8.x, **Laravel 11**,
  **MySQL 8.x**, phpMyAdmin, **Chart.js** y **AWS (EC2, RDS for MySQL, S3, Certificate
  Manager)**. No menciona React ni PostgreSQL en ninguna parte.
  - El `README.md` estaba correcto; la sección "Aprendizajes y cambios" de este documento
    (Node.js + React + PostgreSQL "aprobado por el tutor") es texto de plantilla erróneo
    y **debe borrarse**.
  - Bootstrap 5 está justificado en la tesis citando RNF-12 (responsive) y RNF-15
    (compatibilidad de navegadores).
- 🚫 Bloqueador: **La numeración de RF y RNF del README no coincide con la tesis**
  (severidad: Alta)
  - El README listaba `RF-01: Registrar calificación`; en la tesis RF-01 es
    *Autenticación de usuarios*. El registro de calificaciones es **RF-13** y la consulta
    es **RF-14**.
  - La tesis define **RNF-01 a RNF-26**, no los 12 del README.
  - Se corrigió el README con la numeración real. Las ramas y commits deben usar la de
    la tesis, que es la que revisa el tutor.
- 📌 **Corrección de alcance:** las pantallas de *Gestión de Matrículas* y *Dashboard
  Financiero* del prototipo **sí están en el alcance**. Son los objetivos específicos 5 y 6
  de la tesis y ocupan RF-34 a RF-46 (matrículas, pagos de mensualidades, comprobantes,
  estudiantes en mora, alertas de pago y KPIs financieros). La lista de 7 módulos del README
  está incompleta.
- 🚫 Bloqueador: **Laravel 11 no es instalable** (severidad: Media)
  - Composer 2.10 bloquea todas las versiones 11.x (hasta 11.55.0) por advisories de seguridad sin
    parchear: el soporte de seguridad de Laravel 11 terminó y las vulnerabilidades no se corrigen.
  - Se instaló **Laravel 12.66.0**, que resuelve limpio ("No security vulnerability advisories found")
    y es compatible con PHP 8.2. Hay que actualizar el `README.md` que dice "Laravel 11.x".
  - Relevante para RNF-4 (OWASP Top 10) y para el manejo de datos de menores (Ley 1581).
- ✅ Completado: Esquema del núcleo académico (12 tablas), modelos Eloquent y seeders
  con datos ficticios; vista de consulta de calificaciones (**RF-14**)
- 📌 **Decisión de modelado:** las evaluaciones son entidad propia (tabla `evaluaciones`),
  no una columna `tipo_evaluacion` dentro de `calificaciones`. RF-13 exige registrar el
  tipo de evaluación (parcial, final...), lo que implica **varias notas por período** en
  una misma asignatura. El diseño inicial tenía una restricción única sobre
  `(matrícula, asignación, período)` que lo impedía. Ahora la nota del período es el
  promedio ponderado de sus evaluaciones, y la definitiva pondera esas notas por el peso
  de cada período.
- 📌 **Ojo con las dos escalas.** La escala de desempeño del Decreto 1290
  (Superior/Alto/Básico/Bajo) **no es** la escala de riesgo. RF-29 define niveles
  bajo/medio/alto con una puntuación de 0 a 100 que combina notas, asistencia y
  observaciones. Son cosas distintas; hoy solo está modelada la primera.
- 📌 Decisión de alcance: **el proyecto cubre únicamente los grados 9°, 10° y 11°.**
  No estaba documentado en ninguna parte. Los seeders generan solo esos tres grados
  (6 cursos, 168 estudiantes). La tabla `grados` no impone el límite: si más adelante
  se incluyen 6° a 8°, basta agregarlos al seeder sin tocar el esquema.
- ⏳ Próximo: RF-35 (login) y RF-36 (roles y permisos) para arrancar Sprint 1
- ⏳ Próximo: Validar el esquema ER con Thomas antes de que se construya encima
- ⏳ Próximo: Tests automatizados del módulo 1 (RNF-11); hoy la única verificación
  del cálculo de definitiva es visual
- ⏳ Próximo: Ajustar el diseño de la vista de calificaciones al prototipo de Stitch
- 💡 Aprendizaje: PHP 8.2.12 de XAMPP es de octubre 2023 y le faltan ~2 años de parches. Funciona
  para Laravel 12, pero conviene actualizarlo antes de pensar en el despliegue a AWS.

---

#### [2026-08-12] - Jullians Mauricio Amado Gutiérrez (con Claude)

- ✅ Completado: Revisión de los commits de RF-13/RF-14 — corregido bug de
  `fecha_consentimiento` en el seeder, agregado CHECK de rango 0-5 en BD
  (respaldo de la validación del modelo), eliminado un N+1 en el seeder, y
  unificado el cálculo de promedio ponderado que estaba duplicado en dos sitios
- ✅ Completado: Diseño visual de `/calificaciones` ajustado al prototipo de
  Stitch (paleta, sidebar con iconos, tarjetas de estadísticas), reimplementado
  en Bootstrap 5 para no depender de Tailwind
- ✅ Completado: **RF-01 (autenticación) y base de RF-05 (roles)** — login,
  logout, rutas protegidas con middleware `auth`, usuarios de prueba
  `admin@sjc.edu.co` / `coordinador@sjc.edu.co`
- ✅ Completado: Diseño responsive (sidebar tipo cajón en celular, tabla de
  calificaciones como tarjetas con desplegable por período en pantallas chicas)
- ✅ Completado: Corregido el warning de PHP 8.5 (`PDO::MYSQL_ATTR_SSL_CA`)
  que salía en pantalla en `config/database.php`
- 📌 Decisión de datos: el seeder ahora crea **un solo curso por grado**
  (9°, 10°, 11°, sin secciones A/B) — el colegio piloto no tiene paralelos
- ⏳ Próximo: RF-02 a RF-08 (recuperación de contraseña, gestión de usuarios,
  bloqueo de cuentas, auditoría de accesos) — sin definir con precisión hasta
  tener el texto exacto de la tesis
- ⏳ Próximo: boletín académico (RF-19/RF-20), candidato fuerte para siguiente
  pantalla porque reutiliza el cálculo de notas que ya existe

---

#### [2026-08-13] - Jullians Mauricio Amado Gutiérrez (con Claude)

- ✅ Completado: Corregido el bug de responsive donde el sidebar se quedaba
  siempre visible en móvil — el CSS compilado en `public/build/` estaba
  desactualizado respecto a `resources/css/app.scss` (no había `npm run dev`
  corriendo). Recompilado con `npm run build`.
- ✅ Completado: Cargado `docs/RF y no RF.txt` con el texto real y completo de
  los 46 RF y 26 RNF de la tesis — ya no hace falta adivinar el alcance de
  RF-02 a RF-12.
- ✅ Completado: **RF-02 (cierre de sesión)** — verificado que el logout de
  RF-01 ya cumplía el requisito (invalida sesión, regenera token CSRF).
- ✅ Completado: **RF-03 (recuperación de contraseña)** — flujo completo con
  el password broker nativo de Laravel: `/forgot-password` envía el enlace
  (MAIL_MAILER=log en local), `/reset-password/{token}` cambia la clave.
  Probado de punta a punta con curl.
- ✅ Completado: **RF-04 (gestión de roles del sistema)** — se reemplazó la
  columna `users.role` (string libre) por tablas `roles`/`permissions`/
  `permission_role`. CRUD en `/roles` (index/crear/editar), protegido con
  middleware `admin` nuevo, solo para el actor Administrativo. Catálogo de
  permisos derivado de `App\Support\Modulos`. Seeder `RolePermissionSeeder`
  crea roles Administrador/Coordinador/Docente con permisos por defecto.
- ✅ Completado: **RF-05 (control de acceso según rol)** — middleware nuevo
  `permission:<slug>` (reemplaza al `admin` ad hoc de RF-04) aplicado a
  `/calificaciones` (permiso `gestion-de-calificaciones`) y `/roles` (permiso
  `roles-y-permisos`). El sidebar y la landing ahora ocultan/deshabilitan los
  módulos que el rol no tiene permiso de ver, usando el mismo slug que ya
  vive en `App\Support\Modulos` (`permiso`), para no duplicar la lista.
- 📌 Decisión de datos: se agregó usuario semilla `docente@sjc.edu.co` /
  `docente` — antes no había ningún usuario de prueba con el rol docente
  (solo admin y coordinador), y hacía falta uno para poder probar que el
  control de acceso realmente restringe algo.
- ⏳ Próximo: RF-06 a RF-08 (alta/edición/consulta de usuarios — hoy solo
  existen los 3 usuarios del seeder, no hay pantalla para crear más), RF-13
  (registrar/editar calificación), boletín académico (RF-19/RF-20)

---

#### [2026-08-13] (noche) - Jullians Mauricio Amado Gutiérrez (con Claude)

- ✅ Completado: **RF-06 (registro de usuarios)** — `/usuarios/crear`, genera
  una contraseña temporal (`Str::password`) y la envía por notificación
  (`App\Notifications\CuentaCreada`, mismo mecanismo de log local que RF-03).
- ✅ Completado: **RF-07 (actualización de usuarios)** — `/usuarios/{id}/editar`,
  edita nombres/apellidos/email/rol/estado de cuenta (el documento de
  identidad no se edita después del alta, según el texto de la tesis).
- ✅ Completado: **RF-08 (consulta de usuarios)** — `/usuarios`, filtros por
  rol/estado + búsqueda por nombre o correo, paginado.
- 📌 Decisión de esquema: la tabla `users` ahora tiene `nombres`/`apellidos`/
  `tipo_documento`/`documento`/`telefono`/`activo`, replicando el mismo
  patrón que ya usaban `Docente` y `Estudiante` — se eliminó la columna
  `name` (un solo campo) que quedaba inconsistente con el resto del esquema.
- ✅ Completado: el estado de cuenta (`activo`) ahora bloquea el login de
  verdad (`SessionController::store`) — si no, el toggle de `/usuarios` no
  serviría para nada.
- ✅ Completado: `Paginator::useBootstrapFive()` en `AppServiceProvider` — la
  paginación de Laravel viene en Tailwind por defecto y la app no carga ese
  framework.
- 📌 Nuevo permiso `gestion-de-usuarios` (RolePermissionSeeder), solo para el
  rol admin — mismo criterio que `roles-y-permisos`, ambas son pantallas de
  administración, no módulos académicos.
- ✅ Completado: reescritos `docs/estadoProyectoDesarrollo.md` y
  `.claude/NOTAS.md` para que reflejen el estado real del código (venían
  desactualizados desde el 2026-08-12, antes de todo el trabajo de
  autenticación/roles/usuarios de hoy).
- ⏳ Próximo: RF-13 (registrar/editar calificación), boletín académico
  (RF-19/RF-20), tests automatizados (hoy todo se probó manualmente con curl)

---

#### [2026-08-13] (madrugada+1) - Jullians Mauricio Amado Gutiérrez (con Claude)

- ✅ Completado: **RF-09 (registro de estudiantes)** — `/estudiantes/crear`,
  crea el estudiante y su matrícula en el curso del grado solicitado (año
  lectivo activo); la confirmación devuelve el número de matrícula
  preliminar (el ID de esa matrícula).
- ✅ Completado: **RF-10 (actualización de estudiantes)** — `/estudiantes/{id}/editar`,
  edita datos personales/contacto y reasigna grado (mueve la matrícula
  vigente a otro curso, o crea una si no existía). El documento de
  identidad no se edita, mismo criterio que RF-07.
- ✅ Completado: **RF-11 (consulta de perfil)** — `/estudiantes/{id}`, con
  todos los campos que pide la tesis (documento, nacimiento, género, grado
  actual, acudiente, dirección, contacto, fecha de registro). Actor
  Docente/Administrativo: agregado `/estudiantes` (listado + búsqueda) como
  plomería necesaria para llegar al ID, no es un RF en sí.
- 📌 Decisión de esquema: se agregaron `acudiente_nombre`/`acudiente_telefono`
  a `estudiantes` (la tesis los pide como entrada de RF-09 y salida de RF-11).
- 📌 Decisión de permisos: dos permisos nuevos en vez de uno — `gestion-de-estudiantes`
  (admin+coordinador, alta/edición) y `consulta-de-estudiantes` (+docente,
  solo lectura) — porque RF-09/RF-10 y RF-11 tienen actores distintos en la
  tesis (Administrativo vs. Docente/Administrativo).
- 📌 "Estudiantes" no es uno de los 9 módulos del README — es una entidad
  compartida (como Docente), así que no vive en `App\Support\Modulos`.
- ⏳ Próximo: RF-12 (historial académico del estudiante — reutiliza
  `Matricula::definitiva()` que ya existe), RF-13 (registrar/editar
  calificación), tests automatizados

---

#### [2026-08-13] (madrugada+2) - Jullians Mauricio Amado Gutiérrez (con Claude)

- ✅ Completado: reescrita la lista de RF y RNF en `docs/estadoProyecto.md` y
  `README.md` contra el texto real de la tesis (`docs/RF y no RF.txt`) — 46 RF
  agrupados en los mismos bloques que ya usaba el README, 26 RNF reales
  (antes 12 inventados). Corregida también la sección "Módulos - Progreso",
  incluyendo los módulos 8 y 9 que faltaban (Matrículas, Dashboard Financiero).
- ✅ Completado: **base de datos de testing separada** (`compass_test`) y
  `phpunit.xml` apuntando a ella — antes `RefreshDatabase` habría corrido
  `migrate:fresh` sobre `compass_dev` (el entorno de desarrollo real) porque
  no hay extensión `sqlite` disponible para usar `:memory:`.
- ✅ Completado: **RNF-08 (cifrado de contraseñas) y RNF-09 (control de
  acceso por rol) validados con tests automatizados** — `PasswordHashingTest`
  y `RoleAccessControlTest` (`tests/Feature/`), 9 tests. Antes solo se habían
  probado a mano con curl.
- ✅ Completado: **RNF-11 (registro de auditoría)** — tabla `auditorias`
  (append-only, sin `updated_at`), modelo `Auditoria::registrar()`, trait
  `Auditable` (`app/Models/Concerns/`) enganchado a `User`, `Role` y
  `Estudiante` para loguear altas/modificaciones. `SessionController` audita
  login/login fallido (con el email intentado)/logout. `RoleController`
  audita los cambios de permisos aparte, porque `sync()` de una tabla pivote
  no dispara el evento `updated` del modelo. Pantalla `/auditoria` (filtros
  por evento/usuario/fecha), solo para el permiso `auditoria` (admin).
- ✅ Completado: **RNF-10 (protección de datos personales, Ley 1581)** —
  resuelto junto con RNF-11: cada vez que alguien abre el perfil de un
  estudiante (`EstudianteController@show`) queda registrado en la auditoría,
  que es el mecanismo de trazabilidad/accountability que pide la ley.
- 📌 Decisión de diseño: la auditoría de `Estudiante` guarda **solo los
  nombres de los campos que cambiaron, nunca sus valores**
  (`$auditableSoloClaves = true` en el modelo) — evita duplicar datos
  sensibles de un menor en una segunda tabla. `User`/`Role` sí guardan
  valores (no son datos de menores), excepto `password`/`remember_token`,
  que el trait excluye siempre.
- ✅ Completado: test `AuditLogTest` (4 tests) valida login/login fallido/
  auditoría de alta de estudiante sin exponer datos/consulta de perfil.
  Total del proyecto: **16 tests automatizados**, todos verdes.
- ⏳ Próximo: RF-12, RF-13, y extender `Auditable` a otros modelos si se
  agregan más CRUD sensibles (ej. cuando exista Matrículas/Financiero)

---

## 📊 Métricas del proyecto

### Velocity (velocidad del equipo)

| Sprint | RF Completados | RNF Validados | Puntos | Tendencia |
|--------|---|---|---|---|
| Sprint 0 (Diseño) | 0 | 0 | - | → En progreso |
| Sprint 1 (Previsto) | 26 | 3 | - | ⏳ Estimado |
| Sprint 2 (Previsto) | 20 | 9 | - | ⏳ Estimado |

### Burn-down chart (para el final de sprint)

```
Puntos pendientes
|
|  ████████ Ideal
|  ███████░ Real
|  ██████░░
|  █████░░░
|  ████░░░░
|  ███░░░░░
|  ██░░░░░░
|  █░░░░░░░
|
└─────────────── Días del sprint
```

---

## 🎯 KPIs de calidad

| Métrica | Target | Actual | Estado |
|---------|--------|--------|--------|
| Cobertura de tests | 80% | 0% | ⏳ Iniciará Sprint 1 |
| Code review time | < 24h | - | ✅ Meta clara |
| Pull requests por dev/semana | 2-3 | 0 | ⏳ Iniciará Sprint 1 |
| Bugs encontrados en testing | < 10% RF | - | ⏳ Iniciará Sprint 5 |
| Documentación actualizada | 100% | 70% | 🟡 En progreso |

---

## 📞 Contactos rápidos

| Rol | Nombre | Email | Slack |
|-----|--------|-------|-------|
| Tutor | Hugo Alfonso Ortiz Barrero | hugo.ortiz@unimb.edu.co | @Hugo |
| Coordinador (Jullians) | Jullians Mauricio Amado G. | jullians@correo.com | @Jullians |
| Backend (Thomas) | Thomas Leonardo Castillo C. | thomas@correo.com | @Thomas |
| Frontend (Mateo) | Mateo Antonio Chica Parra | mateo@correo.com | @Mateo |

---

<div align="center">

**Última actualización:** [Auto-actualizar con cada cambio]  
**Próxima revisión:** [Fin del sprint]

Si algo está rojo 🔴 o amarillo 🟡, **actúa ahora**. No esperes al fin del sprint.

</div>
