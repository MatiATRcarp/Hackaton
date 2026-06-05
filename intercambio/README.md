# SkillSwap — Guía de instalación

## Requisitos
- XAMPP (o WAMP) con Apache y MySQL activos
- PHP 7.4 o superior (XAMPP lo incluye)

---

## Pasos para instalar

### 1. Copiar el proyecto
Copiá la carpeta `intercambio` dentro de:
```
C:\xampp\htdocs\intercambio\     (Windows)
/Applications/XAMPP/htdocs/intercambio/  (Mac)
```

### 2. Crear la base de datos

**Opción A — phpMyAdmin:**
1. Abrí el navegador y entrá a `http://localhost/phpmyadmin`
2. Hacé clic en "Nueva" (panel izquierdo)
3. Nombre: `intercambio_habilidades` → Crear
4. Seleccioná la base creada → pestaña "SQL"
5. Pegá todo el contenido de `base_de_datos.sql` → Ejecutar

**Opción B — DBeaver:**
1. Conectate a tu MySQL local
2. Clic derecho en la conexión → "Ejecutar script SQL"
3. Abrí el archivo `base_de_datos.sql` → Ejecutar

### 3. Configurar la conexión (si es necesario)
Abrí `db.php` y verificá estos valores:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'intercambio_habilidades');
define('DB_USER', 'root');   // en XAMPP suele ser 'root'
define('DB_PASS', '');        // en XAMPP suele estar vacío
```

### 4. Abrir la app
Entrá a: `http://localhost/intercambio/`

---

## Usuarios de prueba (contraseña: `password`)
| Nombre            | Email           |
|-------------------|-----------------|
| Valentina López   | valen@test.com  |
| Mateo García      | mateo@test.com  |
| Lucía Fernández   | lucia@test.com  |

---

## Estructura de archivos
```
intercambio/
├── db.php              ← Conexión a MySQL
├── sesion.php          ← Funciones de sesión y utilidades
├── index.php           ← Página de inicio
├── login.php           ← Iniciar sesión
├── registro.php        ← Crear cuenta
├── logout.php          ← Cerrar sesión
├── perfil.php          ← Ver y editar mi perfil y habilidades
├── explorar.php        ← Buscar estudiantes
├── ver_perfil.php      ← Perfil público de otro usuario
├── solicitud.php       ← Enviar solicitud de intercambio
├── mis_solicitudes.php ← Ver solicitudes recibidas y enviadas
├── base_de_datos.sql   ← Script SQL para crear todo
└── css/
    └── estilos.css     ← Todos los estilos
```
