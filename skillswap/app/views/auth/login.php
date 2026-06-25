<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm">
  <div class="card mt-3">
    <div class="card-header">
      <h2>Bienvenido de nuevo</h2>
      <p>Ingresá con tu cuenta para continuar</p>
    </div>

    <?php if ($info): ?><div class="alerta alerta-info"><?= e($info) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alerta alerta-error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" action="index.php?p=login">
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>"
               placeholder="tu@email.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password"
               placeholder="Tu contraseña" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block mt-2">Iniciar sesión</button>
    </form>

    <p class="mt-2" style="text-align:center;font-size:.88rem;color:var(--texto-2);">
      ¿No tenés cuenta?
      <a href="index.php?p=registro_alumno">Registrarme como alumno</a> ·
      <a href="index.php?p=registro_tutor">Ser tutor</a>
    </p>
  </div>
</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
