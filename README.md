# SkillSwap 🎓

> **Marketplace de tutorías universitarias con IA y Blockchain**
> Plataforma que conecta alumnos universitarios con tutores de su misma carrera, permitiendo buscar, agendar y registrar sesiones de tutoría de manera transparente y verificable.

---

## 📋 Índice

- [Descripción del Proyecto](#descripción-del-proyecto)
- [Líneas Temáticas Integradas](#líneas-temáticas-integradas)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación y Configuración](#instalación-y-configuración)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Funcionalidades Principales](#funcionalidades-principales)
- [Arquitectura Técnica](#arquitectura-técnica)
- [Módulo Blockchain](#módulo-blockchain)
- [Módulo de IA](#módulo-de-ia)
- [Seguridad Implementada](#seguridad-implementada)
- [Base de Datos](#base-de-datos)
- [Decisiones Arquitectónicas](#decisiones-arquitectónicas)
- [Métricas y Pruebas](#métricas-y-pruebas)
- [Equipo](#equipo)

---

## Descripción del Proyecto

SkillSwap es una aplicación web MVC desarrollada en PHP que resuelve un problema real: **la dificultad de los estudiantes universitarios para encontrar tutores de materias difíciles dentro de su propia institución**.

La plataforma permite:
- A los **alumnos**: buscar tutores filtrados por materia y área, ver su disponibilidad, enviar solicitudes y chatear una vez aceptadas.
- A los **tutores**: publicar sus materias, gestionar horarios disponibles, aceptar/rechazar solicitudes y recibir calificaciones.
- Al **admin**: aprobar tutores, moderar denuncias y visualizar el registro de transacciones en Blockchain.

---

## Líneas Temáticas Integradas

| # | Unidad | Implementación concreta |
|---|--------|------------------------|
| ✅ | **Unidad I – Lenguajes de Programación** | PHP 8 con `declare(strict_types=1)`, paradigma orientado a objetos, patrón MVC sin framework |
| ✅ | **Unidad II – Sistemas de Tipos** | Type hints en todos los métodos, `FETCH_ASSOC`, `EMULATE_PREPARES=false`, ENUMs en columnas SQL |
| ✅ | **Unidad III – Diseño de Compiladores / BD** | MySQL con 9 tablas relacionales, 2 triggers, integridad referencial, PDO con prepared statements |
| ✅ | **Unidad IV – Seguridad en Sistemas Computacionales** | `password_hash/verify`, `htmlspecialchars`, `session_regenerate_id`, control de roles, validación MIME |
| ✅ | **IA** | Sistema de recomendación por score ponderado (matching alumno ↔ tutor por materias y calificaciones) |
| ✅ | **Blockchain** | Registro de pagos en Sepolia Testnet (simulado con `tx_hash` SHA-256, tabla `blockchain_pagos`) |

---

## Requisitos del Sistema

- **PHP** 8.0+
- **MySQL / MariaDB** 10.4+
- **Apache / Nginx** (o XAMPP en desarrollo)
- Extensiones PHP: `pdo`, `pdo_mysql`, `fileinfo`

---

## Instalación y Configuración

### 1. Clonar el repositorio

```bash
git clone https://github.com/[tu-usuario]/skillswap.git
cd skillswap
```

### 2. Crear la base de datos

```bash
mysql -u root -p < sql/schema.sql
```

Esto crea la base de datos `skillswap`, todas las tablas, triggers y los datos semilla (usuario admin y 20 materias de ejemplo).

**Credenciales del admin por defecto:**
```
Email:    admin@skillswap.com
Password: password
```

### 3. Configurar la conexión

Editar `config/database.php` con tus credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'skillswap');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Configurar Apache (VirtualHost o XAMPP)

Apuntar el DocumentRoot a la carpeta `skillswap/`. En XAMPP, copiar la carpeta a `htdocs/skillswap/` y acceder desde:

```
http://localhost/skillswap/
```

### 5. Permisos de carpeta de uploads

```bash
chmod 755 public/uploads/certificados/
```

---

## Estructura del Proyecto

```
skillswap/
├── config/
│   └── database.php          # Configuración PDO y constantes
├── public/
│   ├── css/
│   │   └── estilos.css       # Estilos globales
│   └── uploads/
│       └── certificados/     # Certificados subidos por tutores
├── app/
│   ├── controllers/
│   │   ├── AuthController.php        # Login, registro, logout
│   │   ├── TutorAlumnoController.php # Dashboard tutor + alumno
│   │   └── AdminController.php       # Panel admin
│   ├── models/
│   │   ├── Usuario.php               # CRUD usuarios
│   │   ├── Materia.php               # CRUD materias
│   │   ├── Horario.php               # Gestión de disponibilidad
│   │   ├── Solicitud.php             # Flujo de solicitudes
│   │   └── ChatDenunciaBlockchain.php # Chat, denuncias, pagos
│   ├── views/
│   │   ├── auth/     # login, registro, pantalla pendiente
│   │   ├── admin/    # dashboard, usuarios, denuncias, blockchain, materias
│   │   ├── tutor/    # dashboard, perfil, horarios
│   │   ├── alumno/   # dashboard, buscar, ver tutor, solicitud, perfil
│   │   ├── chat/     # sala de chat por solicitud
│   │   └── partials/ # nav, footer
│   └── helpers/
│       ├── Session.php    # Gestión de sesiones segura
│       └── funciones.php  # renderizar(), redirigir(), e(), conectar()
└── index.php         # Front controller / Router
```

---

## Funcionalidades Principales

### 👤 Roles de Usuario

| Rol | Capacidades |
|-----|-------------|
| **Alumno** | Buscar tutores, enviar solicitudes, chatear, calificar, denunciar, convertirse en tutor |
| **Tutor** | Gestionar horarios, aceptar/rechazar solicitudes, chatear, finalizar tutorías |
| **Admin** | Aprobar tutores, bloquear usuarios, resolver denuncias, ver transacciones blockchain, gestionar materias |

### 🔍 Búsqueda con IA

El sistema de recomendación calcula un **score de compatibilidad** entre alumno y tutor considerando:
- Coincidencia de materias que el alumno busca vs. las que el tutor enseña
- Promedio de calificaciones del tutor
- Disponibilidad de horarios activos

### 📅 Gestión de Disponibilidad

- El tutor carga bloques horarios (fecha + hora inicio/fin)
- Un **trigger SQL** (`trg_horario_no_solapamiento`) impide la creación de horarios superpuestos a nivel de base de datos
- Al aceptarse una solicitud, otro **trigger** (`trg_solicitud_aceptada`) marca automáticamente el horario como ocupado

### 💬 Chat Integrado

- Solo disponible entre las partes de una solicitud **aceptada**
- Mensajes sanitizados con `strip_tags()` (solo texto plano)
- Historial persistente en `chat_mensajes`

### ⭐ Sistema de Calificaciones

- El alumno califica al tutor (1-5 estrellas) al finalizar la tutoría
- El promedio de calificaciones se muestra en el perfil del tutor y afecta el score de IA

---

## Arquitectura Técnica

### Patrón MVC

```
index.php (Front Controller)
    └── Router (match/switch)
         ├── AuthController   → vistas auth/
         ├── TutorController  → vistas tutor/
         ├── AlumnoController → vistas alumno/
         └── AdminController  → vistas admin/
```

### Conexión a BD (PDO)

```php
$pdo = new PDO("mysql:host=...", $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
```

Configuración elegida para máxima seguridad: lanza excepciones en error, devuelve arrays asociativos y usa prepared statements nativos (previene second-order SQL injection).

---

## Módulo Blockchain

### Decisión Arquitectónica

En producción real, se utilizaría `web3.php` + **Infura/Alchemy** para firmar y enviar transacciones reales a Sepolia Testnet o una red L2. Para el MVP, se genera un `tx_hash` determinista usando SHA-256 sobre datos únicos de la transacción, lo que simula el comportamiento de un hash de transacción Ethereum real (32 bytes = 64 caracteres hexadecimales).

La arquitectura está preparada para intercambiar la función simulada por una llamada RPC real sin modificar el resto del código.

### Flujo de Pago

```
Tutoría finalizada
    └── BlockchainPago::registrar($solicitudId, $tutorId, $alumnoId)
         ├── Recupera wallet del tutor (o genera una aleatoria si no tiene)
         ├── Genera tx_hash = "0x" . SHA256(datos únicos + microtime + random_bytes)
         └── INSERT INTO blockchain_pagos (solicitud_id, tutor_id, alumno_id,
                  wallet_destino, monto_eth=0.01, tx_hash, red='Sepolia Testnet (simulado)')
```

### Tabla `blockchain_pagos`

| Campo | Descripción |
|-------|-------------|
| `tx_hash` | Hash SHA-256 que simula un tx_hash de Ethereum (64 hex chars) |
| `wallet_destino` | Dirección Ethereum del tutor |
| `monto_eth` | Recompensa simbólica (0.01 ETH por defecto) |
| `red` | Red objetivo (`Sepolia Testnet (simulado)`) |
| `estado` | `pendiente` o `confirmado` |

---

## Módulo de IA

### Sistema de Recomendación (Score Matching)

El algoritmo calcula un puntaje de compatibilidad para ordenar los resultados de búsqueda:

```
score = (materias_coincidentes × 10) + (promedio_calificacion × 5) + (horarios_disponibles × 2)
```

- **Materias coincidentes**: número de materias en común entre lo que el alumno busca y lo que el tutor enseña (factor más importante).
- **Promedio de calificación**: de 1 a 5 estrellas, afecta el ranking.
- **Horarios disponibles**: tutores con más disponibilidad priorizan sobre los que tienen agenda llena.

Esta lógica es extensible a modelos de ML (cosine similarity, collaborative filtering) en una versión de producción con volumen de datos suficiente.

---

## Seguridad Implementada

| Amenaza | Mitigación |
|---------|------------|
| SQL Injection | PDO + prepared statements nativos (`EMULATE_PREPARES=false`) |
| XSS | `htmlspecialchars()` en todas las vistas antes de imprimir datos del usuario |
| Contraseñas | `password_hash()` con bcrypt, verificación con `password_verify()` |
| Session Hijacking | `session_regenerate_id(true)` al loguearse |
| File Upload | Validación de tipo MIME, extensión y tamaño (max 2 MB), nombre renombrado con `uniqid()` |
| Autorización | Verificación de rol en cada controlador con `Session::requireRol()` |
| Acceso a chat | Verificación de que el usuario es parte de la solicitud antes de mostrar mensajes |

---

## Base de Datos

### Diagrama de Tablas

```
materias
usuarios (rol: admin | tutor | alumno)
tutor_materias    ──→ usuarios + materias
alumno_materias   ──→ usuarios + materias
horarios          ──→ usuarios (tutor)
    └── Trigger: trg_horario_no_solapamiento
solicitudes       ──→ usuarios (x2) + horarios + materias
    └── Trigger: trg_solicitud_aceptada
chat_mensajes     ──→ solicitudes + usuarios
denuncias         ──→ usuarios (x2)
blockchain_pagos  ──→ solicitudes + usuarios (x2)
```

### Triggers

**`trg_horario_no_solapamiento`** (BEFORE INSERT en `horarios`):
- Verifica que el nuevo horario no se superponga con ninguno existente del mismo tutor.
- Lanza `SIGNAL SQLSTATE '45000'` si hay solapamiento.

**`trg_solicitud_aceptada`** (AFTER UPDATE en `solicitudes`):
- Al cambiar estado a `aceptada`: marca el horario como `ocupado`.
- Al cambiar a `cancelada` o `rechazada`: libera el horario (`libre`).

---

## Decisiones Arquitectónicas

### 1. MVC sin Framework
Se eligió PHP puro sin frameworks (Laravel, Symfony) para demostrar dominio directo del lenguaje y el patrón arquitectónico, tal como corresponde a la cursada de Teoría de Computación.

### 2. PDO vs. MySQLi
PDO fue elegido por su abstracción de la capa de base de datos y su compatibilidad futura para migrar a otro motor si fuera necesario.

### 3. Blockchain Simulada
La decisión de simular Sepolia en lugar de usar la red real se tomó por las siguientes razones:
- El entorno de desarrollo es local (sin acceso a ETH de prueba en la demo)
- La arquitectura es idéntica a la producción real; solo se debe intercambiar la función de firma
- Permite demostrar el concepto completo sin dependencia de infraestructura externa

### 4. IA por Score Ponderado
Se eligió un algoritmo determinista en lugar de ML por adecuación al MVP: no hay volumen de datos suficiente para entrenar un modelo. El diseño permite reemplazar el score manual por un modelo de colaboración filtrada cuando la plataforma tenga usuarios reales.

---

## Métricas y Pruebas

### Pruebas funcionales realizadas

| Caso de Prueba | Resultado |
|----------------|-----------|
| Registro de alumno con email duplicado | ✅ Error controlado |
| Registro de tutor sin certificado | ✅ Error controlado |
| Login con credenciales incorrectas | ✅ Mensaje de error sin revelar cuál campo falló |
| Creación de horario solapado | ✅ Trigger SQL rechaza la inserción |
| Solicitud con horario ya ocupado | ✅ Validación en modelo Solicitud |
| Chat entre usuario no autorizado | ✅ Redirección con error |
| Finalización de tutoría → pago blockchain | ✅ TX hash generado y registrado |
| Upload de archivo mayor a 2 MB | ✅ Rechazado con mensaje |
| XSS en campo de bio del tutor | ✅ Escapado correctamente en vista |
| SQL Injection en búsqueda | ✅ Neutralizado por prepared statement |

### Métricas de la BD (seed data)
- Tablas: 9
- Triggers: 2
- Materias precargadas: 20 (en 5 áreas)
- Usuario admin: 1

---

## Licencia

Proyecto académico desarrollado para el Examen Final de **Teoría de Computación 2** — Lic. en Sistemas de Información — Universidad Champagnat, Junio 2026.
