<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro tutor · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm">
  <div class="card mt-3">
    <div class="card-header">
      <h2>Registrarme como tutor</h2>
      <p>Tu cuenta queda pendiente hasta que el admin revise tu certificado</p>
    </div>
    <div class="alerta alerta-info">
      ⚠️ El admin revisará tu certificado antes de activar tu cuenta como tutor.
    </div>
    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="index.php?p=registro_tutor" enctype="multipart/form-data">
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
        <label>Bio / Presentación</label>
        <textarea name="bio" placeholder="Contá algo sobre vos y tu experiencia académica..."><?= e($datos['bio'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Certificado de alumno regular * <span style="color:var(--gris-400)">(PDF, JPG o PNG, máx. 10MB)</span></label>
        <input type="file" name="certificado" accept=".pdf,.jpg,.jpeg,.png" required>
        <span class="form-hint">Este documento confirma que sos alumno activo.</span>
      </div>
      <div class="form-group">
        <label>Wallet Ethereum (opcional)</label>
        <input type="text" name="wallet" value="<?= e($datos['wallet'] ?? '') ?>"
               placeholder="0x... (para recibir pagos simulados)">
        <span class="form-hint">Si no tenés, se generará una dirección de prueba automáticamente.</span>
      </div>
      <div class="form-group">
        <label>Contraseña * (mín. 6 caracteres)</label>
        <input type="password" name="password" required autocomplete="new-password">
      </div>
      <div class="form-group">
        <label>Repetir contraseña *</label>
        <input type="password" name="password2" required autocomplete="new-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block mt-2">Enviar solicitud de tutor</button>
    </form>

    <p class="mt-2" style="text-align:center;font-size:.88rem;color:var(--texto-2);">
      ¿Solo querés tomar tutorías? <a href="index.php?p=registro_alumno">Registrarte como alumno</a>
    </p>
  </div>
</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
