<?php
/** transportadores.php – cadastro e edição de TRANSPORTADORES. */
require 'config/config.php';

$cfg = [
    'titulo'   => 'Transportadores',
    'singular' => 'Transportador',
    'classe'   => Transportador::class,
    'vazio'    => 'Nenhum transportador ainda. Cadastre o primeiro ao lado.',
    'campos'   => [
        ['nome' => 'nome',     'rotulo' => 'Nome / Razão social', 'obrigatorio' => true],
        ['nome' => 'cnpj',     'rotulo' => 'CNPJ',      'mascara' => 'cnpj', 'dica' => '00.000.000/0000-00'],
        ['nome' => 'telefone', 'rotulo' => 'Telefone',  'col' => 6, 'mascara' => 'telefone'],
        ['nome' => 'placa',    'rotulo' => 'Placa',     'col' => 6, 'dica' => 'ABC1D23'],
        ['nome' => 'email',    'rotulo' => 'E-mail',    'input' => 'email'],
    ],
    'colunas'  => [
        ['rotulo' => '#',             'valor' => fn($r) => (int)$r['id']],
        ['rotulo' => 'Transportador', 'valor' => fn($r) => e($r['nome']) . '<br><small class="text-muted">' . e($r['cnpj']) . '</small>'],
        ['rotulo' => 'Contato',       'valor' => fn($r) => e($r['telefone']) . '<br><small class="text-muted">' . e($r['email']) . '</small>'],
        ['rotulo' => 'Veículo',       'valor' => fn($r) => e($r['placa'])],
    ],
];
require 'includes/crud.php';
