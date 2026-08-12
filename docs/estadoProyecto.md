# 📊 Estado del Proyecto Compass

**Última actualización:** [Fecha y hora]  
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
| **Fase actual** | Diseño | Prototipos en Sitch, BD en diseño |
| **Progreso general** | 15% | Propuesta aprobada, arquitectura definida |
| **Módulos iniciados** | 0/7 | Sin desarrollo aún (próximo sprint) |
| **RF completados** | 0/46 | Listos para sprint 1 |
| **RNF completados** | 0/12 | Por validar en testing |
| **Riesgos activos** | 1 | Stack confirmado, pero entorno no 100% validado |
| **Bloqueadores** | 0 | Ninguno crítico en este momento |

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

**Total: 46 RF**  
**Completados: 0 (0%)**  
**En progreso: 0**  
**No iniciados: 46 (100%)**

### Módulo 1: Gestión de Calificaciones
- [ ] RF-01: Registrar calificación de estudiante
- [ ] RF-02: Consultar promedio por período
- [ ] RF-03: Modificar calificación registrada
- [ ] RF-04: Exportar reporte de calificaciones
- [ ] RF-05: Validar rango de calificaciones (0-5)

**Subtotal:** 0/5 ✅

### Módulo 2: Asistencia y Puntualidad
- [ ] RF-06: Registrar asistencia diaria
- [ ] RF-07: Registrar retraso
- [ ] RF-08: Calcular porcentaje de asistencia
- [ ] RF-09: Generar alerta si asistencia < 75%

**Subtotal:** 0/4 ✅

### Módulo 3: Convivencia Escolar
- [ ] RF-10: Registrar evento de convivencia
- [ ] RF-11: Clasificar eventos (leve, moderado, grave)
- [ ] RF-12: Consultar historial de convivencia
- [ ] RF-13: Generar reporte de convivencia

**Subtotal:** 0/4 ✅

### Módulo 4: Observador Académico
- [ ] RF-14: Registrar anotación de docente
- [ ] RF-15: Registrar participación en clase
- [ ] RF-16: Consultar observaciones por período
- [ ] RF-17: Exportar observaciones

**Subtotal:** 0/4 ✅

### Módulo 5: Reportes de Período
- [ ] RF-18: Consolidar datos por período
- [ ] RF-19: Calcular promedio ponderado
- [ ] RF-20: Generar reporte PDF
- [ ] RF-21: Compartir reporte con acudientes

**Subtotal:** 0/4 ✅

### Módulo 6: Sistema de Alertas Tempranas (EWS)
- [ ] RF-22: Definir criterios de riesgo académico
- [ ] RF-23: Calcular score de riesgo en tiempo real
- [ ] RF-24: Generar alerta si score > umbral
- [ ] RF-25: Mostrar dashboard de alertas
- [ ] RF-26: Filtrar alertas por grado/sección
- [ ] RF-27: Registrar intervención pedagógica
- [ ] RF-28: Validar que alertas no sean falsas alarmas

**Subtotal:** 0/7 ✅

### Módulo 7: Portal de Acudientes
- [ ] RF-29: Login de acudiente con DNI/correo
- [ ] RF-30: Visualizar calificaciones del estudiante
- [ ] RF-31: Visualizar asistencia
- [ ] RF-32: Visualizar alertas de riesgo (solo nivel alto)
- [ ] RF-33: Contactar docente vía formulario
- [ ] RF-34: Descargar reportes de período

**Subtotal:** 0/6 ✅

### Requisitos transversales (RF-35 a RF-46)
- [ ] RF-35: Login de usuario (docente/coordinador)
- [ ] RF-36: Gestión de perfiles (roles y permisos)
- [ ] RF-37: Validación de datos de entrada
- [ ] RF-38: Manejo de excepciones y errores
- [ ] RF-39: Auditoría de cambios en datos sensibles
- [ ] RF-40: Backup automático diario
- [ ] RF-41: Importar datos desde CSV/Excel
- [ ] RF-42: Sincronizar con sistema SIAE (si existe)
- [ ] RF-43: Notificaciones por email
- [ ] RF-44: Búsqueda de estudiantes
- [ ] RF-45: Filtros avanzados en dashboards
- [ ] RF-46: Validar reglas de riesgo según Decreto 1290

