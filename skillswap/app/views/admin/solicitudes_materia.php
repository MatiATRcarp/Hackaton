<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitudes de materias · Admin SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2 style="font-weight:800;">📋 Solicitudes de nuevas materias</h2>
    <a href="index.php?p=admin" class="btn btn-ghost btn-sm">← Volver</a>
  </div>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <?php if (empty($solicitudes)): ?>
    <div class="vacio"><p>📭</p><p>No hay solicitudes de materias todavía.</p></div>
  <?php else: ?>

    <?php
      $pendientes = array_filter($solicitudes, fn($s) => $s['estado'] === 'pendiente');
      $resueltas  = array_filter($solicitudes, fn($s) => $s['estado'] !== 'pendiente');
    ?>

    <?php if ($pendientes): ?>
    <div class="card" style="margin-bottom:1.5rem;">
      <div class="card-header">
        <h2>⏳ Pendientes de revisión (<?= count($pendientes) ?>)</h2>
      </div>
      <div class="tabla-wrap">
        <table>
          <thead>
            <tr>
              <th>Solicitante</th>
              <th>Rol</th>
              <th>Materia propuesta</th>
              <th>Área</th>
              <th>Motivo</th>
              <th>Fecha</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pendientes as $s): ?>
            <tr>
              <td><strong><?= e($s['usuario_nombre']) ?></strong></td>
              <td>
                <span class="badge <?= $s['usuario_rol'] === 'tutor' ? 'badge-primary' : 'badge-secondary' ?>">
                  <?= ucfirst(e($s['usuario_rol'])) ?>
                </span>
              </td>
              <td><?= e($s['nombre']) ?></td>
              <td><?= e($s['area']) ?></td>
              <td style="font-size:.85rem;color:var(--texto-2);max-width:220px;">
                <?= $s['motivo'] ? e($s['motivo']) : '<em style="color:var(--gris-400)">Sin motivo</em>' ?>
              </td>
              <td style="font-size:.82rem;white-space:nowrap;"><?= fechaHora($s['created_at']) ?></td>
              <td>
                <form method="POST" action="index.php?p=admin_revisar_materia" style="display:flex;flex-direction:column;gap:.4rem;min-width:220px;">
                  <input type="hidden" name="solicitud_id" value="<?= $s['id'] ?>">
                  <input type="text" name="nota" placeholder="Nota al usuario (opcional)"
                         style="border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.35rem .6rem;font-size:.82rem;">
                  <div style="display:flex;gap:.4rem;">
                    <button name="accion" value="aprobar" class="btn btn-success btn-sm" style="flex:1;">✓ Aprobar</button>
                    <button name="accion" value="rechazar" class="btn btn-danger btn-sm" style="flex:1;">✕ Rechazar</button>
                  </div>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($resueltas): ?>
    <div class="card">
      <div class="card-header">
        <h2>📁 Historial de solicitudes resueltas</h2>
      </div>
      <div class="tabla-wrap">
        <table>
          <thead>
            <tr>
              <th>Solicitante</th>
              <th>Rol</th>
              <th>Materia</th>
              <th>Área</th>
              <th>Estado</th>
              <th>Nota admin</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($resueltas as $s): ?>
            <tr>
              <td><?= e($s['usuario_nombre']) ?></td>
              <td>
                <span class="badge <?= $s['usuario_rol'] === 'tutor' ? 'badge-primary' : 'badge-secondary' ?>">
                  <?= ucfirst(e($s['usuario_rol'])) ?>
                </span>
              </td>
              <td><?= e($s['nombre']) ?></td>
              <td><?= e($s['area']) ?></td>
              <td>
                <span class="badge <?= $s['estado'] === 'aprobada' ? 'badge-success' : 'badge-danger' ?>">
                  <?= $s['estado'] === 'aprobada' ? '✓ Aprobada' : '✕ Rechazada' ?>
                </span>
              </td>
              <td style="font-size:.82rem;color:var(--texto-2);"><?= e($s['admin_nota'] ?? '') ?></td>
              <td style="font-size:.82rem;white-space:nowrap;"><?= fechaHora($s['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

  <?php endif; ?>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
