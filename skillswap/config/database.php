<?php
// ── Configuración de base de datos ───────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'skillswap');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

// ── Rutas ─────────────────────────────────────────────────────
define('BASE_PATH',   dirname(__DIR__));
define('BASE_URL',    'http://localhost/skillswap');
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/certificados');

function conectar(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        die('<div style="padding:2rem;color:#b91c1c;font-family:sans-serif;">
            <strong>Error de conexión:</strong> ' . htmlspecialchars($e->getMessage()) . '
        </div>');
    }
    return $pdo;
}
