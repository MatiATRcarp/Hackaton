<?php
require_once 'sesion.php';
require_once 'db.php';

redirigirSiLogueado();

$error = '';

// Mensajes de contexto
$msg = $_GET['msg'] ?? '';
$info = match($msg) {
    'sesion'   => 'Necesitás iniciar sesión para acceder a esa página.',
    'registro' => '¡Registro exitoso! Ya podés iniciar sesión.',
    'logout'   => 'Cerraste sesión correctamente.',
    default    => ''
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Completá todos los campos.';
    } else {
        $pdo  = conectar();
        $stmt = $pdo->prepare('SELECT id, nombre, password FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            header('Location: perfil.php');
            exit;
        } else {
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="registro.php" class="btn-nav">Registrarse</a>
  </div>
</nav>

<div class="contenedor-sm">
  <div class="card mt-3">
    <div class="card-header">
      <h2>Bienvenido de nuevo</h2>
      <p>Ingresá con tu cuenta para continuar</p>
    </div>

    <?php if ($info): ?>
      <div class="alerta alerta-info"><?= e($info) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email"
               value="<?= e($_POST['email'] ?? '') ?>"
               placeholder="tu@email.com" required>
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password"
               placeholder="Tu contraseña" required>
      </div>

      <button type="submit" class="btn btn-primary btn-block mt-2">
        Iniciar sesión
      </button>
    </form>

    <hr class="divider">

    <p class="text-center text-muted">
      ¿No tenés cuenta?
      <a href="registro.php" style="color:var(--lila-600);font-weight:600;">Registrate gratis</a>
    </p>
  </div>
</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
