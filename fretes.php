<?php
/** fretes.php – lista dos FRETES gerados (com transportador, rota e produtos). */
require 'config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir'])) {
    Frete::excluir((int)$_POST['excluir']);
    flash('success', 'Frete excluído.');
    redirecionar('fretes.php');
}

$fretes = Frete::completos();
usort($fretes, fn($a, $b) => [$b['data'], $b['id']] <=> [$a['data'], $a['id']]);

$titulo = 'Fretes';
include 'includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Fretes</h1>
  <a class="btn btn-primary" href="frete_novo.php">+ Novo frete</a>
</div>
<?php mostrarFlash(); ?>

<div class="card shadow-sm"><div class="table-responsive">
  <table class="table table-hover align-middle mb-0">
    <thead><tr><th>#</th><th>Data</th><th>Transportador</th><th>Rota</th><th>Produtos</th><th class="text-end">Frete</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($fretes as $f): ?>
      <tr>
        <td><?= (int)$f['id'] ?></td>
        <td class="text-nowrap"><?= e(dataBR($f['data'])) ?></td>
        <td><?= e($f['transportador']) ?></td>
        <td><?= e($f['origem']) ?><br><small class="text-muted">→ <?= e($f['destino']) ?></small></td>
        <td>
          <?php foreach ($f['lista_produtos'] as $p): ?><span class="badge text-bg-light border"><?= e($p['nome']) ?></span> <?php endforeach; ?>
          <br><small class="text-muted"><?= count($f['lista_produtos']) ?> item(ns) • <?= number_format($f['peso_total'], 2, ',', '.') ?> kg • <?= moeda($f['valor_produtos']) ?></small>
        </td>
        <td class="text-end text-nowrap"><?= moeda($f['valor_frete']) ?></td>
        <td class="text-end">
          <form method="post" onsubmit="return confirm('Excluir este frete?')">
            <button class="btn btn-sm btn-outline-danger" name="excluir" value="<?= (int)$f['id'] ?>">Excluir</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$fretes): ?>
      <tr><td colspan="7" class="text-center text-muted py-4">Nenhum frete gerado ainda. <a href="frete_novo.php">Gere o primeiro</a>.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div></div>
<?php include 'includes/footer.php'; ?>
