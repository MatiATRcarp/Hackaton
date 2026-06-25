<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Solicitar tutoría · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm mt-3">

  <a href="index.php?p=alumno_tutor&id=<?= $tutor['id'] ?>" class="btn btn-ghost btn-sm" style="margin-bottom:1rem;">← Volver</a>

  <div class="card">
    <div class="card-header">
      <h2>Solicitar tutoría</h2>
      <p>Con <strong><?= e($tutor['nombre']) ?></strong></p>
    </div>

    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

    <?php if (empty($horarios)): ?>
      <div class="alerta alerta-warning">
        Este tutor no tiene horarios disponibles. Intentá más tarde.
      </div>
    <?php else: ?>
      <form method="POST" action="index.php?p=alumno_solicitar&id=<?= $tutor['id'] ?>">
        <div class="form-group">
          <label>Materia *</label>
          <select name="materia_id" required>
            <option value="">-- Seleccioná una materia --</option>
            <?php foreach ($todasMaterias as $m): ?>
              <option value="<?= $m['id'] ?>"><?= e($m['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Horario disponible *</label>
          <select name="horario_id" required>
            <option value="">-- Seleccioná un horario --</option>
            <?php foreach ($horarios as $h): ?>
              <option value="<?= $h['id'] ?>">
                <?= date('d/m/Y', strtotime($h['fecha'])) ?>
                <?= date('H:i', strtotime($h['hora_inicio'])) ?> – <?= date('H:i', strtotime($h['hora_fin'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tu mensaje *</label>
          <textarea name="mensaje" rows="4" required
                    placeholder="Explicá qué necesitás, qué temas te cuestan más, o cualquier detalle relevante..."></textarea>
          <span class="form-hint">Mínimo 10 caracteres. El tutor leerá esto antes de aceptar.</span>
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-2">🤝 Enviar solicitud</button>
      </form>
    <?php endif; ?>
  </div>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
