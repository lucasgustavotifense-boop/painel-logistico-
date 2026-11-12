# &#x20;Painel Logístico

Gerenciador logístico desenvolvido em **PHP** para a APO *Projeto Prático* da disciplina **Fundamentos de Programação para Internet** (UNIPAR EAD).

O sistema permite cadastrar **produtos**, **endereços** (origem e destino) e **transportadores**, gerar **fretes** em que um único transportador leva vários produtos e **baixar o relatório** dos fretes de um determinado dia.

## &#x20;Funcionalidades

Cadastro, edição e exclusão de **produtos** (nome, descrição, peso e valor).

Cadastro, edição e exclusão de **endereços** (nome do local, CEP, rua, número, complemento, bairro, cidade e UF), usados como origem e destino.

Cadastro, edição e exclusão de **transportadores** (nome, CNPJ, telefone, e-mail e placa).

**Geração de frete** ligando um transportador, uma origem, um destino e vários produtos (relação 1 para muitos).

Campos adicionados **dinamicamente com JavaScript**: o usuário inclui e remove linhas de produto na tela de frete, e o resumo (quantidade, peso e valor) é atualizado na hora.

**Relatório de fretes do dia** com download em CSV (abre no Excel e no LibreOffice).

Menu de navegação em todas as telas e visual responsivo com Bootstrap.

Máscaras de digitação para CEP, CNPJ e telefone.

## &#x20;Como executar (XAMPP)

**Requisitos:** XAMPP com PHP 7.4 ou superior e conexão com a internet (para carregar o Bootstrap).

1. Copie a pasta painel-logistica para C:\\xampp\\htdocs\\.
2. Abra o **XAMPP Control Panel** e inicie o **Apache**.
3. Acesse no navegador: [http://localhost](http://localhost/painel-logistica/)
4. *(Opcional)* Na página inicial, clique em **Carregar dados de exemplo** para testar mais rápido.

Não é necessário criar banco de dados: os arquivos em `data/` são criados automaticamente no primeiro uso.

## &#x20;Como usar

1. **Produtos:** cadastre o que será transportado.
2. **Endereços:** cadastre os locais de origem e destino.
3. **Transportadores:** cadastre quem fará o transporte.
4. **Novo frete:** escolha a data, o transportador, a origem e o destino e clique em *+ Adicionar produto* quantas vezes precisar. Depois clique em *Gerar frete*.
5. **Fretes:** veja a lista dos fretes gerados.
6. **Relatório:** escolha o dia, clique em *Ver fretes* e depois em *Baixar relatório (CSV)*.

## 

