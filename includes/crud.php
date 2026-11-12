<?php
/**
 * includes/crud.php
 * Tela padrão de CADASTRO E EDIÇÃO (formulário à esquerda + lista à direita).
 * Usada por produtos.php, enderecos.php e transportadores.php: cada uma só envia a configuração ($cfg)
 * com o nome da classe, os campos do formulário e as colunas da tabela.
 */
$classe = $cfg['classe'];
$pagina = basename($_SERVER['SCRIPT_NAME']);
$erro = '';
$valores = [];

// 1) Recebeu um formulário? Salva ou exclui e volta para a própria página.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['excluir'])) {
            $classe::excluir((int)$_POST['excluir']);
            flash('success', $cfg['singular'] . ' excluído.');
        } else {
            $classe::salvar($_POST);
            flash('success', $cfg['singular'] . ' salvo com sucesso.');
        }
        redirecionar($pagina);
    } catch (InvalidArgumentException $ex) {
        $erro = $ex->getMessage();
        $valores = $_POST;                       // mantém o que a pessoa digitou
    }
}

// 2) Está editando? (?editar=ID) Carrega o registro no formulário.
if (!$valores && isset($_GET['editar'])) {
    $valores = $classe::buscar((int)$_GET['editar']) ?? [];
}
$editando = !empty($valores['id']);
$lista = $classe::todos();
$titulo = $cfg['titulo'];

/** Mostra o valor de um campo no formulário (números em formato brasileiro). */
$mostrar = function (array $c) use ($valores): string {
    $v = $valores[$c['nome']] ?? '';
    if (($c['tipo'] ?? '') === 'decimal' && $v !== '' && is_numeric($v)) {
        return number_format((float)$v, 2, ',', '');
    }
    return (string)$v;
};

include __DIR__ . '/header.php';
?>
<h1 class="h4 mb-3"><?= e($cfg['titulo']) ?></h1>
<?php mostrarFlash(); ?>
<?php if ($erro): ?><div class="alert alert-danger"><?= e($erro) ?></div><?php endif; ?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card shadow-sm"><div class="card-body">
      <h2 class="h6 mb-3"><?= $editando ? 'Editar ' : 'Novo ' ?><?= e(mb_strtolower($cfg['singular'])) ?></h2>
      <form method="post" class="row g-2">
        <input type="hidden" name="id" value="<?= e($valores['id'] ?? '') ?>">
        <?php foreach ($cfg['campos'] as $c): $id = 'c_' . $c['nome']; ?>
          <div class="col-<?= (int)($c['col'] ?? 12) ?>">
            <label class="form-label mb-1" for="<?= $id ?>"><?= e($c['rotulo']) ?><?= !empty($c['obrigatorio']) ? ' *' : '' ?></label>
            <?php if (($c['tipo'] ?? 'text') === 'select'): ?>
              <select class="form-select" id="<?= $id ?>" name="<?= e($c['nome']) ?>" <?= !empty($c['obrigatorio']) ? 'required' : '' ?>>
                <option value="">Escolha...</option>
                <?php foreach ($c['opcoes'] as $op): ?>
                  <option <?= ($valores[$c['nome']] ?? '') === $op ? 'selected' : '' ?>><?= e($op) ?></option>
                <?php endforeach; ?>
              </select>
            <?php elseif (($c['tipo'] ?? '') === 'textarea'): ?>
              <textarea class="form-control" id="<?= $id ?>" name="<?= e($c['nome']) ?>" rows="2"><?= e($mostrar($c)) ?></textarea>
            <?php else: ?>
              <input class="form-control" id="<?= $id ?>" name="<?= e($c['nome']) ?>" value="<?= e($mostrar($c)) ?>"
                     type="<?= e($c['input'] ?? 'text') ?>" placeholder="<?= e($c['dica'] ?? '') ?>"
                     <?= !empty($c['mascara']) ? 'data-mascara="' . e($c['mascara']) . '"' : '' ?>
                     <?= !empty($c['decimal']) || ($c['tipo'] ?? '') === 'decimal' ? 'inputmode="decimal"' : '' ?>
                     <?= !empty($c['obrigatorio']) ? 'required' : '' ?>>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div class="col-12 mt-3">
          <button class="btn btn-primary">Salvar <?= e(mb_strtolower($cfg['singular'])) ?></button>
          <?php if ($editando): ?><a class="btn btn-link" href="<?= e($pagina) ?>">Cancelar</a><?php endif; ?>
        </div>
      </form>
    </div></div>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm"><div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead><tr>
          <?php foreach ($cfg['colunas'] as $col): ?><th><?= e($col['rotulo']) ?></th><?php endforeach; ?>
          <th></th>
        </tr></thead>
        <tbody>
        <?php foreach ($lista as $r): ?>
          <tr>
            <?php foreach ($cfg['colunas'] as $col): ?><td><?= $col['valor']($r) ?></td><?php endforeach; ?>
            <td class="text-end text-nowrap">
              <a class="btn btn-sm btn-outline-primary" href="?editar=<?= (int)$r['id'] ?>">Editar</a>
              <form method="post" class="d-inline" onsubmit="return confirm('Excluir este registro?')">
                <button class="btn btn-sm btn-outline-danger" name="excluir" value="<?= (int)$r['id'] ?>">Excluir</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$lista): ?>
          <tr><td colspan="<?= count($cfg['colunas']) + 1 ?>" class="text-center text-muted py-4"><?= e($cfg['vazio']) ?></td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div></div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
