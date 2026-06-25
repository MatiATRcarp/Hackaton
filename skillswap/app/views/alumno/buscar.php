<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buscar tutor · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <h2 style="font-weight:800;margin-bottom:1rem;">🔍 Buscar tutor</h2>

  <!-- Filtro -->
  <form method="GET" action="index.php" class="filtros">
    <input type="hidden" name="p" value="alumno_buscar">
    <div class="form-group">
      <label>Filtrar por materia</label>
      <select name="materia">
        <option value="0">Todas las materias</option>
        <?php foreach ($todasMaterias as $m): ?>
          <option value="<?= $m['id'] ?>" <?= $filtroMateria == $m['id'] ? 'selected' : '' ?>>
            <?= e($m['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrar</button>
    <?php if ($filtroMateria > 0): ?>
      <a href="index.php?p=alumno_buscar" class="btn btn-ghost">Limpiar</a>
    <?php endif; ?>
  </form>

  <p style="font-size:.85rem;color:var(--texto-2);margin-bottom:.8rem;">
    <?= count($tutores) ?> tutor<?= count($tutores) !== 1 ? 'es' : '' ?> encontrado<?= count($tutores) !== 1 ? 's' : '' ?>
  </p>

  <?php if (empty($tutores)): ?>
    <div class="vacio"><p>🔍</p><p>No hay tutores disponibles para ese criterio.</p></div>
  <?php else: ?>
    <div class="grilla">
      <?php foreach ($tutores as $t): ?>
        <div class="tarjeta-tutor">
          <div style="display:flex;align-items:center;gap:.8rem;">
            <div class="avatar"><?= e(iniciales($t['nombre'])) ?></div>
            <div>
              <h3 style="font-weight:700;font-size:.97rem;"><?= e($t['nombre']) ?></h3>
              <p style="font-size:.8rem;color:var(--gris-400);"><?= e($t['carrera']) ?> · <?= e($t['universidad']) ?></p>
            </div>
          </div>
          <?php if (!empty($t['bio'])): ?>
            <p style="font-size:.83rem;color:var(--texto-2);"><?= e(mb_strimwidth($t['bio'],0,100,'…')) ?></p>
          <?php endif; ?>
          <?php if ($t['materias']): ?>
            <div class="chips">
              <?php foreach (array_slice($t['materias'], 0, 4) as $m): ?>
                <span class="chip"><?= e($m['nombre']) ?></span>
              <?php endforeach; ?>
              <?php if (count($t['materias']) > 4): ?>
                <span class="chip" style="background:var(--gris-100);color:var(--gris-400);">+<?= count($t['materias'])-4 ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <a href="index.php?p=alumno_tutor&id=<?= $t['id'] ?>" class="btn btn-secondary btn-sm" style="margin-top:auto;">
            Ver perfil →
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
