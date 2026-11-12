<?php
/**
 * relatorio.php – RELATÓRIO dos fretes de um dia.
 * Escolha a data, veja a tabela e clique em "Baixar relatório (CSV)".
 * O arquivo CSV abre direto no Excel / LibreOffice (separador ponto e vírgula, acentos corretos).
 */
require 'config/config.php';

$data = trim((string)($_GET['data'] ?? date('Y-m-d')));
$dt = DateTime::createFromFormat('Y-m-d', $data);
$dataValida = $dt && $dt->format('Y-m-d') === $data;
$fretes = $dataValida ? Frete::completos($data) : [];
$totalFrete = array_sum(array_column($fretes, 'valor_frete'));

// Pediu o download? Envia o CSV e termina (antes de escrever qualquer HTML).
if ($dataValida && isset($_GET['baixar'])) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="relatorio-fretes-' . $data . '.csv"');
    $saida = fopen('php://output', 'w');
    fwrite($saida, "\xEF\xBB\xBF");                       // BOM: faz o Excel entender os acentos
    $csv = fn(array $linha) => fputcsv($saida, $linha, ';', '"', '\\');
    $csv(['Relatório de fretes do dia ' . dataBR($data)]);
    $csv(['ID', 'Data', 'Transportador', 'Origem', 'Destino', 'Produtos', 'Qtd. produtos', 'Peso total (kg)', 'Valor dos produtos (R$)', 'Valor do frete (R$)', 'Observação']);
    foreach ($fretes as $f) {
        $csv([
            $f['id'], dataBR($f['data']), $f['transportador'], $f['origem_completo'], $f['destino_completo'],
            implode(' | ', array_column($f['lista_produtos'], 'nome')), count($f['lista_produtos']),
            number_format($f['peso_total'], 2, ',', ''), number_format($f['valor_produtos'], 2, ',', ''),
            number_format($f['valor_frete'], 2, ',', ''), $f['observacao'],
        ]);
    }
    $csv(['', '', '', '', '', '', '', '', 'Total dos fretes', number_format($totalFrete, 2, ',', ''), '']);
    fclose($saida);
    exit;
}

$titulo = 'Relatório';
include 'includes/header.php';
?>
<h1 class="h4 mb-3">Relatório de fretes do dia</h1>

<form method="get" class="card shadow-sm mb-4"><div class="card-body row g-2 align-items-end">
  <div class="col-sm-4 col-md-3">
    <label class="form-label mb-1" for="data">Data</label>
    <input class="form-control" type="date" id="data" name="data" value="<?= e($data) ?>" required>
  </div>
  <div class="col-auto"><button class="btn btn-primary">Ver fretes</button></div>
  <?php if ($dataValida && $fretes): ?>
    <div class="col-auto"><a class="btn btn-success" href="?data=<?= e($data) ?>&baixar=1">⬇ Baixar relatório (CSV)</a></div>
  <?php endif; ?>
</div></form>

<?php if (!$dataValida): ?>
  <div class="alert alert-danger">Data inválida.</div>
<?php else: ?>
  <div class="card shadow-sm"><div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>#</th><th>Transportador</th><th>Rota</th><th>Produtos</th><th class="text-end">Frete</th></tr></thead>
      <tbody>
      <?php foreach ($fretes as $f): ?>
        <tr>
          <td><?= (int)$f['id'] ?></td>
          <td><?= e($f['transportador']) ?></td>
          <td><?= e($f['origem']) ?><br><small class="text-muted">→ <?= e($f['destino']) ?></small></td>
          <td><?= e(implode(', ', array_column($f['lista_produtos'], 'nome'))) ?></td>
          <td class="text-end"><?= moeda($f['valor_frete']) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$fretes): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">Nenhum frete em <?= e(dataBR($data)) ?>.</td></tr>
      <?php else: ?>
        <tr class="table-light fw-semibold"><td colspan="4"><?= count($fretes) ?> frete(s) no dia</td><td class="text-end"><?= moeda($totalFrete) ?></td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div></div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
