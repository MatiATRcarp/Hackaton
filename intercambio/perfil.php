<?php
require_once 'sesion.php';
require_once 'db.php';

requerirLogin();

$pdo    = conectar();
$uid    = usuarioId();
$error  = '';
$exito  = '';

// ── Cargar datos del usuario ──────────────────────
$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$uid]);
$usuario = $stmt->fetch();

// ── Cargar todas las habilidades disponibles ──────
$habilidades = $pdo->query('SELECT * FROM habilidades ORDER BY nombre')->fetchAll();

// ── Cargar habilidades del usuario ────────────────
$stmt = $pdo->prepare('
    SELECT h.id, h.nombre, uh.tipo
    FROM usuario_habilidades uh
    JOIN habilidades h ON h.id = uh.habilidad_id
    WHERE uh.usuario_id = ?
    ORDER BY uh.tipo, h.nombre
');
$stmt->execute([$uid]);
$mis_habilidades = $stmt->fetchAll();

$ofrece_ids = array_column(array_filter($mis_habilidades, fn($h) => $h['tipo'] === 'ofrece'), 'id');
$busca_ids  = array_column(array_filter($mis_habilidades, fn($h) => $h['tipo'] === 'busca'),  'id');

// ── Guardar datos básicos ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {

    if ($_POST['accion'] === 'perfil') {
        $nombre  = trim($_POST['nombre']  ?? '');
        $carrera = trim($_POST['carrera'] ?? '');
        $bio     = trim($_POST['bio']     ?? '');

        if (empty($nombre)) {
            $error = 'El nombre no puede estar vacío.';
        } else {
            $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, carrera = ?, bio = ? WHERE id = ?');
            $stmt->execute([$nombre, $carrera, $bio, $uid]);
            $_SESSION['usuario_nombre'] = $nombre;
            $exito = 'Perfil actualizado correctamente.';
            // Recargar datos
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
            $stmt->execute([$uid]);
            $usuario = $stmt->fetch();
        }
    }

    if ($_POST['accion'] === 'habilidades') {
        $ofrece = $_POST['ofrece'] ?? [];
        $busca  = $_POST['busca']  ?? [];

        // Eliminar las anteriores
        $stmt = $pdo->prepare('DELETE FROM usuario_habilidades WHERE usuario_id = ?');
        $stmt->execute([$uid]);

        // Insertar las nuevas
        $stmt = $pdo->prepare('INSERT INTO usuario_habilidades (usuario_id, habilidad_id, tipo) VALUES (?, ?, ?)');
        foreach ($ofrece as $hid) {
            $stmt->execute([$uid, (int)$hid, 'ofrece']);
        }
        foreach ($busca as $hid) {
            if (!in_array($hid, $ofrece)) { // no duplicar
                $stmt->execute([$uid, (int)$hid, 'busca']);
            }
        }

        $exito = 'Habilidades actualizadas.';

        // Recargar
        $stmt = $pdo->prepare('
            SELECT h.id, h.nombre, uh.tipo
            FROM usuario_habilidades uh
            JOIN habilidades h ON h.id = uh.habilidad_id
            WHERE uh.usuario_id = ?
            ORDER BY uh.tipo, h.nombre
        ');
        $stmt->execute([$uid]);
        $mis_habilidades = $stmt->fetchAll();
        $ofrece_ids = array_column(array_filter($mis_habilidades, fn($h) => $h['tipo'] === 'ofrece'), 'id');
        $busca_ids  = array_column(array_filter($mis_habilidades, fn($h) => $h['tipo'] === 'busca'),  'id');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi perfil · SkillSwap</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>Beta</span></a>
  <div class="nav-links">
    <a href="explorar.php">Explorar</a>
    <a href="mis_solicitudes.php">Mis solicitudes</a>
    <a href="perfil.php" class="activo">Mi perfil</a>
    <a href="logout.php" class="btn-nav">Salir</a>
  </div>
</nav>

<div class="contenedor-md">

  <div class="perfil-header mt-3">
    <div class="avatar avatar-lg"><?= e(iniciales($usuario['nombre'])) ?></div>
    <div>
      <h2><?= e($usuario['nombre']) ?></h2>
      <p><?= e($usuario['carrera'] ?: 'Sin carrera indicada') ?></p>
    </div>
  </div>

  <?php if ($exito): ?>
    <div class="alerta alerta-exito"><?= e($exito) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alerta alerta-error"><?= e($error) ?></div>
  <?php endif; ?>

  <!-- ── Editar datos del perfil ── -->
  <div class="card mb-2">
    <div class="card-header">
      <h2>Datos personales</h2>
      <p>Tu información visible para otros estudiantes</p>
    </div>

    <form method="POST" action="perfil.php">
      <input type="hidden" name="accion" value="perfil">

      <div class="form-group">
        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre"
               value="<?= e($usuario['nombre']) ?>" required>
      </div>

      <div class="form-group">
        <label for="carrera">Carrera / facultad</label>
        <input type="text" id="carrera" name="carrera"
               value="<?= e($usuario['carrera'] ?? '') ?>"
               placeholder="Ej: Ingeniería en Sistemas, 2do año">
      </div>

      <div class="form-group">
        <label for="bio">Sobre mí</label>
        <textarea id="bio" name="bio"
                  placeholder="Contá algo de vos: tus intereses, en qué año estás, cómo preferís trabajar..."><?= e($usuario['bio'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>
  </div>

  <!-- ── Habilidades ── -->
  <div class="card">
    <div class="card-header">
      <h2>Mis habilidades</h2>
      <p>Marcá qué podés enseñar y qué querés aprender</p>
    </div>

    <form method="POST" action="perfil.php">
      <input type="hidden" name="accion" value="habilidades">

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        <div>
          <p class="chip-label" style="color:var(--lila-600);margin-bottom:10px;">
            🟣 Lo que puedo <strong>enseñar</strong>
          </p>
          <?php foreach ($habilidades as $h): ?>
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:0.9rem;font-weight:400;cursor:pointer;">
              <input type="checkbox" name="ofrece[]"
                     value="<?= $h['id'] ?>"
                     <?= in_array($h['id'], $ofrece_ids) ? 'checked' : '' ?>
                     style="accent-color:var(--lila-500);width:15px;height:15px;">
              <?= e($h['nombre']) ?>
            </label>
          <?php endforeach; ?>
        </div>

        <div>
          <p class="chip-label" style="color:#c2410c;margin-bottom:10px;">
            🟠 Lo que quiero <strong>aprender</strong>
          </p>
          <?php foreach ($habilidades as $h): ?>
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;font-size:0.9rem;font-weight:400;cursor:pointer;">
              <input type="checkbox" name="busca[]"
                     value="<?= $h['id'] ?>"
                     <?= in_array($h['id'], $busca_ids) ? 'checked' : '' ?>
                     style="accent-color:#f97316;width:15px;height:15px;">
              <?= e($h['nombre']) ?>
            </label>
          <?php endforeach; ?>
        </div>

      </div>

      <hr class="divider">
      <button type="submit" class="btn btn-primary">Guardar habilidades</button>
    </form>
  </div>

</div>

<footer>
  SkillSwap &mdash; Proyecto Hackathon &copy; <?= date('Y') ?>
</footer>

</body>
</html>
