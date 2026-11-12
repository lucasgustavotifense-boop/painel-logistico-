<?php
/**
 * src/Modelo.php
 * Classe-base das entidades (Produto, Endereco, Transportador, Frete).
 * Já traz listar, buscar, salvar e excluir. Cada entidade só diz:
 *  - em qual arquivo guarda (arquivo()),
 *  - como validar o que veio do formulário (validar()),
 *  - se pode ser excluída (verificarUso()).
 */
abstract class Modelo
{
    abstract protected static function arquivo(): string;

    /** Recebe o $_POST, limpa e confere os dados. Lança InvalidArgumentException com a mensagem do erro. */
    abstract public static function validar(array $post): array;

    /** Texto curto usado nas listas de escolha (selects). */
    abstract public static function rotulo(array $registro): string;

    protected static function repo(): Armazenamento
    {
        return new Armazenamento(static::arquivo());
    }

    public static function todos(): array
    {
        $lista = static::repo()->todos();
        usort($lista, fn($a, $b) => $a['id'] <=> $b['id']);
        return $lista;
    }

    public static function buscar(int $id): ?array
    {
        return static::repo()->buscar($id);
    }

    public static function salvar(array $post): array
    {
        $dados = static::validar($post);
        $id = (int)($post['id'] ?? 0);
        if ($id > 0) {
            if (!static::buscar($id)) {
                throw new InvalidArgumentException('Registro não encontrado.');
            }
            $dados['id'] = $id;
        }
        return static::repo()->salvar($dados);
    }

    public static function excluir(int $id): void
    {
        static::verificarUso($id);
        static::repo()->excluir($id);
    }

    /** Sobrescreva para impedir a exclusão quando o registro já está em algum frete. */
    protected static function verificarUso(int $id): void
    {
    }

    /** Pega um campo de texto do formulário já sem espaços; se for obrigatório e estiver vazio, dá erro. */
    protected static function texto(array $post, string $campo, string $rotulo, bool $obrigatorio = false, int $max = 120): string
    {
        $v = trim((string)($post[$campo] ?? ''));
        if ($obrigatorio && $v === '') {
            throw new InvalidArgumentException("Preencha o campo \"$rotulo\".");
        }
        if (mb_strlen($v) > $max) {
            throw new InvalidArgumentException("O campo \"$rotulo\" aceita até $max caracteres.");
        }
        return $v;
    }
}
