<?php
require_once 'sesion.php';
require_once 'db.php';

requerirLogin();

$pdo = conectar();
$uid = usuarioId();

$id_destino = (int)($_GET['id'] ?? 0);

if ($id_destino === 0 || $id_destino === $uid) {
    header('Location: explorar.php');
    exit;
}

// ── Verificar que el destinatario existe ──────────
$stmt = $pdo->prepare('SELECT id, nombre, carrera FROM usuarios WHERE id = ?');
$stmt->execute([$id_destino]);
$destino = $stmt->fetch();

if (!$destino) {
    header('Location: explorar.php');
    exit;
}

// ── Verificar que no exista ya una solicitud ──────
$stmt = $pdo->prepare('
    SELECT id FROM solicitudes
    WHERE (de_usuario_id = ? AND para_usuario_id = ?)
       OR (de_usuario_id = ? AND para_usuario_id = ?)
    LIMIT 1
');
$stmt->execute([$uid, $id_destino, $id_destino, $uid]);

if ($stmt->fetch()) {
    header('Location: ver_perfil.php?id=' . $id_destino);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($mensaje)) {
        $error = 'Escribí un mensaje explicando qué proponés a cambio.';
    } elseif (strlen($mensaje) < 20) {
        $error = 'El mensaje es muy corto. Explicá mejor tu propuesta.';
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO solicitudes (de_usuario_id, para_usuario_id, mensaje)
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$uid, $id_destino, $mensaje]);

        header('Location: mis_solicitudes.php?msg=enviada');
        exit;
    }
}

// ── Mis habilidades para mostrar en el formulario ─
$stmt = $pdo->prepare('
    SELECT h.nombre, uh.tipo
    FROM usuario_habilidades uh
    JOIN habilidades h ON h.id = uh.habilidad_id
    WHERE uh.usuario_id = ?
');
$stmt->execute([$uid]);
$mis_skills = $stmt->fetchAll();
$mis_ofrece = array_filter($mis_skills, fn($h) => $h['tipo'] === 'ofrece');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Proponer intercambio · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php">Explorar</a>
    <a href="mis_solicitudes.php">Mis solicitudes</a>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" class="btn-nav">Salir</a>
  </div>
</nav>

<div class="contenedor-sm">

  <a href="ver_perfil.php?id=<?= $id_destino ?>" class="btn btn-ghost btn-sm mt-3" style="margin-bottom:1.2rem;">
    ← Volver al perfil
  </a>

  <div class="card">
    <div class="card-header">
      <h2>Proponer intercambio</h2>
      <p>
        Enviando solicitud a
        <strong style="color:var(--lila-600);"><?= e($destino['nombre']) ?></strong>
        <?php if ($destino['carrera']): ?>
          &mdash; <?= e($destino['carrera']) ?>
        <?php endif; ?>
      </p>
    </div>

    <?php if ($mis_ofrece): ?>
      <div class="alerta alerta-info" style="margin-bottom:1.2rem;">
        <div>
          <strong>Lo que vos podés ofrecer:</strong>
          <div class="chips-lista" style="margin-top:6px;">
            <?php foreach ($mis_ofrece as $h): ?>
              <span class="chip chip-ofrece"><?= e($h['nombre']) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="alerta alerta-info">
        Todavía no cargaste tus habilidades.
        <a href="perfil.php" style="color:var(--lila-600);font-weight:600;">Completá tu perfil →</a>
      </div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="mensaje">Tu propuesta</label>
        <textarea id="mensaje" name="mensaje" rows="5"
                  placeholder="Ej: Hola! Vi que buscás ayuda con Cálculo. Yo puedo explicarte los temas de la unidad 3 a cambio de que me enseñes inglés técnico. ¿Te parece?"
                  required><?= e($_POST['mensaje'] ?? '') ?></textarea>
        <span class="form-hint">Sé claro/a en lo que ofrecés y lo que pedís a cambio. ¡El intercambio es entre pares!</span>
      </div>

      <button type="submit" class="btn btn-primary btn-block mt-2">
        🤝 Enviar solicitud
      </button>
    </form>
  </div>

</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
