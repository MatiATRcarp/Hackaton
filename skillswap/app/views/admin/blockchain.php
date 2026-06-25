<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blockchain · Admin SkillSwap</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>
<?php require BASE_PATH . '/app/views/partials/nav.php'; ?>
<div class="contenedor mt-3">

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2 style="font-weight:800;">⛓ Registro de pagos Blockchain</h2>
    <a href="index.php?p=admin" class="btn btn-ghost btn-sm">← Volver</a>
  </div>

  <div class="alerta alerta-info">
    <strong>Red:</strong> Sepolia Testnet (simulado) · Los tx_hash son SHA-256 deterministicos que representan la estructura de una transacción real en Ethereum.
  </div>

  <?php if (empty($pagos)): ?>
    <div class="vacio"><p>⛓</p><p>No hay pagos registrados todavía.</p></div>
  <?php else: ?>
    <div class="tabla-wrap">
      <table>
        <thead>
          <tr>
            <th>#Sol.</th><th>Tutor</th><th>Alumno</th>
            <th>Wallet destino</th><th>Monto ETH</th><th>Estado</th><th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pagos as $pago): ?>
          <tr>
            <td><?= $pago['solicitud_id'] ?></td>
            <td><?= e($pago['tutor_nombre']) ?></td>
            <td><?= e($pago['alumno_nombre']) ?></td>
            <td>
              <div class="tx-card" style="font-size:.7rem;padding:.4rem .6rem;margin:0;">
                <?= e(substr($pago['wallet_destino'], 0, 20)) ?>…
              </div>
            </td>
            <td><strong><?= e($pago['monto_eth']) ?> ETH</strong></td>
            <td><span class="badge badge-success"><?= e($pago['estado']) ?></span></td>
            <td style="font-size:.78rem;"><?= fechaHora($pago['created_at']) ?></td>
          </tr>
          <tr>
            <td colspan="7" style="padding-top:0;padding-bottom:.8rem;">
              <div class="tx-card">
                <span>tx_hash:</span> <?= e($pago['tx_hash']) ?><br>
                <span>red:</span> <?= e($pago['red']) ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>
<?php require BASE_PATH . '/app/views/partials/footer.php'; ?>
</body>
</html>
