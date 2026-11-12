<?php
/** index.php – página inicial (painel): resumo dos cadastros, passo a passo e últimos fretes. */
require 'config/config.php';

$produtos = Produto::todos();
$enderecos = Endereco::todos();
$transportadores = Transportador::todos();
$vazio = !$produtos && !$enderecos && !$transportadores;

// Botão opcional para testar rápido: só aparece (e só funciona) se nada foi cadastrado ainda.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exemplo']) && $vazio) {
    Exemplo::carregar();
    flash('success', 'Dados de exemplo carregados. Agora é só gerar um frete!');
    redirecionar('index.php');
}

$fretes = Frete::completos();
usort($fretes, fn($a, $b) => [$b['data'], $b['id']] <=> [$a['data'], $a['id']]);
$ultimos = array_slice($fretes, 0, 5);

$cartoes = [
    ['Produtos', count($produtos), 'produtos.php'],
    ['Endereços', count($enderecos), 'enderecos.php'],
    ['Transportadores', count($transportadores), 'transportadores.php'],
    ['Fretes', count($fretes), 'fretes.php'],
];

$titulo = 'Início';
include 'includes/header.php';
?>
<h1 class="h4 mb-3">Painel logístico</h1>
<?php mostrarFlash(); ?>

<div class="row g-3 mb-4">
  <?php foreach ($cartoes as [$nome, $qtd, $link]): ?>
    <div class="col-6 col-lg-3">
      <a class="text-decoration-none" href="<?= $link ?>">
        <div class="card shadow-sm h-100"><div class="card-body">
          <div class="cartao-numero text-primary"><?= $qtd ?></div>
          <div class="text-muted"><?= $nome ?></div>
        </div></div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card shadow-sm h-100"><div class="card-body">
      <h2 class="h6 mb-3">Como usar</h2>
      <ol class="mb-3">
        <li>Cadastre os <a href="produtos.php">produtos</a>.</li>
        <li>Cadastre os <a href="enderecos.php">endereços</a> de origem e destino.</li>
        <li>Cadastre o <a href="transportadores.php">transportador</a>.</li>
        <li>Gere o <a href="frete_novo.php">frete</a>: um transportador pode levar vários produtos.</li>
        <li>Baixe o <a href="relatorio.php">relatório</a> dos fretes de um dia.</li>
      </ol>
      <?php if ($vazio): ?>
        <form method="post"><button class="btn btn-outline-primary btn-sm" name="exemplo" value="1">Carregar dados de exemplo</button></form>
      <?php endif; ?>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card shadow-sm h-100"><div class="card-body">
      <h2 class="h6 mb-3">Últimos fretes</h2>
      <?php if (!$ultimos): ?>
        <p class="text-muted mb-0">Nenhum frete ainda.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($ultimos as $f): ?>
            <li class="list-group-item px-0 d-flex justify-content-between">
              <span>#<?= (int)$f['id'] ?> • <?= e(dataBR($f['data'])) ?> • <?= e($f['transportador']) ?><br>
                <small class="text-muted"><?= e($f['origem']) ?> → <?= e($f['destino']) ?></small></span>
              <span class="text-nowrap"><?= count($f['lista_produtos']) ?> item(ns)</span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div></div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
