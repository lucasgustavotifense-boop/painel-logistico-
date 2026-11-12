<?php
/**
 * src/Endereco.php – um endereço. O mesmo cadastro serve para ORIGEM e DESTINO do frete.
 * O CEP não é conferido com nenhuma API (o roteiro dispensa).
 */
class Endereco extends Modelo
{
    public const UFS = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

    protected static function arquivo(): string { return 'enderecos'; }

    public static function validar(array $post): array
    {
        $uf = strtoupper(self::texto($post, 'uf', 'UF', true, 2));
        if (!in_array($uf, self::UFS, true)) {
            throw new InvalidArgumentException('UF inválida.');
        }
        return [
            'apelido'     => self::texto($post, 'apelido', 'Nome do local', true, 80),
            'cep'         => self::texto($post, 'cep', 'CEP', true, 9),
            'logradouro'  => self::texto($post, 'logradouro', 'Rua / Avenida', true, 120),
            'numero'      => self::texto($post, 'numero', 'Número', true, 10),
            'complemento' => self::texto($post, 'complemento', 'Complemento', false, 60),
            'bairro'      => self::texto($post, 'bairro', 'Bairro', true, 80),
            'cidade'      => self::texto($post, 'cidade', 'Cidade', true, 80),
            'uf'          => $uf,
        ];
    }

    public static function rotulo(array $e): string
    {
        return $e['apelido'] . ' – ' . $e['cidade'] . '/' . $e['uf'];
    }

    /** Endereço em uma linha só, para tabelas e relatório. */
    public static function completo(array $e): string
    {
        $linha = $e['logradouro'] . ', ' . $e['numero'];
        if ($e['complemento'] !== '') {
            $linha .= ' (' . $e['complemento'] . ')';
        }
        return $linha . ' – ' . $e['bairro'] . ', ' . $e['cidade'] . '/' . $e['uf'] . ' – CEP ' . $e['cep'];
    }

    protected static function verificarUso(int $id): void
    {
        foreach (Frete::todos() as $f) {
            if ($f['origem_id'] === $id || $f['destino_id'] === $id) {
                throw new InvalidArgumentException('Não é possível excluir: este endereço está em um frete.');
            }
        }
    }
}
