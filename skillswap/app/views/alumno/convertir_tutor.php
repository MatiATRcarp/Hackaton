<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ser tutor · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm mt-3">
  <div class="card">
    <div class="card-header">
      <h2>Quiero ser tutor</h2>
      <p>Completá el formulario para solicitar convertirte en tutor</p>
    </div>
    <div class="alerta alerta-info">El admin revisará tu certificado antes de activar tu cuenta de tutor.</div>
    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="index.php?p=alumno_convertir" enctype="multipart/form-data">
      <div class="form-group"><label>Carrera *</label>
        <input type="text" name="carrera" value="<?= e($datos['carrera'] ?? '') ?>" required></div>
      <div class="form-group"><label>Año de cursado *</label>
        <select name="anio">
          <?php for ($i = 1; $i <= 6; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?>° año</option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group"><label>Universidad *</label>
        <input type="text" name="universidad" value="<?= e($datos['universidad'] ?? '') ?>" required></div>
      <div class="form-group"><label>Bio / Experiencia</label>
        <textarea name="bio" placeholder="Contá por qué querés ser tutor..."><?= e($datos['bio'] ?? '') ?></textarea></div>
      <div class="form-group">
        <label>Certificado de alumno regular * (PDF/JPG/PNG, máx. 2MB)</label>
        <input type="file" name="certificado" accept=".pdf,.jpg,.jpeg,.png" required>
      </div>
      <div class="form-group">
        <label>Wallet Ethereum (opcional)</label>
        <input type="text" name="wallet" value="<?= e($datos['wallet'] ?? '') ?>" placeholder="0x...">
        <span class="form-hint">Para recibir pagos al finalizar tutorías.</span>
      </div>
      <button type="submit" class="btn btn-primary btn-block mt-2">Enviar solicitud</button>
    </form>
  </div>
</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
