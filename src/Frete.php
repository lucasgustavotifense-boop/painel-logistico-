<?php
/**
 * src/Frete.php – o FRETE liga tudo:
 *   1 transportador  +  1 endereço de origem  +  1 endereço de destino  +  VÁRIOS produtos.
 * Ou seja, um transportador pode levar vários produtos (relação 1 para muitos).
 * No arquivo guardamos só os ids; os nomes são buscados na hora de mostrar (completos()).
 */
class Frete extends Modelo
{
    protected static function arquivo(): string { return 'fretes'; }

    public static function validar(array $post): array
    {
        $data = trim((string)($post['data'] ?? ''));
        $dt = DateTime::createFromFormat('Y-m-d', $data);
        if (!$dt || $dt->format('Y-m-d') !== $data) {
            throw new InvalidArgumentException('Informe uma data válida para o frete.');
        }

        $transp  = (int)($post['transportador_id'] ?? 0);
        $origem  = (int)($post['origem_id'] ?? 0);
        $destino = (int)($post['destino_id'] ?? 0);
        if (!Transportador::buscar($transp)) {
            throw new InvalidArgumentException('Escolha um transportador.');
        }
        if (!Endereco::buscar($origem) || !Endereco::buscar($destino)) {
            throw new InvalidArgumentException('Escolha o endereço de origem e o de destino.');
        }
        if ($origem === $destino) {
            throw new InvalidArgumentException('Origem e destino precisam ser endereços diferentes.');
        }

        $produtos = array_values(array_unique(array_filter(array_map('intval', (array)($post['produtos'] ?? [])))));
        if (!$produtos) {
            throw new InvalidArgumentException('Inclua pelo menos um produto no frete.');
        }
        foreach ($produtos as $idProduto) {
            if (!Produto::buscar($idProduto)) {
                throw new InvalidArgumentException('Um dos produtos escolhidos não existe mais.');
            }
        }

        $valor = lerNumero($post['valor_frete'] ?? 0);
        if ($valor < 0) {
            throw new InvalidArgumentException('O valor do frete não pode ser negativo.');
        }

        return [
            'data'             => $data,
            'transportador_id' => $transp,
            'origem_id'        => $origem,
            'destino_id'       => $destino,
            'produtos'         => $produtos,
            'valor_frete'      => $valor,
            'observacao'       => self::texto($post, 'observacao', 'Observação', false, 255),
            'criado_em'        => date('Y-m-d H:i:s'),
        ];
    }

    public static function rotulo(array $f): string
    {
        return 'Frete #' . $f['id'];
    }

    /**
     * Devolve os fretes (opcionalmente só de um dia) já com os nomes montados:
     * transportador, origem, destino, lista de produtos, peso total e valor dos produtos.
     */
    public static function completos(?string $data = null): array
    {
        $transp = array_column(Transportador::todos(), null, 'id');
        $ends   = array_column(Endereco::todos(), null, 'id');
        $prods  = array_column(Produto::todos(), null, 'id');

        $saida = [];
        foreach (self::todos() as $f) {
            if ($data !== null && $f['data'] !== $data) {
                continue;
            }
            $lista = array_values(array_filter(array_map(fn($id) => $prods[$id] ?? null, $f['produtos'])));
            $f['transportador'] = isset($transp[$f['transportador_id']]) ? Transportador::rotulo($transp[$f['transportador_id']]) : '(removido)';
            $f['origem']  = isset($ends[$f['origem_id']])  ? Endereco::rotulo($ends[$f['origem_id']])   : '(removido)';
            $f['destino'] = isset($ends[$f['destino_id']]) ? Endereco::rotulo($ends[$f['destino_id']]) : '(removido)';
            $f['origem_completo']  = isset($ends[$f['origem_id']])  ? Endereco::completo($ends[$f['origem_id']])  : '';
            $f['destino_completo'] = isset($ends[$f['destino_id']]) ? Endereco::completo($ends[$f['destino_id']]) : '';
            $f['lista_produtos']   = $lista;
            $f['peso_total']       = array_sum(array_column($lista, 'peso'));
            $f['valor_produtos']   = array_sum(array_column($lista, 'valor'));
            $saida[] = $f;
        }
        return $saida;
    }
}
