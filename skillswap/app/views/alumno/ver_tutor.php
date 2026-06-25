<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tutor['nombre']) ?> · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-md mt-3">

  <a href="index.php?p=alumno_buscar" class="btn btn-ghost btn-sm" style="margin-bottom:1rem;">← Volver</a>

  <div class="card">
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
      <div class="avatar avatar-lg"><?= e(iniciales($tutor['nombre'])) ?></div>
      <div>
        <h2 style="font-size:1.3rem;font-weight:700;"><?= e($tutor['nombre']) ?></h2>
        <p style="color:var(--gris-400);font-size:.88rem;">
          <?= e($tutor['carrera']) ?> · <?= $tutor['anio_cursado'] ?>° año · <?= e($tutor['universidad']) ?>
        </p>
      </div>
    </div>

    <?php if ($tutor['bio']): ?>
      <p style="font-size:.92rem;color:var(--texto-2);line-height:1.7;margin-bottom:1rem;">
        <?= nl2br(e($tutor['bio'])) ?>
      </p>
    <?php endif; ?>

    <p style="font-size:.82rem;font-weight:700;color:var(--texto-2);margin-bottom:.5rem;">MATERIAS QUE ENSEÑA</p>
    <div class="chips" style="margin-bottom:1.2rem;">
      <?php foreach ($materias as $m): ?>
        <span class="chip"><?= e($m['nombre']) ?></span>
      <?php endforeach; ?>
    </div>

    <hr style="border:none;border-top:1.5px solid var(--lila-100);margin-bottom:1.2rem;">

    <!-- Horarios disponibles -->
    <p style="font-size:.82rem;font-weight:700;color:var(--texto-2);margin-bottom:.5rem;">DISPONIBILIDAD</p>
    <?php if (empty($horarios)): ?>
      <p style="font-size:.88rem;color:var(--gris-400);">Este tutor no tiene horarios disponibles por ahora.</p>
    <?php else: ?>
      <div class="horario-lista" style="margin-bottom:1rem;">
        <?php foreach ($horarios as $h): ?>
          <div class="horario-item">
            <span>
              📅 <strong><?= date('d/m/Y', strtotime($h['fecha'])) ?></strong>
              · <?= date('H:i', strtotime($h['hora_inicio'])) ?> – <?= date('H:i', strtotime($h['hora_fin'])) ?>
            </span>
            <span class="badge badge-success">🟢 Disponible</span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if (Session::isLoggedIn()): ?>
      <a href="index.php?p=alumno_solicitar&id=<?= $tutor['id'] ?>" class="btn btn-primary">
        🤝 Solicitar tutoría
      </a>
    <?php else: ?>
      <a href="index.php?p=login" class="btn btn-primary">Iniciar sesión para solicitar</a>
    <?php endif; ?>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
