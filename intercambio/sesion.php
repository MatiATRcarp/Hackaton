<?php
// ── Gestión de sesiones ────────────────────────────

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirige al login si el usuario no está logueado.
 * Usá esta función al inicio de páginas protegidas.
 */
function requerirLogin(): void {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php?msg=sesion');
        exit;
    }
}

/**
 * Redirige al perfil si el usuario ya está logueado.
 * Usá en login.php y registro.php.
 */
function redirigirSiLogueado(): void {
    if (isset($_SESSION['usuario_id'])) {
        header('Location: perfil.php');
        exit;
    }
}

/**
 * Devuelve el id del usuario logueado, o null si no hay sesión.
 */
function usuarioId(): ?int {
    return isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
}

/**
 * Devuelve el nombre del usuario logueado.
 */
function usuarioNombre(): string {
    return $_SESSION['usuario_nombre'] ?? '';
}

/**
 * Devuelve las iniciales del nombre para el avatar.
 */
function iniciales(string $nombre): string {
    $partes = explode(' ', trim($nombre));
    $ini = strtoupper(substr($partes[0], 0, 1));
    if (isset($partes[1])) $ini .= strtoupper(substr($partes[1], 0, 1));
    return $ini;
}

/**
 * Escapa HTML para evitar XSS.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
