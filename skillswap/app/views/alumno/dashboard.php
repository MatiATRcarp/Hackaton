<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis tutorías · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <h2 style="font-weight:800;margin-bottom:1.2rem;">Hola, <?= e(Session::userName()) ?> 👋</h2>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <div style="display:flex;gap:.8rem;margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="index.php?p=alumno_buscar" class="btn btn-primary">🔍 Buscar tutor</a>
    <a href="index.php?p=alumno_perfil" class="btn btn-secondary">✏️ Mi perfil</a>
    <a href="index.php?p=alumno_convertir" class="btn btn-ghost btn-sm">Convertirme en tutor</a>
  </div>

  <?php if (empty($solicitudes)): ?>
    <div class="vacio"><p>📚</p><p>Todavía no enviaste ninguna solicitud.<br>
      <a href="index.php?p=alumno_buscar">Buscá un tutor →</a></p>
    </div>
  <?php else: ?>
    <?php foreach ($solicitudes as $s): ?>
      <div class="sol-item">
        <div class="avatar"><?= e(iniciales($s['tutor_nombre'])) ?></div>
        <div class="sol-body">
          <div class="sol-header">
            <h4><?= e($s['tutor_nombre']) ?></h4>
            <span class="badge <?= badgeEstado($s['estado']) ?>"><?= labelEstado($s['estado']) ?></span>
          </div>
          <p style="font-size:.8rem;color:var(--gris-400);">
            📚 <?= e($s['materia_nombre']) ?> ·
            📅 <?= date('d/m/Y', strtotime($s['fecha'])) ?>
            <?= date('H:i', strtotime($s['hora_inicio'])) ?>–<?= date('H:i', strtotime($s['hora_fin'])) ?>
          </p>
          <p style="font-size:.88rem;background:var(--lila-50);padding:.5rem;border-radius:var(--radio-sm);border-left:3px solid var(--lila-300);margin:.4rem 0;">
            <?= nl2br(e($s['mensaje'])) ?>
          </p>

          <div class="sol-acciones">
            <?php if ($s['estado'] === 'aceptada'): ?>
              <a href="index.php?p=alumno_chat&id=<?= $s['id'] ?>" class="btn btn-primary btn-sm">💬 Ir al chat</a>
              <p style="font-size:.82rem;color:var(--texto-2);">📧 <?= e($s['tutor_email']) ?></p>
            <?php endif; ?>

            <?php if (in_array($s['estado'], ['pendiente', 'aceptada'])): ?>
              <form method="POST" action="index.php?p=alumno_cancelar">
                <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                <button class="btn btn-ghost btn-sm">Cancelar</button>
              </form>
            <?php endif; ?>

            <?php if ($s['estado'] === 'finalizada' && $s['calificacion'] === null): ?>
              <form method="POST" action="index.php?p=alumno_calificar" style="display:flex;gap:.4rem;align-items:center;">
                <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                <select name="calificacion" style="border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.3rem .5rem;font-size:.85rem;">
                  <option value="5">⭐⭐⭐⭐⭐</option>
                  <option value="4">⭐⭐⭐⭐</option>
                  <option value="3">⭐⭐⭐</option>
                  <option value="2">⭐⭐</option>
                  <option value="1">⭐</option>
                </select>
                <input type="text" name="comentario" placeholder="Comentario (opcional)"
                       style="border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.3rem .6rem;font-size:.85rem;">
                <button class="btn btn-warning btn-sm">⭐ Calificar</button>
              </form>
            <?php elseif ($s['estado'] === 'finalizada' && $s['calificacion'] !== null): ?>
              <span class="estrellas"><?= estrellas((int)$s['calificacion']) ?></span>
              <?php if ($s['comentario_cal']): ?>
                <span style="font-size:.82rem;color:var(--texto-2);"><?= e($s['comentario_cal']) ?></span>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($s['estado'] === 'finalizada'): ?>
              <?php $pago = (new BlockchainPago())->deSolicitud((int)$s['id']); ?>
              <?php if ($pago): ?>
                <div class="tx-card" style="font-size:.7rem;width:100%;margin-top:.4rem;">
                  <span>⛓ Pago registrado:</span> <?= e(substr($pago['tx_hash'],0,30)) ?>…
                  (<?= e($pago['monto_eth']) ?> ETH · <?= e($pago['red']) ?>)
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <!-- Denunciar tutor -->
            <?php if (in_array($s['estado'], ['aceptada','finalizada'])): ?>
              <details style="margin-top:.3rem;">
                <summary style="font-size:.78rem;color:var(--rojo);cursor:pointer;">Denunciar tutor</summary>
                <form method="POST" action="index.php?p=alumno_denunciar" style="display:flex;gap:.4rem;margin-top:.5rem;">
                  <input type="hidden" name="tutor_id" value="<?= $s['tutor_id'] ?>">
                  <input type="text" name="motivo" placeholder="Motivo de la denuncia (mín. 10 caracteres)" required
                         style="flex:1;border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.4rem .7rem;font-size:.85rem;">
                  <button class="btn btn-danger btn-sm">Denunciar</button>
                </form>
              </details>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
