<?php
/**
 * includes/funcoes.php
 * Funções pequenas usadas em várias telas.
 */

// Se a extensão mbstring estiver desligada no PHP, usa versões simples no lugar.
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $s): int { return preg_match_all('/./us', $s) ?: strlen($s); }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $s): string { return strtolower($s); }
}

/** Escapa texto antes de mostrar no HTML (evita que alguém injete código na tela). */
function e($valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

/** Formata número como dinheiro: 1234.5 -> "R$ 1.234,50". */
function moeda(float $v): string
{
    return 'R$ ' . number_format($v, 2, ',', '.');
}

/** Formata data do banco (2026-11-12) para o jeito brasileiro (12/11/2026). */
function dataBR(string $d): string
{
    $dt = DateTime::createFromFormat('Y-m-d', $d);
    return $dt ? $dt->format('d/m/Y') : $d;
}

/** Lê número digitado em formato brasileiro ("1.234,50" ou "12,5") e devolve float. */
function lerNumero($texto): float
{
    $t = trim((string)$texto);
    if ($t === '') {
        return 0.0;
    }
    if (strpos($t, ',') !== false) {            // tem vírgula: ponto é separador de milhar
        $t = str_replace('.', '', $t);
        $t = str_replace(',', '.', $t);
    }
    if (!is_numeric($t)) {
        throw new InvalidArgumentException('Número inválido: "' . $texto . '".');
    }
    return (float)$t;
}

/** Guarda uma mensagem para aparecer na próxima tela (depois de um redirecionamento). */
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $mensagem];
}

/** Mostra (e apaga) a mensagem guardada. Tipos: success, danger, warning. */
function mostrarFlash(): void
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="alert alert-' . e($f['tipo']) . ' alert-dismissible fade show" role="alert">'
           . e($f['msg'])
           . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button></div>';
    }
}

/** Redireciona para outra página e encerra o script. */
function redirecionar(string $destino): void
{
    header('Location: ' . $destino);
    exit;
}
