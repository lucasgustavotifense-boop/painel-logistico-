<?php
/**
 * includes/header.php
 * Topo de todas as páginas: abre o HTML, carrega o Bootstrap e desenha o MENU de navegação.
 * A página atual fica destacada no menu.
 */
$atual = basename($_SERVER['SCRIPT_NAME']);
$menu = [
    'index.php'           => ['Início',          ['index.php']],
    'produtos.php'        => ['Produtos',        ['produtos.php']],
    'enderecos.php'       => ['Endereços',       ['enderecos.php']],
    'transportadores.php' => ['Transportadores', ['transportadores.php']],
    'fretes.php'          => ['Fretes',          ['fretes.php', 'frete_novo.php']],
    'relatorio.php'       => ['Relatório',       ['relatorio.php']],
];
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo ?? 'Painel Logístico') ?> – Painel Logístico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/estilo.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-escuro mb-4">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="index.php">🚚 Painel Logístico</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Abrir menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav me-auto">
        <?php foreach ($menu as $arquivo => [$rotulo, $paginas]): ?>
          <li class="nav-item">
            <a class="nav-link <?= in_array($atual, $paginas, true) ? 'active' : '' ?>" href="<?= $arquivo ?>"><?= $rotulo ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <a class="btn btn-sm btn-light" href="frete_novo.php">+ Novo frete</a>
    </div>
  </div>
</nav>
<main class="container pb-5">
