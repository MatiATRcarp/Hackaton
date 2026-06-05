<?php
// ── Configuración de la base de datos ──────────────
// Cambiá estos valores según tu entorno local

define('DB_HOST', 'localhost');
define('DB_NAME', 'intercambio_habilidades');
define('DB_USER', 'root');       // usuario de MySQL (en XAMPP suele ser root)
define('DB_PASS', '');           // contraseña (en XAMPP suele estar vacía)
define('DB_CHARSET', 'utf8mb4');

function conectar(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opciones);
        } catch (PDOException $e) {
            // En producción nunca mostrar detalles del error
            die('<div style="font-family:sans-serif;padding:2rem;color:#b91c1c;background:#fee2e2;border-radius:10px;margin:2rem auto;max-width:500px;">
                <strong>Error de conexión a la base de datos.</strong><br>
                Verificá que MySQL esté activo y que las credenciales en db.php sean correctas.<br><br>
                <small>' . htmlspecialchars($e->getMessage()) . '</small>
            </div>');
        }
    }

    return $pdo;
}
