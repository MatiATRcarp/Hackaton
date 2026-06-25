<nav>
  <a href="index.php" class="nav-logo">SkillSwap <span>MVP</span></a>
  <div class="nav-links">
    <?php
    $p   = $_GET['p'] ?? '';
    $rol = Session::userRol();
    ?>
    <?php if (Session::isLoggedIn()): ?>
      <?php if ($rol === 'admin'): ?>
        <a href="index.php?p=admin"             class="<?= str_starts_with($p,'admin') ? 'activo' : '' ?>">Panel</a>
        <a href="index.php?p=admin_usuarios"    class="<?= $p==='admin_usuarios' ? 'activo' : '' ?>">Usuarios</a>
        <a href="index.php?p=admin_denuncias"   class="<?= $p==='admin_denuncias' ? 'activo' : '' ?>">Denuncias</a>
        <a href="index.php?p=admin_blockchain"  class="<?= $p==='admin_blockchain' ? 'activo' : '' ?>">Blockchain</a>
      <?php elseif ($rol === 'tutor'): ?>
        <a href="index.php?p=tutor"             class="<?= $p==='tutor' ? 'activo' : '' ?>">Dashboard</a>
        <a href="index.php?p=tutor_horarios"    class="<?= $p==='tutor_horarios' ? 'activo' : '' ?>">Mis horarios</a>
        <a href="index.php?p=tutor_perfil"      class="<?= $p==='tutor_perfil' ? 'activo' : '' ?>">Perfil</a>
      <?php else: ?>
        <a href="index.php?p=alumno"            class="<?= $p==='alumno' ? 'activo' : '' ?>">Mis tutorías</a>
        <a href="index.php?p=alumno_buscar"     class="<?= $p==='alumno_buscar' ? 'activo' : '' ?>">Buscar tutor</a>
        <a href="index.php?p=alumno_perfil"     class="<?= $p==='alumno_perfil' ? 'activo' : '' ?>">Perfil</a>
      <?php endif; ?>
      <span style="font-size:.82rem;color:var(--gris-400);">
        <?= e(Session::userName()) ?>
      </span>
      <a href="index.php?p=logout" class="btn-nav">Salir</a>
    <?php else: ?>
      <a href="index.php?p=alumno_buscar">Explorar</a>
      <a href="index.php?p=login">Iniciar sesión</a>
      <a href="index.php?p=registro_alumno" class="btn-nav">Registrarse</a>
    <?php endif; ?>
  </div>
</nav>
