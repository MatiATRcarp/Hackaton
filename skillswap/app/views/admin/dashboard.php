<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <h2 style="font-weight:800;margin-bottom:1.2rem;">Panel de administración</h2>

  <?php if ($exito = Session::getFlash('exito')): ?>
    <div class="alerta alerta-exito"><?= e($exito) ?></div>
  <?php endif; ?>

  <!-- Stats -->
  <div class="dash-grid">
    <div class="stat-card">
      <div class="numero"><?= count($tutoresPendientes) ?></div>
      <div class="label">Tutores pendientes</div>
    </div>
    <div class="stat-card">
      <div class="numero"><?= count($denunciasPendientes) ?></div>
      <div class="label">Denuncias pendientes</div>
    </div>
    <div class="stat-card">
      <div class="numero"><?= count($solicitudesMateriasPend) ?></div>
      <div class="label">Solicitudes de materias</div>
    </div>
    <div class="stat-card">
      <div class="numero"><?= count($ultimosPagos) ?></div>
      <div class="label">Pagos blockchain</div>
    </div>
  </div>

  <!-- Tutores pendientes -->
  <?php if ($tutoresPendientes): ?>
  <div class="card">
    <div class="card-header">
      <h2>⏳ Tutores pendientes de aprobación</h2>
    </div>
    <div class="tabla-wrap">
      <table>
        <thead>
          <tr>
            <th>Nombre</th><th>Email</th><th>Carrera</th><th>Universidad</th><th>Certificado</th><th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tutoresPendientes as $t): ?>
          <tr>
            <td><strong><?= e($t['nombre']) ?></strong></td>
            <td><?= e($t['email']) ?></td>
            <td><?= e($t['carrera']) ?> (<?= $t['anio_cursado'] ?>°)</td>
            <td><?= e($t['universidad']) ?></td>
            <td>
              <?php if ($t['certificado_path']): ?>
                <a href="<?= e(BASE_URL . '/public/' . $t['certificado_path']) ?>"
                   target="_blank" class="btn btn-ghost btn-sm">📄 Ver</a>
              <?php else: ?>
                <span style="color:var(--gris-400)">No subió</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex;gap:.4rem;">
                <form method="POST" action="index.php?p=admin_aprobar">
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button class="btn btn-success btn-sm">✓ Aprobar</button>
                </form>
                <form method="POST" action="index.php?p=admin_rechazar">
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button class="btn btn-danger btn-sm">✕ Rechazar</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <!-- Denuncias pendientes -->
  <?php if ($denunciasPendientes): ?>
  <div class="card">
    <div class="card-header">
      <h2>🚨 Denuncias pendientes</h2>
    </div>
    <?php foreach ($denunciasPendientes as $d): ?>
      <div style="border:1px solid var(--gris-200);border-radius:var(--radio-sm);padding:.8rem;margin-bottom:.6rem;">
        <div style="display:flex;justify-content:space-between;margin-bottom:.4rem;">
          <strong><?= e($d['denunciante_nombre']) ?></strong> denuncia a
          <strong style="color:var(--rojo);"><?= e($d['denunciado_nombre']) ?></strong>
          <span class="badge badge-danger">Pendiente</span>
        </div>
        <p style="font-size:.88rem;color:var(--texto-2);margin-bottom:.7rem;"><?= e($d['motivo']) ?></p>
        <form method="POST" action="index.php?p=admin_resolver" style="display:flex;gap:.5rem;flex-wrap:wrap;">
          <input type="hidden" name="denuncia_id"  value="<?= $d['id'] ?>">
          <input type="hidden" name="denunciado_id" value="<?= $d['denunciado_id'] ?>">
          <input type="text"   name="resolucion" placeholder="Resolución..." style="flex:1;min-width:180px;border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.4rem .7rem;">
          <button name="accion" value="resolver"  class="btn btn-secondary btn-sm">Resolver</button>
          <button name="accion" value="bloquear"  class="btn btn-danger btn-sm">Bloquear usuario</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- Solicitudes de materias pendientes -->
  <?php if ($solicitudesMateriasPend): ?>
  <div class="card">
    <div class="card-header">
      <h2>📋 Solicitudes de nuevas materias pendientes</h2>
    </div>
    <div class="tabla-wrap">
      <table>
        <thead>
          <tr><th>Solicitante</th><th>Rol</th><th>Materia propuesta</th><th>Área</th><th>Motivo</th><th>Acción</th></tr>
        </thead>
        <tbody>
          <?php foreach ($solicitudesMateriasPend as $sm): ?>
          <tr>
            <td><strong><?= e($sm['usuario_nombre']) ?></strong></td>
            <td>
              <span class="badge <?= $sm['usuario_rol'] === 'tutor' ? 'badge-primary' : 'badge-secondary' ?>">
                <?= ucfirst(e($sm['usuario_rol'])) ?>
              </span>
            </td>
            <td><?= e($sm['nombre']) ?></td>
            <td><?= e($sm['area']) ?></td>
            <td style="font-size:.82rem;color:var(--texto-2);">
              <?= $sm['motivo'] ? e($sm['motivo']) : '<em style="color:var(--gris-400)">—</em>' ?>
            </td>
            <td>
              <form method="POST" action="index.php?p=admin_revisar_materia" style="display:flex;gap:.4rem;flex-wrap:wrap;">
                <input type="hidden" name="solicitud_id" value="<?= $sm['id'] ?>">
                <input type="text" name="nota" placeholder="Nota (opcional)"
                       style="border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.3rem .5rem;font-size:.8rem;min-width:130px;">
                <button name="accion" value="aprobar"  class="btn btn-success btn-sm">✓ Aprobar</button>
                <button name="accion" value="rechazar" class="btn btn-danger btn-sm">✕ Rechazar</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div style="padding:.8rem 0 0;font-size:.82rem;">
      <a href="index.php?p=admin_solicitudes_materia">Ver todas las solicitudes →</a>
    </div>
  </div>
  <?php endif; ?>

  <div style="display:flex;gap:.8rem;flex-wrap:wrap;margin-top:.5rem;">
    <a href="index.php?p=admin_usuarios"             class="btn btn-secondary">👥 Gestionar usuarios</a>
    <a href="index.php?p=admin_materias"             class="btn btn-secondary">📚 Materias</a>
    <a href="index.php?p=admin_solicitudes_materia"  class="btn btn-secondary">📋 Solicitudes de materias</a>
    <a href="index.php?p=admin_denuncias"            class="btn btn-secondary">🚨 Todas las denuncias</a>
    <a href="index.php?p=admin_blockchain"           class="btn btn-secondary">⛓ Pagos blockchain</a>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
