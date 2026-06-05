<?php
require_once 'sesion.php';
require_once 'db.php';

requerirLogin();

$pdo = conectar();
$uid = usuarioId();

$exito = '';
$error = '';

// ── Acción: aceptar o rechazar una solicitud recibida ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['solicitud_id'])) {
    $sol_id = (int)$_POST['solicitud_id'];
    $accion  = $_POST['accion'];

    if (in_array($accion, ['aceptada', 'rechazada'])) {
        // Solo puede responder el destinatario
        $stmt = $pdo->prepare('
            UPDATE solicitudes
            SET estado = ?
            WHERE id = ? AND para_usuario_id = ?
        ');
        $stmt->execute([$accion, $sol_id, $uid]);

        $exito = $accion === 'aceptada'
            ? '¡Intercambio aceptado! Ya pueden coordinar por email.'
            : 'Solicitud rechazada.';
    }
}

// ── Solicitudes recibidas ─────────────────────────
$stmt = $pdo->prepare('
    SELECT s.id, s.mensaje, s.estado, s.created_at,
           u.id AS remitente_id, u.nombre AS remitente_nombre, u.carrera AS remitente_carrera, u.email AS remitente_email
    FROM solicitudes s
    JOIN usuarios u ON u.id = s.de_usuario_id
    WHERE s.para_usuario_id = ?
    ORDER BY s.created_at DESC
');
$stmt->execute([$uid]);
$recibidas = $stmt->fetchAll();

// ── Solicitudes enviadas ──────────────────────────
$stmt = $pdo->prepare('
    SELECT s.id, s.mensaje, s.estado, s.created_at,
           u.id AS destino_id, u.nombre AS destino_nombre, u.carrera AS destino_carrera, u.email AS destino_email
    FROM solicitudes s
    JOIN usuarios u ON u.id = s.para_usuario_id
    WHERE s.de_usuario_id = ?
    ORDER BY s.created_at DESC
');
$stmt->execute([$uid]);
$enviadas = $stmt->fetchAll();

// ── Tab activo ────────────────────────────────────
$tab = $_GET['tab'] ?? 'recibidas';

// ── Mensaje de éxito desde redirect ──────────────
$msg = $_GET['msg'] ?? '';
if ($msg === 'enviada') $exito = 'Solicitud enviada correctamente. Esperá la respuesta del otro estudiante.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis solicitudes · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php">Explorar</a>
    <a href="mis_solicitudes.php" class="activo">Mis solicitudes</a>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" class="btn-nav">Salir</a>
  </div>
</nav>

<div class="contenedor-md">

  <div class="seccion-titulo mt-3">
    <h2>Mis solicitudes</h2>
  </div>

  <?php if ($exito): ?>
    <div class="alerta alerta-exito"><?= e($exito) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alerta alerta-error"><?= e($error) ?></div>
  <?php endif; ?>

  <!-- ── Tabs ── -->
  <div class="tabs">
    <a href="?tab=recibidas"
       class="tab <?= $tab === 'recibidas' ? 'activo' : '' ?>">
      Recibidas
      <?php if (count($recibidas) > 0): ?>
        <span style="background:var(--lila-200);color:var(--lila-600);border-radius:20px;padding:1px 7px;font-size:0.75rem;margin-left:4px;">
          <?= count($recibidas) ?>
        </span>
      <?php endif; ?>
    </a>
    <a href="?tab=enviadas"
       class="tab <?= $tab === 'enviadas' ? 'activo' : '' ?>">
      Enviadas
      <?php if (count($enviadas) > 0): ?>
        <span style="background:var(--lila-200);color:var(--lila-600);border-radius:20px;padding:1px 7px;font-size:0.75rem;margin-left:4px;">
          <?= count($enviadas) ?>
        </span>
      <?php endif; ?>
    </a>
  </div>

  <!-- ── RECIBIDAS ── -->
  <?php if ($tab === 'recibidas'): ?>
    <?php if (empty($recibidas)): ?>
      <div class="vacio">
        <p style="font-size:2rem;">📭</p>
        <p>Todavía no recibiste ninguna solicitud.<br>
           <a href="perfil.php" style="color:var(--lila-600);">Completá tu perfil</a> para que otros te encuentren.</p>
      </div>
    <?php else: ?>
      <?php foreach ($recibidas as $s): ?>
        <div class="solicitud-item">
          <div class="avatar"><?= e(iniciales($s['remitente_nombre'])) ?></div>
          <div class="solicitud-body">
            <div class="flex-between">
              <h4><?= e($s['remitente_nombre']) ?></h4>
              <span class="badge badge-<?= $s['estado'] ?>"><?= ucfirst($s['estado']) ?></span>
            </div>
            <p style="color:var(--gris-400);font-size:0.8rem;">
              <?= e($s['remitente_carrera'] ?: 'Carrera no indicada') ?>
              &middot; <?= date('d/m/Y', strtotime($s['created_at'])) ?>
            </p>

            <p style="margin-top:8px;font-size:0.9rem;color:var(--texto);background:var(--lila-50);padding:10px 12px;border-radius:8px;border-left:3px solid var(--lila-300);">
              <?= nl2br(e($s['mensaje'])) ?>
            </p>

            <?php if ($s['estado'] === 'aceptada'): ?>
              <p style="margin-top:8px;font-size:0.88rem;color:var(--texto-2);">
                📧 Contactalo/a:
                <a href="mailto:<?= e($s['remitente_email']) ?>"
                   style="color:var(--lila-600);font-weight:600;">
                  <?= e($s['remitente_email']) ?>
                </a>
              </p>
            <?php endif; ?>

            <?php if ($s['estado'] === 'pendiente'): ?>
              <div class="solicitud-acciones">
                <form method="POST">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <input type="hidden" name="accion" value="aceptada">
                  <button type="submit" class="btn btn-primary btn-sm">✓ Aceptar</button>
                </form>
                <form method="POST">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <input type="hidden" name="accion" value="rechazada">
                  <button type="submit" class="btn btn-danger btn-sm">✕ Rechazar</button>
                </form>
                <a href="ver_perfil.php?id=<?= $s['remitente_id'] ?>"
                   class="btn btn-ghost btn-sm">Ver perfil</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  <!-- ── ENVIADAS ── -->
  <?php else: ?>
    <?php if (empty($enviadas)): ?>
      <div class="vacio">
        <p style="font-size:2rem;">📤</p>
        <p>Todavía no enviaste ninguna solicitud.<br>
           <a href="explorar.php" style="color:var(--lila-600);">Explorá estudiantes</a> y proponé un intercambio.</p>
      </div>
    <?php else: ?>
      <?php foreach ($enviadas as $s): ?>
        <div class="solicitud-item">
          <div class="avatar"><?= e(iniciales($s['destino_nombre'])) ?></div>
          <div class="solicitud-body">
            <div class="flex-between">
              <h4>Para: <?= e($s['destino_nombre']) ?></h4>
              <span class="badge badge-<?= $s['estado'] ?>"><?= ucfirst($s['estado']) ?></span>
            </div>
            <p style="color:var(--gris-400);font-size:0.8rem;">
              <?= e($s['destino_carrera'] ?: 'Carrera no indicada') ?>
              &middot; <?= date('d/m/Y', strtotime($s['created_at'])) ?>
            </p>

            <p style="margin-top:8px;font-size:0.9rem;color:var(--texto);background:var(--lila-50);padding:10px 12px;border-radius:8px;border-left:3px solid var(--lila-300);">
              <?= nl2br(e($s['mensaje'])) ?>
            </p>

            <?php if ($s['estado'] === 'aceptada'): ?>
              <p style="margin-top:8px;font-size:0.88rem;color:var(--texto-2);">
                📧 Contactalo/a:
                <a href="mailto:<?= e($s['destino_email']) ?>"
                   style="color:var(--lila-600);font-weight:600;">
                  <?= e($s['destino_email']) ?>
                </a>
              </p>
            <?php endif; ?>

            <div class="solicitud-acciones" style="margin-top:8px;">
              <a href="ver_perfil.php?id=<?= $s['destino_id'] ?>"
                 class="btn btn-ghost btn-sm">Ver perfil</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endif; ?>

</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
