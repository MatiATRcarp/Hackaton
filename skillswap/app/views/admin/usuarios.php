<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuarios · Admin SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2 style="font-weight:800;">Gestión de usuarios</h2>
    <a href="index.php?p=admin" class="btn btn-ghost btn-sm">← Volver</a>
  </div>

  <?php if ($exito = Session::getFlash('exito')): ?>
    <div class="alerta alerta-exito"><?= e($exito) ?></div>
  <?php endif; ?>

  <!-- Filtros -->
  <form method="GET" action="index.php" class="filtros">
    <input type="hidden" name="p" value="admin_usuarios">
    <div class="form-group">
      <label>Rol</label>
      <select name="rol">
        <option value="">Todos</option>
        <option value="alumno" <?= $rol==='alumno' ? 'selected' : '' ?>>Alumno</option>
        <option value="tutor"  <?= $rol==='tutor'  ? 'selected' : '' ?>>Tutor</option>
      </select>
    </div>
    <div class="form-group">
      <label>Estado</label>
      <select name="estado">
        <option value="">Todos</option>
        <option value="activo"    <?= $estado==='activo'    ? 'selected' : '' ?>>Activo</option>
        <option value="pendiente" <?= $estado==='pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="bloqueado" <?= $estado==='bloqueado' ? 'selected' : '' ?>>Bloqueado</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrar</button>
  </form>

  <div class="tabla-wrap">
    <table>
      <thead>
        <tr>
          <th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th>
          <th>Carrera</th><th>Universidad</th><th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
          <td><strong><?= e($u['nombre']) ?></strong></td>
          <td style="font-size:.82rem;"><?= e($u['email']) ?></td>
          <td><span class="badge badge-info"><?= e($u['rol']) ?></span></td>
          <td>
            <span class="badge <?= $u['estado']==='activo' ? 'badge-success' : ($u['estado']==='bloqueado' ? 'badge-danger' : 'badge-warning') ?>">
              <?= e($u['estado']) ?>
            </span>
          </td>
          <td style="font-size:.82rem;"><?= e($u['carrera']) ?> (<?= $u['anio_cursado'] ?>°)</td>
          <td style="font-size:.82rem;"><?= e($u['universidad']) ?></td>
          <td>
            <div style="display:flex;gap:.3rem;">
              <?php if ($u['estado'] !== 'bloqueado'): ?>
                <form method="POST" action="index.php?p=admin_bloquear">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="btn btn-danger btn-sm">Bloquear</button>
                </form>
              <?php else: ?>
                <form method="POST" action="index.php?p=admin_desbloquear">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button class="btn btn-success btn-sm">Desbloquear</button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
