<?php
/** src/Produto.php – o que será transportado: nome, descrição, peso (kg) e valor (R$). */
class Produto extends Modelo
{
    protected static function arquivo(): string { return 'produtos'; }

    public static function validar(array $post): array
    {
        $nome  = self::texto($post, 'nome', 'Nome', true, 100);
        $peso  = lerNumero($post['peso'] ?? 0);
        $valor = lerNumero($post['valor'] ?? 0);
        if ($peso < 0 || $valor < 0) {
            throw new InvalidArgumentException('Peso e valor não podem ser negativos.');
        }
        return [
            'nome'      => $nome,
            'descricao' => self::texto($post, 'descricao', 'Descrição', false, 255),
            'peso'      => $peso,
            'valor'     => $valor,
        ];
    }

    public static function rotulo(array $p): string
    {
        return $p['nome'];
    }

    protected static function verificarUso(int $id): void
    {
        foreach (Frete::todos() as $f) {
            if (in_array($id, $f['produtos'], true)) {
                throw new InvalidArgumentException('Não é possível excluir: este produto está em um frete.');
            }
        }
    }
}
