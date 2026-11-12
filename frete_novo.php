<?php
/**
 * frete_novo.php – GERAR FRETE.
 * Escolhe transportador, origem, destino e VÁRIOS produtos (as linhas de produto são
 * adicionadas/removidas dinamicamente por assets/js/frete.js). Ao salvar, a classe Frete valida tudo.
 */
require 'config/config.php';

$transportadores = Transportador::todos();
$enderecos       = Endereco::todos();
$produtos        = Produto::todos();

$erro = '';
$v = ['data' => date('Y-m-d'), 'transportador_id' => '', 'origem_id' => '', 'destino_id' => '',
      'produtos' => [''], 'valor_frete' => '', 'observacao' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $frete = Frete::salvar($_POST);
        flash('success', 'Frete #' . $frete['id'] . ' gerado com sucesso.');
        redirecionar('fretes.php');
    } catch (InvalidArgumentException $ex) {
        $erro = $ex->getMessage();
        $v = array_merge($v, $_POST);
        $v['produtos'] = array_values((array)($_POST['produtos'] ?? [''])) ?: [''];
    }
}

/** Monta as opções de um select a partir de uma lista de registros. */
function opcoes(array $lista, string $classe, $selecionado, bool $comDados = false): string
{
    $html = '<option value="">Escolha...</option>';
    foreach ($lista as $r) {
        $dados = $comDados ? ' data-valor="' . e($r['valor']) . '" data-peso="' . e($r['peso']) . '"' : '';
        $html .= '<option value="' . (int)$r['id'] . '"' . $dados . ((string)$selecionado === (string)$r['id'] ? ' selected' : '')
              . '>' . e($classe::rotulo($r)) . ($comDados ? ' – ' . moeda($r['valor']) : '') . '</option>';
    }
    return $html;
}

$faltando = [];
if (!$transportadores) $faltando[] = '<a href="transportadores.php">um transportador</a>';
if (count($enderecos) < 2) $faltando[] = '<a href="enderecos.php">dois endereços (origem e destino)</a>';
if (!$produtos) $faltando[] = '<a href="produtos.php">pelo menos um produto</a>';

$titulo = 'Novo frete';
$scriptExtra = 'assets/js/frete.js';
include 'includes/header.php';
?>
<h1 class="h4 mb-3">Gerar frete</h1>
<?php mostrarFlash(); ?>
<?php if ($erro): ?><div class="alert alert-danger" id="erro-servidor"><?= e($erro) ?></div><?php endif; ?>

<?php if ($faltando): ?>
  <div class="alert alert-warning">Antes de gerar um frete, cadastre <?= implode(', ', $faltando) ?>.</div>
<?php else: ?>
<form method="post" id="form-frete" class="card shadow-sm"><div class="card-body">
  <div id="erro-js" class="alert alert-danger d-none" role="alert"></div>
  <div class="row g-3">
    <div class="col-md-3">
      <label class="form-label" for="data">Data do frete *</label>
      <input class="form-control" type="date" id="data" name="data" value="<?= e($v['data']) ?>" required>
    </div>
    <div class="col-md-9">
      <label class="form-label" for="transportador_id">Transportador *</label>
      <select class="form-select" id="transportador_id" name="transportador_id" required>
        <?= opcoes($transportadores, Transportador::class, $v['transportador_id']) ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="origem_id">Endereço de origem *</label>
      <select class="form-select" id="origem_id" name="origem_id" required>
        <?= opcoes($enderecos, Endereco::class, $v['origem_id']) ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="destino_id">Endereço de destino *</label>
      <select class="form-select" id="destino_id" name="destino_id" required>
        <?= opcoes($enderecos, Endereco::class, $v['destino_id']) ?>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label mb-1">Produtos do frete *</label>
      <div id="linhas" class="d-grid gap-2">
        <?php foreach ($v['produtos'] as $sel): ?>
          <div class="input-group linha-produto">
            <select class="form-select" name="produtos[]"><?= opcoes($produtos, Produto::class, $sel, true) ?></select>
            <button type="button" class="btn btn-outline-danger btn-remover" title="Remover produto">Remover</button>
          </div>
        <?php endforeach; ?>
      </div>
      <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="btn-add">+ Adicionar produto</button>
      <div class="text-muted small mt-2" id="resumo">Nenhum produto escolhido.</div>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="valor_frete">Valor do frete (R$)</label>
      <input class="form-control" id="valor_frete" name="valor_frete" inputmode="decimal" placeholder="0,00" value="<?= e($v['valor_frete']) ?>">
    </div>
    <div class="col-md-8">
      <label class="form-label" for="observacao">Observação</label>
      <input class="form-control" id="observacao" name="observacao" maxlength="255" value="<?= e($v['observacao']) ?>">
    </div>
  </div>
  <div class="mt-4">
    <button class="btn btn-primary">Gerar frete</button>
    <a class="btn btn-link" href="fretes.php">Cancelar</a>
  </div>
</div></form>

<!-- Modelo de linha: o JavaScript copia este bloco cada vez que clica em "Adicionar produto". -->
<template id="modelo-linha">
  <div class="input-group linha-produto">
    <select class="form-select" name="produtos[]"><?= opcoes($produtos, Produto::class, '', true) ?></select>
    <button type="button" class="btn btn-outline-danger btn-remover" title="Remover produto">Remover</button>
  </div>
</template>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
