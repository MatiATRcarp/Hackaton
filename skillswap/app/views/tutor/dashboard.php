<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Tutor · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <h2 style="font-weight:800;margin-bottom:1.2rem;">
    Hola, <?= e(Session::userName()) ?> 👋
  </h2>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <!-- Stats rápidas -->
  <div class="dash-grid">
    <?php
      $pendientes  = array_filter($solicitudes, fn($s) => $s['estado'] === 'pendiente');
      $aceptadas   = array_filter($solicitudes, fn($s) => $s['estado'] === 'aceptada');
      $finalizadas = array_filter($solicitudes, fn($s) => $s['estado'] === 'finalizada');
      $libres      = array_filter($horarios,    fn($h) => $h['estado'] === 'libre');
    ?>
    <div class="stat-card"><div class="numero"><?= count($pendientes) ?></div><div class="label">Solicitudes pendientes</div></div>
    <div class="stat-card"><div class="numero"><?= count($aceptadas) ?></div><div class="label">Tutorías activas</div></div>
    <div class="stat-card"><div class="numero"><?= count($finalizadas) ?></div><div class="label">Completadas</div></div>
    <div class="stat-card"><div class="numero"><?= count($libres) ?></div><div class="label">Horarios libres</div></div>
  </div>

  <!-- Materias que enseño -->
  <?php if ($materias): ?>
  <div style="margin-bottom:1.2rem;">
    <span style="font-size:.82rem;font-weight:600;color:var(--texto-2);">ENSEÑÁS:</span>
    <div class="chips" style="margin-top:.4rem;">
      <?php foreach ($materias as $m): ?>
        <span class="chip"><?= e($m['nombre']) ?></span>
      <?php endforeach; ?>
    </div>
    <a href="index.php?p=tutor_perfil" style="font-size:.8rem;">Editar materias →</a>
  </div>
  <?php else: ?>
    <div class="alerta alerta-warning">
      ⚠️ Todavía no configuraste tus materias.
      <a href="index.php?p=tutor_perfil">Configurar ahora →</a>
    </div>
  <?php endif; ?>

  <!-- Solicitudes -->
  <div class="card">
    <div class="card-header">
      <h2>📩 Solicitudes de tutoría</h2>
    </div>

    <?php if (empty($solicitudes)): ?>
      <div class="vacio"><p>📭</p><p>No tenés solicitudes todavía.</p></div>
    <?php else: ?>
      <?php foreach ($solicitudes as $s): ?>
        <div class="sol-item">
          <div class="avatar"><?= e(iniciales($s['alumno_nombre'])) ?></div>
          <div class="sol-body">
            <div class="sol-header">
              <h4><?= e($s['alumno_nombre']) ?></h4>
              <span class="badge <?= badgeEstado($s['estado']) ?>"><?= labelEstado($s['estado']) ?></span>
            </div>
            <p style="font-size:.8rem;color:var(--gris-400);">
              📚 <?= e($s['materia_nombre']) ?> ·
              📅 <?= date('d/m/Y', strtotime($s['fecha'])) ?>
              <?= date('H:i', strtotime($s['hora_inicio'])) ?>–<?= date('H:i', strtotime($s['hora_fin'])) ?>
            </p>
            <p style="font-size:.88rem;background:var(--lila-50);padding:.6rem;border-radius:var(--radio-sm);border-left:3px solid var(--lila-300);margin:.5rem 0;">
              <?= nl2br(e($s['mensaje'])) ?>
            </p>
            <div class="sol-acciones">
              <?php if ($s['estado'] === 'pendiente'): ?>
                <form method="POST" action="index.php?p=tutor_responder">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <input type="hidden" name="accion" value="aceptada">
                  <button class="btn btn-success btn-sm">✓ Aceptar</button>
                </form>
                <form method="POST" action="index.php?p=tutor_responder">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <input type="hidden" name="accion" value="rechazada">
                  <button class="btn btn-danger btn-sm">✕ Rechazar</button>
                </form>
              <?php endif; ?>

              <?php if ($s['estado'] === 'aceptada'): ?>
                <a href="index.php?p=tutor_chat&id=<?= $s['id'] ?>" class="btn btn-secondary btn-sm">💬 Chat</a>
                <form method="POST" action="index.php?p=tutor_finalizar">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <button class="btn btn-primary btn-sm">🎓 Finalizar + cobrar</button>
                </form>
              <?php endif; ?>

              <?php if (in_array($s['estado'], ['pendiente','aceptada'])): ?>
                <form method="POST" action="index.php?p=tutor_cancelar">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <button class="btn btn-ghost btn-sm">Cancelar</button>
                </form>
              <?php endif; ?>

              <?php if ($s['estado'] === 'aceptada'): ?>
                <p style="font-size:.82rem;color:var(--texto-2);">
                  📧 <?= e($s['alumno_email']) ?>
                </p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
