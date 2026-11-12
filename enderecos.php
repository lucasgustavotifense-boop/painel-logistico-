<?php
/** enderecos.php – cadastro e edição de ENDEREÇOS (servem como origem e destino do frete). */
require 'config/config.php';

$cfg = [
    'titulo'   => 'Endereços',
    'singular' => 'Endereço',
    'classe'   => Endereco::class,
    'vazio'    => 'Nenhum endereço ainda. Cadastre a origem e o destino ao lado.',
    'campos'   => [
        ['nome' => 'apelido',     'rotulo' => 'Nome do local', 'obrigatorio' => true, 'dica' => 'Ex.: Depósito Central'],
        ['nome' => 'cep',         'rotulo' => 'CEP',           'col' => 6, 'mascara' => 'cep', 'obrigatorio' => true, 'dica' => '00000-000'],
        ['nome' => 'numero',      'rotulo' => 'Número',        'col' => 6, 'obrigatorio' => true],
        ['nome' => 'logradouro',  'rotulo' => 'Rua / Avenida', 'obrigatorio' => true],
        ['nome' => 'complemento', 'rotulo' => 'Complemento'],
        ['nome' => 'bairro',      'rotulo' => 'Bairro',        'obrigatorio' => true],
        ['nome' => 'cidade',      'rotulo' => 'Cidade',        'col' => 8, 'obrigatorio' => true],
        ['nome' => 'uf',          'rotulo' => 'UF',            'col' => 4, 'tipo' => 'select', 'opcoes' => Endereco::UFS, 'obrigatorio' => true],
    ],
    'colunas'  => [
        ['rotulo' => '#',        'valor' => fn($r) => (int)$r['id']],
        ['rotulo' => 'Local',    'valor' => fn($r) => e($r['apelido'])],
        ['rotulo' => 'Endereço', 'valor' => fn($r) => e($r['logradouro'] . ', ' . $r['numero']) . '<br><small class="text-muted">' . e($r['bairro'] . ' – ' . $r['cidade'] . '/' . $r['uf'] . ' – ' . $r['cep']) . '</small>'],
    ],
];
require 'includes/crud.php';
