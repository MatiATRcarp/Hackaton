<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chat · SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>

<?php
  $rolActual = Session::userRol();
  $volver    = $rolActual === 'tutor' ? 'tutor' : 'alumno';
?>

<div class="chat-sala">
  <!-- Header -->
  <div style="display:flex;align-items:center;gap:.8rem;padding:.8rem 1rem;background:var(--blanco);border:1.5px solid var(--lila-100);border-radius:var(--radio-lg) var(--radio-lg) 0 0;margin-bottom:0;">
    <a href="index.php?p=<?= $volver ?>" style="color:var(--gris-400);font-size:1.2rem;">←</a>
    <div class="avatar avatar-xs"><?= e(iniciales($otro['nombre'])) ?></div>
    <div>
      <strong style="font-size:.92rem;"><?= e($otro['nombre']) ?></strong>
      <p style="font-size:.75rem;color:var(--gris-400);">
        Solicitud #<?= $sol['id'] ?> · Solo texto permitido
      </p>
    </div>
  </div>

  <!-- Mensajes -->
  <div class="chat-mensajes" id="mensajes">
    <?php if (empty($mensajes)): ?>
      <p style="text-align:center;color:var(--gris-400);font-size:.85rem;margin:auto;">
        Empezá la conversación 👋
      </p>
    <?php else: ?>
      <?php foreach ($mensajes as $m): ?>
        <?php $esPropio = ((int)$m['usuario_id'] === $uid); ?>
        <div class="burbuja-wrap <?= $esPropio ? 'propio' : '' ?>">
          <div class="avatar-xs"><?= e(iniciales($m['autor_nombre'])) ?></div>
          <div>
            <div class="burbuja"><?= nl2br(e($m['mensaje'])) ?></div>
            <div class="burbuja-meta <?= $esPropio ? '' : '' ?>">
              <?= e($m['autor_nombre']) ?> · <?= date('d/m H:i', strtotime($m['created_at'])) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- Input -->
  <?php
    $ruta = $rolActual === 'tutor' ? 'tutor_chat' : 'alumno_chat';
  ?>
  <form method="POST" action="index.php?p=<?= $ruta ?>&id=<?= $sol['id'] ?>" class="chat-input">
    <input type="text" name="mensaje" placeholder="Escribí un mensaje de texto..."
           required maxlength="500" autocomplete="off">
    <button type="submit" class="btn btn-primary btn-sm">Enviar</button>
  </form>
</div>

<script>
  // Scroll al final al cargar
  const c = document.getElementById('mensajes');
  if (c) c.scrollTop = c.scrollHeight;
</script>

<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
