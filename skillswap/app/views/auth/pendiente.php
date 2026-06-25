<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendiente · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor-sm" style="padding-top:3rem;text-align:center;">
  <div class="card">
    <p style="font-size:3rem;margin-bottom:1rem;">⏳</p>
    <h2 style="font-size:1.3rem;font-weight:700;margin-bottom:.5rem;">Cuenta pendiente de aprobación</h2>
    <p style="color:var(--texto-2);font-size:.95rem;">
      El administrador está revisando tu certificado de alumno regular.<br>
      Recibirás acceso completo una vez que sea aprobado.
    </p>
    <div class="mt-3">
      <a href="index.php?p=logout" class="btn btn-ghost btn-sm">Cerrar sesión</a>
    </div>
  </div>
</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
