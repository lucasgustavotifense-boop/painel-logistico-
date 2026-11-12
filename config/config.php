<?php
/**
 * config/config.php
 * Configurações gerais. Todas as páginas começam com: require 'config/config.php';
 * - inicia a sessão (usada nas mensagens de aviso),
 * - define a pasta onde os dados ficam salvos (data/),
 * - registra o "autoload": quando o PHP encontra uma classe (ex.: Produto), ele procura em src/Produto.php.
 */
declare(strict_types=1);

session_start();
date_default_timezone_set('America/Sao_Paulo');

define('RAIZ', dirname(__DIR__));
define('DIR_DADOS', RAIZ . '/data');

spl_autoload_register(function (string $classe): void {
    $arquivo = RAIZ . '/src/' . $classe . '.php';
    if (is_file($arquivo)) {
        require $arquivo;
    }
});

require_once RAIZ . '/includes/funcoes.php';
