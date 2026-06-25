<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Materias · Admin SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2 style="font-weight:800;">📚 Gestión de materias</h2>
    <a href="index.php?p=admin" class="btn btn-ghost btn-sm">← Volver</a>
  </div>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <div class="card">
    <div class="card-header"><h2>Agregar materia</h2></div>
    <form method="POST" action="index.php?p=admin_materias" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
      <input type="hidden" name="accion" value="crear">
      <div class="form-group" style="flex:2;margin:0;">
        <label>Nombre *</label>
        <input type="text" name="nombre" placeholder="Ej: Análisis Matemático I" required>
      </div>
      <div class="form-group" style="flex:1;margin:0;">
        <label>Área *</label>
        <input type="text" name="area" placeholder="Ej: Matemática" required>
      </div>
      <button type="submit" class="btn btn-primary">Agregar</button>
    </form>
  </div>

  <div class="tabla-wrap">
    <table>
      <thead><tr><th>ID</th><th>Nombre</th><th>Área</th><th>Estado</th><th>Acción</th></tr></thead>
      <tbody>
        <?php foreach ($materias as $m): ?>
        <tr>
          <td><?= $m['id'] ?></td>
          <td><strong><?= e($m['nombre']) ?></strong></td>
          <td><?= e($m['area']) ?></td>
          <td>
            <span class="badge <?= $m['activa'] ? 'badge-success' : 'badge-secondary' ?>">
              <?= $m['activa'] ? 'Activa' : 'Inactiva' ?>
            </span>
          </td>
          <td>
            <?php if ($m['activa']): ?>
              <form method="POST" action="index.php?p=admin_materias">
                <input type="hidden" name="accion" value="desactivar">
                <input type="hidden" name="materia_id" value="<?= $m['id'] ?>">
                <button class="btn btn-ghost btn-sm">Desactivar</button>
              </form>
            <?php endif; ?>
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
