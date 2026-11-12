<?php
/** src/Transportador.php – quem leva a carga: nome, CNPJ, telefone, e-mail e placa do veículo. */
class Transportador extends Modelo
{
    protected static function arquivo(): string { return 'transportadores'; }

    public static function validar(array $post): array
    {
        $email = self::texto($post, 'email', 'E-mail', false, 120);
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Informe um e-mail válido.');
        }
        return [
            'nome'     => self::texto($post, 'nome', 'Nome', true, 100),
            'cnpj'     => self::texto($post, 'cnpj', 'CNPJ', false, 18),
            'telefone' => self::texto($post, 'telefone', 'Telefone', false, 15),
            'email'    => $email,
            'placa'    => strtoupper(self::texto($post, 'placa', 'Placa do veículo', false, 8)),
        ];
    }

    public static function rotulo(array $t): string
    {
        return $t['nome'] . ($t['placa'] !== '' ? ' (' . $t['placa'] . ')' : '');
    }

    protected static function verificarUso(int $id): void
    {
        foreach (Frete::todos() as $f) {
            if ($f['transportador_id'] === $id) {
                throw new InvalidArgumentException('Não é possível excluir: este transportador tem fretes.');
            }
        }
    }
}
