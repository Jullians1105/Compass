# 📋 Normas Generales de Desarrollo - Compass

**Documento de referencia para todo el equipo**  
Lee esto antes de empezar a desarrollar. 🚀

---

## 📖 Tabla de Contenidos

1. [Control de versiones (Git)](#-control-de-versiones-git)
2. [Estructura de carpetas](#-estructura-de-carpetas)
3. [Cómo usar Claude](#-cómo-usar-claude)
4. [Comunicación del equipo](#-comunicación-del-equipo)
5. [Actualización de estado](#-actualización-de-estado-del-proyecto)
6. [Instalación local (Windows, Mac)](#-instalación-local)
7. [Solución de problemas comunes](#-solución-de-problemas-comunes)

---

**Crear una .claude y poner a claude en gitignore, leer todo este documento antes de comenzar a desarrollar, leer estado de proyecto, actualizar estadoProyectoDesarrollo y el .claude de cada uno de los integrantes**

## 🔀 Control de versiones (Git)

### Reglas básicas

**Nunca** pushea directamente a `main` o `develop`. Siempre usa ramas de feature.

```bash
# 1. Actualiza antes de empezar
git checkout develop
git pull origin develop

# 2. Crea tu rama de feature
git checkout -b feature/RF-XXX-descripcion-corta
# Ejemplo: feature/RF-05-registrar-calificacion

# 3. Desarrolla y commitea
git add .
git commit -m "RF-XXX: Descripción clara del cambio"
# Ejemplo: git commit -m "RF-05: Agregar validación de notas"

# 4. Push a tu rama
git push origin feature/RF-XXX-descripcion-corta

# 5. Abre Pull Request en GitHub
# - Título: "RF-XXX: Descripción"
# - Descripción: Qué cambió y por qué
# - Asigna para review
```

### Convención de commits

```
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Cambios en documentación
style: Formato de código (sin cambio lógico)
refactor: Cambio de estructura (sin cambio de funcionalidad)
test: Agregar/actualizar tests
chore: Tareas de mantenimiento
```

### Branches

```
main/          → Código en producción (solo merges de release)
develop/       → Rama de integración (aquí se junta el trabajo de todos)
feature/*      → Tu rama personal de desarrollo
hotfix/*       → Correcciones urgentes
```

---

## 📁 Estructura de carpetas

Respeta esta estructura. Si necesitas cambiar algo, **consulta primero**.

```
Compass/
├── app/                           # Código de aplicación Laravel
│   ├── Http/
│   │   ├── Controllers/           # Controladores (lógica de negocios)
│   │   ├── Requests/              # Validaciones de entrada
│   │   └── Middleware/            # Middleware (autenticación, etc)
│   ├── Models/                    # Modelos Eloquent (BD)
│   ├── Services/                  # Servicios de negocio
│   ├── Events/                    # Eventos (si aplica)
│   └── Exceptions/                # Excepciones personalizadas
│
├── routes/                        # Definición de rutas
│   ├── web.php                    # Rutas web (vistas)
│   └── api.php                    # Rutas API (JSON)
│
├── resources/
│   ├── views/                     # Vistas Blade
│   │   ├── layouts/               # Templates
│   │   ├── dashboard/             # Vistas de dashboards
│   │   ├── auth/                  # Vistas de autenticación
│   │   └── components/            # Componentes reutilizables
│   ├── css/                       # Estilos CSS/SCSS
│   └── js/                        # JavaScript (no esencial)
│
├── database/
│   ├── migrations/                # Migraciones de BD
│   ├── seeders/                   # Datos de prueba
│   └── factories/                 # Factories para testing
│
├── tests/                         # Tests unitarios e integración
│   ├── Unit/                      # Tests unitarios
│   └── Feature/                   # Tests de características
│
├── storage/
│   ├── logs/                      # Logs de la aplicación
│   ├── app/                       # Almacenamiento temporal
│   └── framework/                 # Cache, sessions
│
├── public/                        # Carpeta pública (accesible desde web)
│   ├── index.php                  # Punto de entrada
│   └── assets/                    # CSS, JS compilados
│
├── docs/                          # Documentación
│   ├── NORMAS_DESARROLLO.md       # Este archivo
│   ├── estadoProyecto.md          # Estado del proyecto
│   ├── ARQUITECTURA.md
│   ├── API.md
│   ├── BASE_DATOS.md
│   └── REQUISITOS.md
│
├── .env.example                   # Variables de ejemplo
├── .gitignore
├── composer.json                  # Dependencias PHP
├── composer.lock
├── package.json                   # Dependencias frontend
├── package-lock.json
├── artisan                        # CLI de Laravel
├── docker-compose.yml
├── README.md
└── LICENSE (MIT)
```
---

---
## 💬 Comunicación del equipo

### Por Discord/Slack

**Cada día:**
- ✅ Al empezar: "Hoy trabajo en RF-XXX"
- ✅ Al terminar: "Hice PR al feature/RF-XXX"
- ✅ Si hay bloqueador: "Necesito ayuda con..."

**Formato corto:**
```
🚀 Hoy: Validación de calificaciones (RF-05)
📊 Progreso: 70%
🚫 Bloqueador: No sé cómo hacer migraciones
```

### En Pull Request (GitHub)

**Título:**
```
RF-XXX: Descripción breve (máx 50 caracteres)
```

**Descripción:**
```markdown
## Descripción
Qué hace este PR (1-2 líneas)

## Cambios
- Cambio 1
- Cambio 2

## Testing
Cómo testeaste (pasos para reproducir)

## Links relacionados
- Cierra: #123 (si cierra un issue)
```

---

## 🔄 Actualización de estado del proyecto

### Por qué es importante

Necesitamos saber qué anda bien, qué falta y qué está atrasado.  
**Esto es CRÍTICO para la planificación de sprints.**

### Cuándo actualizar

- ✅ **Fin de cada día** si trabajaste
- ✅ **Fin de sprint** (compilación general)
- ✅ **Si hay cambios importantes**

### Cómo actualizar

1. **Abre:** `docs/estadoProyecto.md`
2. **Ve a:** Sección "Registro diario de cambios"
3. **Agrega una entrada** con tu nombre y lo que hiciste
4. **Haz commit y push:**

```bash
git add docs/estadoProyecto.md
git commit -m "docs: Actualizar estado (fin de día)"
git push origin develop
```

### Formato de entrada

```markdown
#### [YYYY-MM-DD] - [Tu nombre]

- ✅ Completado: [Descripción]
- 🟡 En progreso: [Descripción] (% estimado)
- ⏳ Próximo: [Descripción]
- 🚫 Bloqueador: [Descripción] (severidad: Alta/Media/Baja)
- 💡 Aprendizaje: [Descripción]
```

**Ejemplo:**
```markdown
#### [2025-09-02] - Thomas Leonardo Castillo Castro

- ✅ Completado: Crear modelo de Calificaciones
- ✅ Completado: Migración de tabla
- 🟡 En progreso: Validaciones (80%)
- ⏳ Próximo: Tests unitarios
- 💡 Aprendizaje: Usar fillable en modelos Eloquent
```

---

## 💻 Instalación local

### Windows

#### Paso 1: Instalar requisitos

1. **PHP 8.x**
   - Descargar de: https://windows.php.net/download/
   - O usar: XAMPP/WAMP (incluye PHP + MySQL)

2. **MySQL 8.0**
   - Descargar de: https://dev.mysql.com/downloads/mysql/
   - O usar: XAMPP/WAMP

3. **Composer**
   - Descargar de: https://getcomposer.org/download/

4. **Git**
   - Descargar de: https://git-scm.com/

5. **Node.js 18+** (para compilar assets)
   - Descargar de: https://nodejs.org/

#### Paso 2: Clonar y configurar

```bash
# Clonar repositorio
git clone https://github.com/Jullians1105/Compass.git
cd Compass

# Instalar dependencias PHP
composer install

# Copiar archivo de configuración
copy .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Instalar dependencias frontend
npm install
```

#### Paso 3: Configurar base de datos

1. **Crear base de datos en MySQL:**
   - Abre MySQL Workbench o línea de comandos
   - Ejecuta:
   ```sql
   CREATE DATABASE compass_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Editar archivo `.env`:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=compass_dev
   DB_USERNAME=root
   DB_PASSWORD=tu_contraseña
   ```

#### Paso 4: Ejecutar migraciones

```bash
# Crear tablas
php artisan migrate

# Cargar datos de prueba (opcional)
php artisan db:seed
```

#### Paso 5: Levantar la aplicación

```bash
# Terminal 1 - Servidor Laravel
php artisan serve
# La app estará en http://localhost:8000

# Terminal 2 - Compilar assets (opcional)
npm run watch
```

---

### Mac (Intel o Apple Silicon)

#### Paso 1: Instalar requisitos con Homebrew

```bash
# Si no tienes Homebrew:
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Instalar PHP 8.x
brew install php

# Instalar MySQL
brew install mysql

# Iniciar MySQL
brew services start mysql

# Instalar Composer
brew install composer

# Instalar Node.js
brew install node
```

#### Paso 2: Verificar versiones

```bash
php -v
mysql --version
composer --version
node -v
git --version
```

#### Paso 3: Clonar y configurar

```bash
# Clonar repositorio
git clone https://github.com/Jullians1105/Compass.git
cd Compass

# Instalar dependencias PHP
composer install

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Instalar dependencias frontend
npm install
```

#### Paso 4: Configurar base de datos

1. **Crear base de datos:**
   ```bash
   mysql -u root -p -e "CREATE DATABASE compass_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. **Editar archivo `.env`:**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=compass_dev
   DB_USERNAME=root
   DB_PASSWORD=contraseña_que_pusiste
   ```

#### Paso 5: Ejecutar migraciones

```bash
# Crear tablas
php artisan migrate

# Cargar datos de prueba (opcional)
php artisan db:seed
```

#### Paso 6: Levantar la aplicación

```bash
# Terminal 1 - Servidor Laravel
php artisan serve
# La app estará en http://localhost:8000

# Terminal 2 - Compilar assets (opcional)
npm run watch
```

**Nota para Apple Silicon (M1/M2/M3):**
Si tienes problemas con MySQL, intenta:
```bash
# En lugar de brew install mysql
brew install mysql@8.0

# Crear symlink
brew link mysql@8.0 --force
```

---

## 🔧 Solución de problemas comunes

### "No me conecta a la base de datos"

```bash
# Verifica que MySQL esté corriendo
# Windows: Busca "Services" y mira MySQL
# Mac: brew services list | grep mysql

# Verifica el .env
cat .env | grep DB_

# Prueba conectarte directamente
mysql -u root -p compass_dev
```

### "Composer install no funciona"

```bash
# Actualiza Composer
composer self-update

# Limpia caché
composer clear-cache

# Reintentar
composer install

# Si sigue fallando, verifica PHP version
php -v  # Debe ser 8.x+
```

### "Mi rama está atrasada a develop"

```bash
git fetch origin
git rebase origin/develop
# Si hay conflictos, resuelve manualmente, luego:
git rebase --continue
git push origin feature/RF-XXX -f
```

### "Olvidé hacer checkout a develop antes de crear la rama"

```bash
git checkout develop
git pull origin develop
git checkout -b feature/RF-XXX-desc
# Tu código sigue aquí
```

### "npm run watch no funciona"

```bash
# Verifica que Node.js esté instalado
node -v

# Limpia caché
rm -rf node_modules
rm package-lock.json

# Reinstala
npm install

# Intenta de nuevo
npm run watch
```

### "No encuentro el archivo .env"

```bash
# Copia el .env.example
cp .env.example .env  # Mac/Linux
copy .env.example .env  # Windows

# Genera la clave
php artisan key:generate
```

### "php artisan migrate da error"

```bash
# Verifica que la BD existe
mysql -u root -p -e "SHOW DATABASES;"

# Si no existe, créala
mysql -u root -p -e "CREATE DATABASE compass_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Vuelve a intentar
php artisan migrate

# Si sigue fallando, revisa los logs
tail -f storage/logs/laravel.log
```

---

## 📌 Resumen rápido

```
1. Git: Siempre rama feature → PR → review → merge
2. Código: Limpio, comentado con propósito (PSR-12)
3. Claude: Para código y explicaciones, no decisiones
4. Estado: Actualiza docs/estadoProyecto.md cada día
5. Comunicación: Chat diario + PR descriptivos
6. Instalación: Funciona en Windows y Mac
7. Dudas: Pregunta, no adivines
```

---

## ✅ Checklist antes de empezar

- [ ] Leí este documento completo
- [ ] Tengo Git configurado localmente
- [ ] Tengo PHP 8.x instalado
- [ ] Tengo MySQL corriendo
- [ ] Tengo Composer instalado
- [ ] Logré clonar el repo
- [ ] Logré instalar dependencias (composer install)
- [ ] Logré crear la BD y ejecutar migraciones
- [ ] Logré levantar `php artisan serve`
- [ ] Entiendo cómo hacer PR
- [ ] Sé dónde actualizar el estado del proyecto

**Si algún checkmark te falla → pregunta en el chat.**

---

<div align="center">

**Hecho por el equipo de Compass - Actualizado: Agosto 2025**

Preguntas: Abre un issue en GitHub o escribe en Discord

</div>