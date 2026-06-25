<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SkillSwap · Tutorías entre pares</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<main>
  <div class="hero">
    <div class="hero-badge">🎓 Plataforma estudiantil</div>
    <h1>Aprendé de quienes<br><em>ya lo cursaron</em></h1>
    <p>Conectate con tutores estudiantes. Recibí ayuda personalizada en las materias que necesitás, con pago transparente en Blockchain.</p>
    <div class="hero-btns">
      <?php if ($logueado): ?>
        <a href="index.php?p=alumno_buscar" class="btn btn-primary">Buscar tutor →</a>
      <?php else: ?>
        <a href="index.php?p=registro_alumno" class="btn btn-primary">Empezar gratis →</a>
        <a href="index.php?p=registro_tutor"  class="btn btn-secondary">Ser tutor</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="contenedor">
    <div class="features">
      <div class="feature-card">
        <div class="feature-icon">🔍</div>
        <h3>Encontrá a tu tutor</h3>
        <p>Filtrá por materia y encontrá tutores que ya cursaron lo que necesitás.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">📅</div>
        <h3>Elegí el horario</h3>
        <p>Cada tutor publica su disponibilidad. Elegís el horario que te quede bien.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">💬</div>
        <h3>Chat integrado</h3>
        <p>Una vez aceptada la tutoría, coordinan los detalles dentro de la plataforma.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">⛓</div>
        <h3>Pago en Blockchain</h3>
        <p>Al finalizar la tutoría, el tutor recibe un pago registrado en Ethereum Testnet.</p>
      </div>
    </div>
  </div>
</main>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
