<?php
/**
 * src/Exemplo.php – carrega alguns cadastros de exemplo (produtos, endereços e transportadores)
 * para você testar e gravar o vídeo sem digitar tudo. Não cria frete: o frete você gera na tela.
 */
class Exemplo
{
    public static function carregar(): void
    {
        $produtos = [
            ['nome' => 'Caixa de parafusos', 'descricao' => 'Caixa com 500 unidades', 'peso' => '4,5', 'valor' => '89,90'],
            ['nome' => 'Furadeira industrial', 'descricao' => 'Furadeira 750W', 'peso' => '3,2', 'valor' => '459,00'],
            ['nome' => 'Rolo de cabo elétrico', 'descricao' => 'Rolo de 100 metros', 'peso' => '12', 'valor' => '320,00'],
            ['nome' => 'Capacete de segurança', 'descricao' => 'Pacote com 10 capacetes', 'peso' => '6', 'valor' => '210,00'],
        ];
        foreach ($produtos as $p) { Produto::salvar($p); }

        $enderecos = [
            ['apelido' => 'Depósito Central', 'cep' => '85900-000', 'logradouro' => 'Avenida Industrial', 'numero' => '1200', 'complemento' => 'Galpão 3', 'bairro' => 'Distrito Industrial', 'cidade' => 'Toledo', 'uf' => 'PR'],
            ['apelido' => 'Loja Centro', 'cep' => '80010-000', 'logradouro' => 'Rua XV de Novembro', 'numero' => '450', 'complemento' => '', 'bairro' => 'Centro', 'cidade' => 'Curitiba', 'uf' => 'PR'],
            ['apelido' => 'Filial Sul', 'cep' => '89801-000', 'logradouro' => 'Rua Marechal Deodoro', 'numero' => '88', 'complemento' => 'Sala 2', 'bairro' => 'Centro', 'cidade' => 'Chapecó', 'uf' => 'SC'],
        ];
        foreach ($enderecos as $e) { Endereco::salvar($e); }

        $transportadores = [
            ['nome' => 'Rápido Sul Transportes', 'cnpj' => '12.345.678/0001-90', 'telefone' => '(45) 3000-1000', 'email' => 'contato@rapidosul.com', 'placa' => 'ABC1D23'],
            ['nome' => 'TransLog Express', 'cnpj' => '98.765.432/0001-10', 'telefone' => '(41) 99999-2000', 'email' => 'vendas@translog.com', 'placa' => 'XYZ9K87'],
        ];
        foreach ($transportadores as $t) { Transportador::salvar($t); }
    }
}
