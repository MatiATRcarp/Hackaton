<?php
require_once 'sesion.php';
require_once 'db.php';

requerirLogin();

$pdo = conectar();
$uid = usuarioId();

// ── Todas las habilidades para el filtro ──────────
$habilidades = $pdo->query('SELECT * FROM habilidades ORDER BY nombre')->fetchAll();

// ── Filtro seleccionado ───────────────────────────
$filtro_hab  = (int)($_GET['habilidad'] ?? 0);
$filtro_tipo = $_GET['tipo'] ?? 'ofrece'; // 'ofrece' o 'busca'

// ── Consulta de usuarios ──────────────────────────
// Mostrar todos excepto el usuario actual, con sus habilidades
if ($filtro_hab > 0) {
    $stmt = $pdo->prepare('
        SELECT DISTINCT u.id, u.nombre, u.carrera, u.bio
        FROM usuarios u
        JOIN usuario_habilidades uh ON uh.usuario_id = u.id
        WHERE u.id != ?
          AND uh.habilidad_id = ?
          AND uh.tipo = ?
        ORDER BY u.nombre
    ');
    $stmt->execute([$uid, $filtro_hab, $filtro_tipo]);
} else {
    $stmt = $pdo->prepare('
        SELECT id, nombre, carrera, bio
        FROM usuarios
        WHERE id != ?
        ORDER BY nombre
    ');
    $stmt->execute([$uid]);
}

$usuarios = $stmt->fetchAll();

// ── Para cada usuario, cargar sus habilidades ─────
$usuarios_con_skills = [];
foreach ($usuarios as $u) {
    $s = $pdo->prepare('
        SELECT h.nombre, uh.tipo
        FROM usuario_habilidades uh
        JOIN habilidades h ON h.id = uh.habilidad_id
        WHERE uh.usuario_id = ?
        ORDER BY uh.tipo, h.nombre
    ');
    $s->execute([$u['id']]);
    $u['habilidades'] = $s->fetchAll();
    $usuarios_con_skills[] = $u;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Explorar · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php" class="activo">Explorar</a>
    <a href="mis_solicitudes.php">Mis solicitudes</a>
    <a href="perfil.php">Mi perfil</a>
    <a href="logout.php" class="btn-nav">Salir</a>
  </div>
</nav>

<div class="contenedor">

  <div class="seccion-titulo mt-3">
    <h2>Explorar estudiantes</h2>
    <span class="text-muted"><?= count($usuarios_con_skills) ?> resultado<?= count($usuarios_con_skills) !== 1 ? 's' : '' ?></span>
  </div>

  <!-- ── Filtros ── -->
  <form method="GET" action="explorar.php" class="filtros">
    <div class="form-group">
      <label for="habilidad">Filtrar por habilidad</label>
      <select id="habilidad" name="habilidad">
        <option value="0">Todas las habilidades</option>
        <?php foreach ($habilidades as $h): ?>
          <option value="<?= $h['id'] ?>" <?= $filtro_hab === (int)$h['id'] ? 'selected' : '' ?>>
            <?= e($h['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="tipo">Buscar a quienes</label>
      <select id="tipo" name="tipo">
        <option value="ofrece" <?= $filtro_tipo === 'ofrece' ? 'selected' : '' ?>>Ofrecen esa habilidad</option>
        <option value="busca"  <?= $filtro_tipo === 'busca'  ? 'selected' : '' ?>>Buscan esa habilidad</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filtrar</button>
    <?php if ($filtro_hab > 0): ?>
      <a href="explorar.php" class="btn btn-ghost" style="align-self:flex-end;">Limpiar</a>
    <?php endif; ?>
  </form>

  <!-- ── Grilla de usuarios ── -->
  <?php if (empty($usuarios_con_skills)): ?>
    <div class="vacio">
      <p style="font-size:2rem;">🔍</p>
      <p>No se encontraron estudiantes con ese criterio.</p>
    </div>
  <?php else: ?>
    <div class="grilla-usuarios">
      <?php foreach ($usuarios_con_skills as $u): ?>
        <?php
          $ofrece = array_filter($u['habilidades'], fn($h) => $h['tipo'] === 'ofrece');
          $busca  = array_filter($u['habilidades'], fn($h) => $h['tipo'] === 'busca');
        ?>
        <div class="tarjeta-usuario">
          <div class="usuario-header">
            <div class="avatar"><?= e(iniciales($u['nombre'])) ?></div>
            <div class="usuario-info">
              <h3><?= e($u['nombre']) ?></h3>
              <p><?= e($u['carrera'] ?: 'Carrera no indicada') ?></p>
            </div>
          </div>

          <?php if ($u['bio']): ?>
            <p style="font-size:0.83rem;color:var(--texto-2);line-height:1.5;">
              <?= e(mb_strimwidth($u['bio'], 0, 100, '…')) ?>
            </p>
          <?php endif; ?>

          <?php if ($ofrece): ?>
            <div>
              <p class="chip-label">Ofrece</p>
              <div class="chips-lista">
                <?php foreach ($ofrece as $h): ?>
                  <span class="chip chip-ofrece"><?= e($h['nombre']) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if ($busca): ?>
            <div>
              <p class="chip-label">Busca</p>
              <div class="chips-lista">
                <?php foreach ($busca as $h): ?>
                  <span class="chip chip-busca"><?= e($h['nombre']) ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <a href="ver_perfil.php?id=<?= $u['id'] ?>" class="btn btn-secondary btn-sm" style="margin-top:auto;">
            Ver perfil →
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
