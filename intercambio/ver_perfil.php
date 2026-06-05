<?php
require_once 'sesion.php';
require_once 'db.php';

requerirLogin();

$pdo = conectar();
$uid = usuarioId();

$id_perfil = (int)($_GET['id'] ?? 0);

if ($id_perfil === 0 || $id_perfil === $uid) {
    header('Location: explorar.php');
    exit;
}

// ── Datos del usuario a ver ───────────────────────
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$id_perfil]);
$perfil = $stmt->fetch();

if (!$perfil) {
    header('Location: explorar.php');
    exit;
}

// ── Habilidades del perfil ────────────────────────
$stmt = $pdo->prepare('
    SELECT h.nombre, uh.tipo
    FROM usuario_habilidades uh
    JOIN habilidades h ON h.id = uh.habilidad_id
    WHERE uh.usuario_id = ?
    ORDER BY uh.tipo, h.nombre
');
$stmt->execute([$id_perfil]);
$habilidades = $stmt->fetchAll();
$ofrece = array_filter($habilidades, fn($h) => $h['tipo'] === 'ofrece');
$busca  = array_filter($habilidades, fn($h) => $h['tipo'] === 'busca');

// ── ¿Ya existe una solicitud entre ambos? ─────────
$stmt = $pdo->prepare('
    SELECT id, estado FROM solicitudes
    WHERE (de_usuario_id = ? AND para_usuario_id = ?)
       OR (de_usuario_id = ? AND para_usuario_id = ?)
    ORDER BY created_at DESC
    LIMIT 1
');
$stmt->execute([$uid, $id_perfil, $id_perfil, $uid]);
$solicitud_existente = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($perfil['nombre']) ?> · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php">Explorar</a>
    <a href="mis_solicitudes.php">Mis solicitudes</a>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" class="btn-nav">Salir</a>
  </div>
</nav>

<div class="contenedor-md">

  <a href="explorar.php" class="btn btn-ghost btn-sm mt-3" style="margin-bottom:1.2rem;">
    ← Volver a explorar
  </a>

  <div class="card">

    <!-- ── Encabezado del perfil ── -->
    <div class="perfil-header">
      <div class="avatar avatar-lg"><?= e(iniciales($perfil['nombre'])) ?></div>
      <div>
        <h2 style="font-size:1.4rem;font-weight:600;"><?= e($perfil['nombre']) ?></h2>
        <p class="text-muted"><?= e($perfil['carrera'] ?: 'Carrera no indicada') ?></p>
      </div>
    </div>

    <?php if ($perfil['bio']): ?>
      <p style="font-size:0.93rem;color:var(--texto-2);line-height:1.7;margin-bottom:1rem;">
        <?= nl2br(e($perfil['bio'])) ?>
      </p>
    <?php endif; ?>

    <hr class="divider">

    <!-- ── Habilidades ── -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
      <div>
        <p class="chip-label">🟣 Puede enseñar</p>
        <div class="chips-lista">
          <?php if ($ofrece): ?>
            <?php foreach ($ofrece as $h): ?>
              <span class="chip chip-ofrece"><?= e($h['nombre']) ?></span>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="text-muted" style="font-size:0.83rem;">No indicó habilidades</span>
          <?php endif; ?>
        </div>
      </div>
      <div>
        <p class="chip-label">🟠 Quiere aprender</p>
        <div class="chips-lista">
          <?php if ($busca): ?>
            <?php foreach ($busca as $h): ?>
              <span class="chip chip-busca"><?= e($h['nombre']) ?></span>
            <?php endforeach; ?>
          <?php else: ?>
            <span class="text-muted" style="font-size:0.83rem;">No indicó habilidades</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <hr class="divider">

    <!-- ── Botón de solicitud ── -->
    <?php if ($solicitud_existente): ?>
      <?php
        $labels = [
          'pendiente' => '⏳ Solicitud enviada — pendiente de respuesta',
          'aceptada'  => '✅ ¡Intercambio aceptado!',
          'rechazada' => '❌ La solicitud fue rechazada',
        ];
        $clase = [
          'pendiente' => 'alerta-info',
          'aceptada'  => 'alerta-exito',
          'rechazada' => 'alerta-error',
        ][$solicitud_existente['estado']];
      ?>
      <div class="alerta <?= $clase ?>">
        <?= $labels[$solicitud_existente['estado']] ?>
      </div>

      <?php if ($solicitud_existente['estado'] === 'aceptada'): ?>
        <p style="font-size:0.9rem;color:var(--texto-2);margin-top:0.5rem;">
          Podés contactar a <strong><?= e($perfil['nombre']) ?></strong> por correo:
          <a href="mailto:<?= e($perfil['email']) ?>" style="color:var(--lila-600);font-weight:600;">
            <?= e($perfil['email']) ?>
          </a>
        </p>
      <?php endif; ?>

    <?php else: ?>
      <a href="solicitud.php?id=<?= $id_perfil ?>" class="btn btn-primary">
        🤝 Proponer intercambio
      </a>
    <?php endif; ?>

  </div>

</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
