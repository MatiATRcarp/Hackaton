<?php
// ── Helpers globales ──────────────────────────────────────────

/** Escapa HTML. */
function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Iniciales para avatar (máx 2). */
function iniciales(string $nombre): string {
    $partes = explode(' ', trim($nombre));
    $ini    = '';
    foreach (array_slice($partes, 0, 2) as $p) {
        $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $ini ?: '?';
}

/** Redirige a una ruta interna. */
function redirigir(string $p, array $extra = []): never {
    $params = array_merge(['p' => $p], $extra);
    header('Location: ' . BASE_URL . '/index.php?' . http_build_query($params));
    exit;
}

/** Renderiza una vista. */
function renderizar(string $vista, array $datos = []): void {
    extract($datos, EXTR_SKIP);
    $path = BASE_PATH . '/app/views/' . $vista . '.php';
    if (!file_exists($path)) {
        die("Vista no encontrada: $vista");
    }
    require $path;
}

/** Formatea fecha/hora legible. */
function fechaHora(string $dt): string {
    return date('d/m/Y H:i', strtotime($dt));
}

/** Estrellas HTML. */
function estrellas(int $n): string {
    return str_repeat('★', $n) . str_repeat('☆', 5 - $n);
}

/** Clase CSS para estado de solicitud. */
function badgeEstado(string $estado): string {
    return match($estado) {
        'pendiente'  => 'badge-warning',
        'aceptada'   => 'badge-success',
        'rechazada'  => 'badge-danger',
        'cancelada'  => 'badge-secondary',
        'finalizada' => 'badge-info',
        default      => '',
    };
}

/** Label legible para estado. */
function labelEstado(string $estado): string {
    return match($estado) {
        'pendiente'  => '⏳ Pendiente',
        'aceptada'   => '✅ Aceptada',
        'rechazada'  => '❌ Rechazada',
        'cancelada'  => '🚫 Cancelada',
        'finalizada' => '🎓 Finalizada',
        default      => $estado,
    };
}
