<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Denuncias · Admin SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2 style="font-weight:800;">🚨 Denuncias</h2>
    <a href="index.php?p=admin" class="btn btn-ghost btn-sm">← Volver</a>
  </div>

  <?php if ($exito): ?><div class="alerta alerta-exito"><?= e($exito) ?></div><?php endif; ?>

  <?php if (empty($denuncias)): ?>
    <div class="vacio"><p>🎉</p><p>No hay denuncias registradas.</p></div>
  <?php else: ?>
    <?php foreach ($denuncias as $d): ?>
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.5rem;">
          <div>
            <span class="badge badge-danger"><?= e($d['estado']) ?></span>
            <strong style="margin-left:.5rem;"><?= e($d['denunciante_nombre']) ?></strong>
            denuncia a
            <strong style="color:var(--rojo);"><?= e($d['denunciado_nombre']) ?></strong>
          </div>
          <span style="font-size:.78rem;color:var(--gris-400);"><?= fechaHora($d['created_at']) ?></span>
        </div>
        <p style="font-size:.9rem;background:var(--gris-100);padding:.6rem;border-radius:var(--radio-sm);margin-bottom:.7rem;">
          <?= e($d['motivo']) ?>
        </p>
        <?php if ($d['estado'] === 'resuelta'): ?>
          <p style="font-size:.85rem;color:var(--verde);">✅ Resolución: <?= e($d['resolucion']) ?></p>
        <?php else: ?>
          <form method="POST" action="index.php?p=admin_resolver" style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <input type="hidden" name="denuncia_id"  value="<?= $d['id'] ?>">
            <input type="hidden" name="denunciado_id" value="<?= $d['denunciado_id'] ?>">
            <input type="text"   name="resolucion" placeholder="Resolución..." required
                   style="flex:1;min-width:200px;border:1.5px solid var(--gris-200);border-radius:var(--radio-sm);padding:.4rem .7rem;">
            <button name="accion" value="resolver"  class="btn btn-secondary btn-sm">Marcar resuelta</button>
            <button name="accion" value="bloquear"  class="btn btn-danger btn-sm">Bloquear y resolver</button>
          </form>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
