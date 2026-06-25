<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi perfil · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-md mt-3">

  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;">
    <div class="avatar avatar-lg"><?= e(iniciales($usuario['nombre'])) ?></div>
    <div>
      <h2 style="font-weight:800;"><?= e($usuario['nombre']) ?></h2>
      <p style="color:var(--texto-2);"><?= e($usuario['carrera']) ?> · <?= e($usuario['universidad']) ?></p>
    </div>
  </div>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

  <!-- Datos personales -->
  <div class="card mb-2">
    <div class="card-header"><h2>Datos personales</h2></div>
    <form method="POST" action="index.php?p=tutor_perfil">
      <input type="hidden" name="accion" value="perfil">
      <div class="form-group">
        <label>Nombre completo *</label>
        <input type="text" name="nombre" value="<?= e($usuario['nombre']) ?>" required>
      </div>
      <div class="form-group">
        <label>Carrera</label>
        <input type="text" name="carrera" value="<?= e($usuario['carrera']) ?>">
      </div>
      <div class="form-group">
        <label>Año</label>
        <select name="anio">
          <?php for ($i = 1; $i <= 6; $i++): ?>
            <option value="<?= $i ?>" <?= $usuario['anio_cursado'] == $i ? 'selected' : '' ?>><?= $i ?>° año</option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Universidad</label>
        <input type="text" name="universidad" value="<?= e($usuario['universidad']) ?>">
      </div>
      <div class="form-group">
        <label>Bio</label>
        <textarea name="bio" placeholder="Contá algo sobre vos..."><?= e($usuario['bio'] ?? '') ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
  </div>

  <!-- Materias -->
  <div class="card">
    <div class="card-header">
      <h2>Materias que enseño</h2>
      <p>Marcá las materias sobre las que podés dar tutoría</p>
    </div>
    <form method="POST" action="index.php?p=tutor_perfil">
      <input type="hidden" name="accion" value="materias">
      <?php foreach ($todasMaterias as $area => $mats): ?>
        <p style="font-weight:700;font-size:.82rem;color:var(--lila-600);margin:.8rem 0 .4rem;"><?= e($area) ?></p>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.3rem;">
          <?php foreach ($mats as $m): ?>
            <label style="display:flex;align-items:center;gap:.5rem;font-size:.88rem;font-weight:400;cursor:pointer;">
              <input type="checkbox" name="materias[]" value="<?= $m['id'] ?>"
                     <?= in_array($m['id'], $misIds) ? 'checked' : '' ?>
                     style="accent-color:var(--lila-500);">
              <?= e($m['nombre']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-primary mt-2">Guardar materias</button>
    </form>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
