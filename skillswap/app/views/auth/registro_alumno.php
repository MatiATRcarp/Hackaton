<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro alumno · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm">
  <div class="card mt-3">
    <div class="card-header">
      <h2>Crear cuenta como alumno</h2>
      <p>Buscá tutores para las materias que necesitás</p>
    </div>
    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="index.php?p=registro_alumno">
      <div class="form-group">
        <label>Nombre completo *</label>
        <input type="text" name="nombre" value="<?= e($datos['nombre'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Correo electrónico *</label>
        <input type="email" name="email" value="<?= e($datos['email'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Carrera *</label>
        <input type="text" name="carrera" value="<?= e($datos['carrera'] ?? '') ?>"
               placeholder="Ej: Ingeniería en Sistemas" required>
      </div>
      <div class="form-group">
        <label>Año de cursado *</label>
        <select name="anio_cursado">
          <?php for ($i = 1; $i <= 6; $i++): ?>
            <option value="<?= $i ?>" <?= ($datos['anio_cursado'] ?? 1) == $i ? 'selected' : '' ?>>
              <?= $i ?>° año
            </option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Universidad / Instituto *</label>
        <input type="text" name="universidad" value="<?= e($datos['universidad'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Contraseña * (mín. 6 caracteres)</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div class="form-group">
        <label>Repetir contraseña *</label>
        <input type="password" name="password2" required autocomplete="new-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block mt-2">Crear cuenta</button>
    </form>

    <p class="mt-2" style="text-align:center;font-size:.88rem;color:var(--texto-2);">
      ¿Querés enseñar? <a href="index.php?p=registro_tutor">Registrarte como tutor</a>
    </p>
  </div>
</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
