<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mis horarios · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-md mt-3">

  <h2 style="font-weight:800;margin-bottom:1.2rem;">📅 Mis horarios</h2>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <!-- Agregar horario -->
  <div class="card">
    <div class="card-header"><h2>Agregar disponibilidad</h2></div>
    <form method="POST" action="index.php?p=tutor_horarios" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="accion" value="crear">
      <div class="form-group" style="flex:1;min-width:140px;margin:0;">
        <label>Fecha *</label>
        <input type="date" name="fecha" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
      </div>
      <div class="form-group" style="flex:1;min-width:120px;margin:0;">
        <label>Hora inicio *</label>
        <input type="time" name="hora_inicio" required>
      </div>
      <div class="form-group" style="flex:1;min-width:120px;margin:0;">
        <label>Hora fin *</label>
        <input type="time" name="hora_fin" required>
      </div>
      <button type="submit" class="btn btn-primary">Agregar</button>
    </form>
    <p style="font-size:.78rem;color:var(--gris-400);margin-top:.5rem;">
      ⚠️ Los horarios no pueden solaparse el mismo día. El sistema lo detecta automáticamente.
    </p>
  </div>

  <!-- Lista de horarios -->
  <div class="card">
    <div class="card-header"><h2>Tus horarios cargados</h2></div>
    <?php if (empty($horarios)): ?>
      <div class="vacio" style="padding:1.5rem;"><p>📭</p><p>No tenés horarios cargados todavía.</p></div>
    <?php else: ?>
      <div class="horario-lista">
        <?php foreach ($horarios as $h): ?>
          <div class="horario-item <?= $h['estado'] === 'ocupado' ? 'ocupado' : '' ?>">
            <div>
              <strong><?= date('d/m/Y', strtotime($h['fecha'])) ?></strong>
              <span style="color:var(--texto-2);font-size:.88rem;margin-left:.5rem;">
                <?= date('H:i', strtotime($h['hora_inicio'])) ?> – <?= date('H:i', strtotime($h['hora_fin'])) ?>
              </span>
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;">
              <span class="badge <?= $h['estado']==='libre' ? 'badge-success' : 'badge-danger' ?>">
                <?= $h['estado'] === 'libre' ? '🟢 Libre' : '🔴 Reservado' ?>
              </span>
              <?php if ($h['estado'] === 'libre'): ?>
                <form method="POST" action="index.php?p=tutor_horarios">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="horario_id" value="<?= $h['id'] ?>">
                  <button class="btn btn-ghost btn-sm">✕</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