**Subtotal:** 0/12 ✅

---

## 🎓 Requisitos no funcionales (RNF) - Estado

**Total: 12 RNF**  
**Completados: 0 (0%)**  
**En validación: 0**  
**No iniciados: 12 (100%)**

| ID | Requisito | Target | Estado | Validado |
|----|-----------|--------|--------|----------|
| RNF-1 | Responsividad móvil | 95% dispositivos | ⏳ Pendiente | No |
| RNF-2 | Carga de dashboard | < 2 segundos | ⏳ Pendiente | No |
| RNF-3 | Disponibilidad | 99% uptime | ⏳ Pendiente | No |
| RNF-4 | Seguridad de datos | OWASP Top 10 | ⏳ Pendiente | No |
| RNF-5 | Encriptación | TLS 1.2+ | ⏳ Pendiente | No |
| RNF-6 | Auditoría | Logs de todos los accesos | ⏳ Pendiente | No |
| RNF-7 | Escalabilidad | 500+ usuarios concurrentes | ⏳ Pendiente | No |
| RNF-8 | Backup | Diario, 30 días retención | ⏳ Pendiente | No |
| RNF-9 | Documentación | 100% API documentada | ⏳ Pendiente | No |
| RNF-10 | Usabilidad | WCAG 2.1 AA | ⏳ Pendiente | No |
| RNF-11 | Mantenibilidad | Código comentado, tests | ⏳ Pendiente | No |
| RNF-12 | Performance ETL | Carga < 30 min | ⏳ Pendiente | No |

---

## 🔧 Módulos - Progreso

### 📌 Módulo 1: Gestión de Calificaciones
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-01 a RF-05 (5 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Requiere diseño de tabla de calificaciones en BD
  - Validaciones según Decreto 1290

### 📌 Módulo 2: Asistencia y Puntualidad
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-06 a RF-09 (4 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Integración con calendario escolar
  - Cálculo automático de porcentajes

### 📌 Módulo 3: Convivencia Escolar
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-10 a RF-13 (4 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Seguir Manual de Convivencia del colegio
  - Clasificación de eventos según norma

### 📌 Módulo 4: Observador Académico
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-14 a RF-17 (4 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Campo de texto libre para docentes
  - Búsqueda y filtrado de observaciones

### 📌 Módulo 5: Reportes de Período
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-18 a RF-21 (4 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Generación de PDF
  - Distribución automática a acudientes

### 📌 Módulo 6: Sistema de Alertas Tempranas (EWS)
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-22 a RF-28 (7 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Crítico:** ⚠️ Módulo principal del proyecto
- **Notas:**
  - Algoritmo de scoring transparente (no "caja negra")
  - Validación con coordinadores en UAT
  - Documento de especificación de reglas: `docs/ESPECIFICACION_EWS.md`

### 📌 Módulo 7: Portal de Acudientes
- **Estado:** ⏳ No iniciado
- **RF asociados:** RF-29 a RF-34 (6 requisitos)
- **% Completado:** 0%
- **Responsable:** [Asignar en sprint]
- **Notas:**
  - Protección de datos: mostrar solo información del hijo/a
  - Cumplimiento Ley 1581

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
- ✅ Stack confirmado: Node.js + React + PostgreSQL
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
| PostgreSQL en lugar de MongoDB | Datos estructurados, integridad referencial importante | Sep 2025 | ✅ Tutor |
| React para frontend | Componentes reutilizables, comunidad activa | Sep 2025 | ✅ Tutor |

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
