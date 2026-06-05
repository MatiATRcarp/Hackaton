<?php
require_once 'sesion.php';
$logueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkillSwap · Intercambiá lo que sabés</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php">Explorar</a>
    <?php if ($logueado): ?>
      <a href="mis_solicitudes.php">Mis solicitudes</a>
      <a href="perfil.php">Mi perfil</a>
      <a href="logout.php" class="btn-nav">Salir</a>
    <?php else: ?>
      <a href="login.php">Iniciar sesión</a>
      <a href="registro.php" class="btn-nav">Registrarse</a>
    <?php endif; ?>
  </div>
</nav>

<main>
  <div class="hero">
    <div class="hero-badge">Comunidad estudiantil</div>
    <h1>Intercambiá lo que sabés,<br><em>aprendé lo que necesitás</em></h1>
    <p>Conectate con otros estudiantes. Enseñá lo que dominás y aprendé lo que te falta, sin dinero de por medio.</p>
    <div class="hero-btns">
      <?php if ($logueado): ?>
        <a href="explorar.php" class="btn btn-primary">Ver estudiantes →</a>
        <a href="perfil.php" class="btn btn-secondary">Mi perfil</a>
      <?php else: ?>
        <a href="registro.php" class="btn btn-primary">Empezar gratis →</a>
        <a href="explorar.php" class="btn btn-secondary">Explorar</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="contenedor">
    <div class="features">
      <div class="feature-card">
        <div class="feature-icon">📚</div>
        <h3>Publicá lo que sabés</h3>
        <p>Indicá qué habilidades podés enseñar: materias, idiomas, música, programación y mucho más.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🔍</div>
        <h3>Encontrá lo que buscás</h3>
        <p>Explorá perfiles de estudiantes filtrados por lo que necesitás aprender.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">🤝</div>
        <h3>Proponé un intercambio</h3>
        <p>Mandá una solicitud explicando qué ofrecés a cambio. Sin plata, solo conocimiento.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">✨</div>
        <h3>100% entre pares</h3>
        <p>Una comunidad horizontal donde todos tienen algo valioso para aportar.</p>
      </div>
    </div>
  </div>
</main>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
