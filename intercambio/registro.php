<?php
require_once 'sesion.php';
require_once 'db.php';

redirigirSiLogueado();

$error  = '';
$datos  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre'   => trim($_POST['nombre']   ?? ''),
        'email'    => trim($_POST['email']    ?? ''),
        'carrera'  => trim($_POST['carrera']  ?? ''),
        'password' => $_POST['password']      ?? '',
        'password2'=> $_POST['password2']     ?? '',
    ];

    // Validaciones
    if (empty($datos['nombre']) || empty($datos['email']) || empty($datos['password'])) {
        $error = 'Completá todos los campos obligatorios.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif (strlen($datos['password']) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($datos['password'] !== $datos['password2']) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo = conectar();

        // Verificar si el email ya existe
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$datos['email']]);

        if ($stmt->fetch()) {
            $error = 'Ya existe una cuenta con ese correo electrónico.';
        } else {
            $hash = password_hash($datos['password'], PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('
                INSERT INTO usuarios (nombre, email, password, carrera)
                VALUES (?, ?, ?, ?)
            ');
            $stmt->execute([
                $datos['nombre'],
                $datos['email'],
                $hash,
                $datos['carrera'],
            ]);

            header('Location: login.php?msg=registro');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="login.php" class="btn-nav">Iniciar sesión</a>
  </div>
</nav>

<div class="contenedor-sm">
  <div class="card mt-3">
    <div class="card-header">
      <h2>Crear cuenta</h2>
      <p>Unite a la comunidad de intercambio estudiantil</p>
    </div>

    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="registro.php">
      <div class="form-group">
        <label for="nombre">Nombre completo <span style="color:var(--lila-500)">*</span></label>
        <input type="text" id="nombre" name="nombre"
               value="<?= e($datos['nombre'] ?? '') ?>"
               placeholder="Tu nombre y apellido" required>
      </div>

      <div class="form-group">
        <label for="email">Correo electrónico <span style="color:var(--lila-500)">*</span></label>
        <input type="email" id="email" name="email"
               value="<?= e($datos['email'] ?? '') ?>"
               placeholder="tu@email.com" required>
      </div>

      <div class="form-group">
        <label for="carrera">Carrera / facultad</label>
        <input type="text" id="carrera" name="carrera"
               value="<?= e($datos['carrera'] ?? '') ?>"
               placeholder="Ej: Ingeniería en Sistemas, 2do año">
        <span class="form-hint">Opcional, pero ayuda a los demás a conocerte</span>
      </div>

      <div class="form-group">
        <label for="password">Contraseña <span style="color:var(--lila-500)">*</span></label>
        <input type="password" id="password" name="password"
               placeholder="Mínimo 6 caracteres" required>
      </div>

      <div class="form-group">
        <label for="password2">Repetir contraseña <span style="color:var(--lila-500)">*</span></label>
        <input type="password" id="password2" name="password2"
               placeholder="Repetí tu contraseña" required>
      </div>

      <button type="submit" class="btn btn-primary btn-block mt-2">
        Crear cuenta
      </button>
    </form>

    <hr class="divider">

    <p class="text-center text-muted">
      ¿Ya tenés cuenta?
      <a href="login.php" style="color:var(--lila-600);font-weight:600;">Iniciá sesión</a>
    </p>
  </div>
</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
