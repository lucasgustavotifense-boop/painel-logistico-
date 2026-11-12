<?php
/**
 * src/Armazenamento.php
 * Guarda e lê os dados em arquivos JSON dentro da pasta data/ (um arquivo por tipo: produtos.json, ...).
 * Assim o projeto funciona no XAMPP sem criar banco de dados.
 * O arquivo é "travado" (flock) enquanto é alterado, para dois acessos não se atropelarem.
 */
class Armazenamento
{
    private string $arquivo;

    public function __construct(string $nome)
    {
        if (!is_dir(DIR_DADOS)) {
            mkdir(DIR_DADOS, 0775, true);
        }
        $this->arquivo = DIR_DADOS . '/' . $nome . '.json';
        if (!is_file($this->arquivo)) {
            file_put_contents($this->arquivo, json_encode(['ultimo_id' => 0, 'itens' => []]));
        }
    }

    /** Lista todos os registros. */
    public function todos(): array
    {
        $dados = json_decode((string)file_get_contents($this->arquivo), true);
        return is_array($dados) ? array_values($dados['itens'] ?? []) : [];
    }

    /** Busca um registro pelo id (ou null). */
    public function buscar(int $id): ?array
    {
        foreach ($this->todos() as $item) {
            if ((int)$item['id'] === $id) {
                return $item;
            }
        }
        return null;
    }

    /** Cria (sem id) ou atualiza (com id) um registro e devolve o registro salvo. */
    public function salvar(array $registro): array
    {
        $this->alterar(function (array &$dados) use (&$registro): void {
            if (empty($registro['id'])) {                       // novo: gera o próximo id
                $registro['id'] = ++$dados['ultimo_id'];
                $dados['itens'][] = $registro;
                return;
            }
            foreach ($dados['itens'] as $i => $item) {          // existente: substitui
                if ((int)$item['id'] === (int)$registro['id']) {
                    $dados['itens'][$i] = $registro;
                    return;
                }
            }
            throw new InvalidArgumentException('Registro não encontrado.');
        });
        return $registro;
    }

    /** Remove um registro pelo id. */
    public function excluir(int $id): void
    {
        $this->alterar(function (array &$dados) use ($id): void {
            $dados['itens'] = array_values(array_filter(
                $dados['itens'],
                fn($item) => (int)$item['id'] !== $id
            ));
        });
    }

    /** Abre o arquivo travado, aplica a mudança e grava de volta. */
    private function alterar(callable $mudanca): void
    {
        $fp = fopen($this->arquivo, 'c+');
        flock($fp, LOCK_EX);
        try {
            $dados = json_decode((string)stream_get_contents($fp), true);
            if (!is_array($dados)) {
                $dados = ['ultimo_id' => 0, 'itens' => []];
            }
            $mudanca($dados);
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            fflush($fp);
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}
