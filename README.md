# 📊 Compass - Sistema de Gestión Académica + Business Intelligence

<div align="center">

![Compass Badge](https://img.shields.io/badge/Status-En%20Desarrollo-orange)
![License](https://img.shields.io/badge/License-MIT-green)
![Laravel](https://img.shields.io/badge/Stack-Laravel%2FPHP%2FMySQL-red)

**Plataforma web responsive para detección temprana de riesgo académico**

Proyecto de grado - Universidad Manuela Beltrán | Ingeniería de Software

</div>

---

## 🎯 ¿Qué es Compass?

**Compass** es una plataforma integral de gestión académica que centraliza datos educativos (calificaciones, asistencia, convivencia, participación) y aplica inteligencia de negocios para detectar tempranamente estudiantes en riesgo académico.

La plataforma permite que docentes y coordinadores tomen decisiones **preventivas** en lugar de reactivas, facilitando intervenciones pedagógicas oportunas.

### Problema que resuelve

Las instituciones educativas generan gran cantidad de datos académicos que están dispersos en múltiples sistemas y no son analizados efectivamente. Esto imposibilita identificar a estudiantes en riesgo de:
- Bajo rendimiento académico
- Pérdida del año escolar
- Deserción

**Resultado:** La gestión es reactiva (intervención tardía) en lugar de preventiva.

---

## ✨ Características principales

### 📌 7 Módulos funcionales

| Módulo | Descripción | Usuarios |
|---|---|---|
| **Gestión de Calificaciones** | Registro y seguimiento de notas por asignatura y período | Docentes, Coordinadores |
| **Asistencia y Puntualidad** | Monitoreo de ausencias y retrasos | Docentes, Coordinadores |
| **Convivencia Escolar** | Registro de eventos disciplinarios según Manual de Convivencia | Coordinadores |
| **Observador Académico** | Anotaciones sobre desempeño y comportamiento | Docentes |
| **Reportes de Período** | Consolidación automática de desempeño | Coordinadores |
| **Sistema de Alertas Tempranas (EWS)** | Detección predictiva de riesgo académico | Coordinadores, Docentes |
| **Portal de Acudientes** | Visualización del progreso estudiantil | Padres/Tutores |

### 🔍 Indicadores clave de riesgo (KPIs)

- **Promedio académico** (por asignatura y general)
- **Asistencia** (porcentaje de presencia)
- **Participación en clase** (registro cualitativo)
- **Convivencia escolar** (eventos disciplinarios)
- **Tendencias** (evolución histórica de cada indicador)

### 🎨 Tecnologías BI

- **ETL (Extract-Transform-Load):** Consolidación de datos desde múltiples fuentes
- **Data Warehouse Dimensional:** Modelo dimensional optimizado para análisis
- **Dashboard Analítico:** Visualizaciones interactivas con KPIs en tiempo real
- **Sistema de Alertas:** Reglas transparentes de riesgo (no cajas negras)

---

## 🛠️ Stack Tecnológico

```yaml
Backend:
  - Laravel 11.x (PHP framework)
  - PHP 8.x+ (lenguaje de programación)
  - Composer (gestor de dependencias)

Frontend:
  - JavaScript (ES6+)
  - Bootstrap 5 (UI/UX - diseño responsivo)
  - Chart.js / D3.js (visualizaciones de datos)
  - HTML5 / CSS3

Database:
  - MySQL 8.0+ (base de datos relacional)
  - AWS RDS (servicio administrado)

BI & Analytics:
  - ETL pipeline (procesamiento de datos)
  - Dimensional modeling (modelo de datos)
  - Laravel Blade templates (dashboards dinámicos)
  - Chart.js (gráficos interactivos)

Infraestructura:
  - AWS (Amazon Web Services)
  - Git (versionamiento)
  - Composer (dependencias PHP)
  - npm (dependencias frontend)

Metodología:
  - Scrum (gestión ágil del proyecto)
```

> **Nota:** Stack confirmado en propuesta de grado. Sujeto a ajustes según validación del tutor.

---

## 👥 Autores

| Autor |
| Thomas Leonardo Castillo Castro |
| Jullians Mauricio Amado Gutiérrez |
| Mateo Antonio Chica Parra |

**Tutor:** Hugo Alfonso Ortiz Barrero  
**Institución:** Universidad Manuela Beltrán - Ingeniería de Software

---

## 📦 Instalación

### Requisitos previos

**Windows, Mac, Linux:**
- PHP 8.x+ (con extensiones: mysql, json, openssl)
- MySQL 8.0+
- Composer (gestor de dependencias PHP)
- Node.js 18+ (para herramientas frontend)
- Git
- (Opcional) Docker

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/Jullians1105/Compass.git
cd Compass
```

### 2️⃣ Estructura del proyecto

```
Compass/
├── app/                      # Código de aplicación Laravel
│   ├── Http/
│   │   ├── Controllers/      # Controladores (lógica)
│   │   └── Requests/         # Validaciones de entrada
│   ├── Models/               # Modelos Eloquent (BD)
│   └── Services/             # Servicios de negocio
│
├── routes/                   # Rutas de la aplicación
│   ├── web.php               # Rutas web
│   └── api.php               # Rutas API (si aplica)
│
├── resources/
│   ├── views/                # Vistas Blade
│   │   ├── layouts/
│   │   ├── dashboard/
│   │   └── auth/
│   ├── css/                  # Estilos personalizados
│   └── js/                   # JavaScript frontend
│
├── database/
│   ├── migrations/           # Migraciones de BD
│   ├── seeders/              # Datos de prueba
│   └── schema/               # Scripts SQL
│
├── storage/                  # Archivos de sesión, logs
├── tests/                    # Tests unitarios
├── public/                   # Archivos públicos (index.php)
│
├── docs/                     # Documentación
│   ├── NORMAS_DESARROLLO.md
│   ├── estadoProyecto.md
│   ├── ARQUITECTURA.md
│   ├── API.md
│   ├── BASE_DATOS.md
│   └── REQUISITOS.md
│
├── .env.example              # Variables de ejemplo
├── .gitignore
├── composer.json
├── package.json
├── docker-compose.yml
├── README.md
└── LICENSE (MIT)
```

### 3️⃣ Configuración inicial

#### Opción A: Instalación manual (Windows, Mac, Linux)

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Copiar archivo de configuración
cp .env.example .env

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Configurar base de datos en .env
# Edita estos valores:
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=compass_dev
DB_USERNAME=root
DB_PASSWORD=tu_contraseña

# 5. Crear base de datos
mysql -u root -p -e "CREATE DATABASE compass_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Ejecutar migraciones
php artisan migrate

# 7. Cargar datos de prueba (opcional)
php artisan db:seed

# 8. Instalar dependencias frontend
npm install

# 9. Compilar assets (CSS, JS)
npm run dev
```

#### Opción B: Con Docker

```bash
# Construir contenedores
docker-compose build

# Levantar servicios
docker-compose up -d

# Ejecutar migraciones dentro del contenedor
docker-compose exec app php artisan migrate

# Ejecutar seeders
docker-compose exec app php artisan db:seed
```

### 4️⃣ Levantar la aplicación

#### Manual

```bash
# Terminal 1 - Servidor Laravel
php artisan serve
# La app estará en http://localhost:8000

# Terminal 2 - Compilador de assets (si usas npm dev)
npm run watch
```

#### Docker

```bash
docker-compose up
# La app estará en http://localhost:8000
```

### ✅ Verificar que todo funciona

```bash
# Ver la página de inicio
curl http://localhost:8000

# Verificar migraciones ejecutadas
php artisan migrate:status

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 📚 Documentación

### Para desarrolladores

- [`docs/NORMAS_DESARROLLO.md`](docs/NORMAS_DESARROLLO.md) — Normas de código y Git
- [`docs/ARQUITECTURA.md`](docs/ARQUITECTURA.md) — Diseño de componentes
- [`docs/API.md`](docs/API.md) — Endpoints y especificación
- [`docs/BASE_DATOS.md`](docs/BASE_DATOS.md) — Modelo dimensional y esquema
- [`docs/REQUISITOS.md`](docs/REQUISITOS.md) — RF y RNF mapeados

### Para usuarios finales

- [`docs/MANUAL_USUARIO.md`](docs/MANUAL_USUARIO.md) — Guía para docentes
- [`docs/MANUAL_COORDINADOR.md`](docs/MANUAL_COORDINADOR.md) — Dashboard y alertas

### Para stakeholders

- [`docs/ESPECIFICACION_EWS.md`](docs/ESPECIFICACION_EWS.md) — Criterios de riesgo
- [`docs/PLAN_PROTECCION_DATOS.md`](docs/PLAN_PROTECCION_DATOS.md) — Cumplimiento legal

---

## 🔐 Consideraciones Éticas y Legales

### Protección de menores (Ley 1581 - 2012, Ley 1098 - 2006)

✅ **Implementado:**
- Consentimiento informado de acudientes
- Anonimización de datos en reportes públicos
- Auditoría de accesos a datos sensibles
- Retención limitada según normativa

⚠️ **Restricciones:**
- Nunca publicar datos reales de estudiantes en repositorio
- Usar datos ficticios/anonimizados para testing
- Validar alertas con coordinadores antes de mostrar a acudientes

### Sistema de alertas: No es "caja negra"

- **Criterios claros:** Documento `ESPECIFICACION_EWS.md` detalla cada regla
- **Acompañamiento pedagógico:** Alertas generan intervención, no castigo
- **Validación humana:** Coordinadores revisan antes de actuar

### Normativa aplicable

- Ley 115/1994 (Educación General)
- Decreto 1290/2009 (Evaluación)
- Ley 1620/2013 (Convivencia Escolar)
- Decreto 1965/2013 (Reglamentación de convivencia)
- Ley 1273/2009 (Protección de información)

---

## 🚀 Estado del proyecto

| Fase | Estado | Fecha | Entregable |
|------|--------|-------|-----------|
| **Propuesta** | ✅ Aprobada | Ago 2025 | Marco teórico, RF/RNF |
| **Diseño** | 🟡 En curso | Sep 2025 | Prototipos UI (Sitch) |
| **Desarrollo Sprint 1** | 🔵 Próximo | Oct 2025 | Backend + BD |
| **Desarrollo Sprint 2** | ⏳ Planificado | Nov 2025 | Frontend + ETL |
| **Testing & Validación** | ⏳ Planificado | Dic 2025 | Piloto en colegio |
| **Entrega Final** | ⏳ Planificado | Ene 2026 | Plataforma funcional |

---

## 🤝 Cómo contribuir

### Workflow Scrum

1. **Clona tu rama de feature:**
   ```bash
   git checkout -b feature/RF-XXX-descripcion
   ```

2. **Desarrolla localmente:**
   ```bash
   git add .
   git commit -m "RF-XXX: Descripción clara del cambio"
   ```

3. **Push y Pull Request:**
   ```bash
   git push origin feature/RF-XXX-descripcion
   ```

4. **Requiere revisión del Scrum Master**

### Estándares de código

- **Backend PHP:** PSR-12 (Standard PHP Recommendations)
- **Frontend JS:** ESLint + Prettier
- **Blade Templates:** Indentación coherente, componentes reutilizables
- **Commits:** Conventional Commits (`feat:`, `fix:`, `docs:`)
- **Ramas:** `main` (producción), `develop` (staging), `feature/*` (desarrollo)

### Branch protection

- `main` y `develop` requieren pull request review
- Mínimo 1 reviewer del equipo
- Tests deben pasar antes de merge

---

## 📋 Requisitos funcionales (46 RF)

Mapeados a los 7 módulos. Ver [`docs/REQUISITOS.md`](docs/REQUISITOS.md) para listado completo.

**Ejemplo:**

```
RF-01: Registrar calificación de estudiante
RF-02: Consultar promedio por período
RF-03: Exportar reporte de calificaciones
...
RF-46: Validar reglas de riesgo en tiempo real
```

---

## 🎓 Requisitos no funcionales (12 RNF)

| ID | Requisito | Target |
|----|-----------|--------|
| RNF-1 | Responsividad móvil | 95% de dispositivos |
| RNF-2 | Carga de dashboard | < 2 segundos |
| RNF-3 | Disponibilidad | 99% uptime |
| RNF-4 | Seguridad de datos | OWASP Top 10 |
| RNF-5 | Encriptación | TLS 1.2+ |
| RNF-6 | Auditoría | Logs de todos los accesos |
| RNF-7 | Escalabilidad | 500+ usuarios concurrentes |
| RNF-8 | Backup | Diario, 30 días retención |
| RNF-9 | Documentación | 100% API documentada |
| RNF-10 | Usabilidad | Compatibilidad WCAG 2.1 AA |
| RNF-11 | Mantenibilidad | Código comentado, tests |
| RNF-12 | Performance ETL | Carga nocturna < 30 min |

---

## 🐛 Reporte de bugs

¿Encontraste un problema?

1. Abre un **Issue** con título descriptivo
2. Incluye pasos para reproducir
3. Especifica entorno (navegador, SO, versión)
4. Asigna al miembro del equipo responsable

**Template:**
```markdown
## Descripción del bug
[Descripción clara]

## Pasos para reproducir
1. ...
2. ...

## Comportamiento esperado vs. actual
- Esperado: ...
- Actual: ...

## Entorno
- OS: 
- Navegador: 
- Versión: 
```

---

## 📞 Contacto

- **Tutor:** hugo.ortiz@unimb.edu.co
- **Coordinador técnico:** jullians@correo.com
- **Issues:** GitHub Issues en este repositorio

---

## 📄 Licencia

Este proyecto está bajo licencia **MIT**. Ver [`LICENSE`](LICENSE) para detalles.

```
Copyright 2025 - Thomas Leonardo Castillo Castro, 
Jullians Mauricio Amado Gutiérrez, Mateo Antonio Chica Parra

Permission is hereby granted, free of charge, to any person obtaining a copy...
```

---

## 🎯 Hoja de ruta (Roadmap)

### Q4 2025
- ✅ Aprobación de propuesta
- 🔄 Diseño visual (Sitch)
- 🔄 Arquitectura de base de datos
- ⏳ Implementación backend básica (Laravel)

### Q1 2026
- ⏳ Frontend (módulos principales)
- ⏳ Integración ETL
- ⏳ Dashboard v1

### Q2 2026
- ⏳ Testing en campo (piloto)
- ⏳ Refinamientos según feedback
- ⏳ Presentación final

---

<div align="center">

**Hecho con ❤️ por el equipo de Ingeniería de Software - UMB**

</div>