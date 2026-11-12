<?php
/** produtos.php – cadastro e edição de PRODUTOS. */
require 'config/config.php';

$cfg = [
    'titulo'   => 'Produtos',
    'singular' => 'Produto',
    'classe'   => Produto::class,
    'vazio'    => 'Nenhum produto ainda. Cadastre o primeiro ao lado.',
    'campos'   => [
        ['nome' => 'nome',      'rotulo' => 'Nome',        'obrigatorio' => true],
        ['nome' => 'descricao', 'rotulo' => 'Descrição',   'tipo' => 'textarea'],
        ['nome' => 'peso',      'rotulo' => 'Peso (kg)',   'tipo' => 'decimal', 'col' => 6, 'dica' => '0,00'],
        ['nome' => 'valor',     'rotulo' => 'Valor (R$)',  'tipo' => 'decimal', 'col' => 6, 'dica' => '0,00', 'obrigatorio' => true],
    ],
    'colunas'  => [
        ['rotulo' => '#',       'valor' => fn($r) => (int)$r['id']],
        ['rotulo' => 'Produto', 'valor' => fn($r) => e($r['nome']) . '<br><small class="text-muted">' . e($r['descricao']) . '</small>'],
        ['rotulo' => 'Peso',    'valor' => fn($r) => number_format($r['peso'], 2, ',', '.') . ' kg'],
        ['rotulo' => 'Valor',   'valor' => fn($r) => moeda($r['valor'])],
    ],
];
require 'includes/crud.php';
