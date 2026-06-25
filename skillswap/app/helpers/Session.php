<?php
/**
 * Session — wrapper estático para manejar la sesión PHP.
 */
class Session {

    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $k, mixed $v): void { $_SESSION[$k] = $v; }
    public static function get(string $k, mixed $default = null): mixed {
        return $_SESSION[$k] ?? $default;
    }
    public static function has(string $k): bool { return isset($_SESSION[$k]); }

    public static function isLoggedIn(): bool  { return isset($_SESSION['usuario_id']); }
    public static function userId(): int        { return (int)($_SESSION['usuario_id'] ?? 0); }
    public static function userRol(): string    { return $_SESSION['usuario_rol'] ?? ''; }
    public static function userName(): string   { return $_SESSION['usuario_nombre'] ?? ''; }

    /** Persiste los datos básicos de sesión. */
    public static function login(array $u): void {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = $u['id'];
        $_SESSION['usuario_nombre'] = $u['nombre'];
        $_SESSION['usuario_rol']    = $u['rol'];
        $_SESSION['usuario_estado'] = $u['estado'];
    }

    /** Mensaje flash (persiste un solo request). */
    public static function flash(string $k, string $v): void {
        $_SESSION['_flash'][$k] = $v;
    }
    public static function getFlash(string $k): string {
        $v = $_SESSION['_flash'][$k] ?? '';
        unset($_SESSION['_flash'][$k]);
        return $v;
    }

    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    /** Requiere login; si no, redirige. */
    public static function requireLogin(): void {
        if (!self::isLoggedIn()) redirigir('login', ['msg' => 'sesion']);
    }

    /** Requiere rol específico. */
    public static function requireRol(string ...$roles): void {
        self::requireLogin();
        if (!in_array(self::userRol(), $roles)) redirigir('inicio');
    }

    /** ¿El usuario actual está bloqueado? */
    public static function isBloqueado(): bool {
        return ($_SESSION['usuario_estado'] ?? '') === 'bloqueado';
    }
}
