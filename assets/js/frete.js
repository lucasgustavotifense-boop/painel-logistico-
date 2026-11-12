/**
 * assets/js/frete.js – comportamento dinâmico da tela "Gerar frete".
 *  - "+ Adicionar produto" cria uma nova linha (copia o <template>);
 *  - "Remover" apaga a linha;
 *  - o mesmo produto não pode ser escolhido duas vezes (as opções já usadas ficam desabilitadas);
 *  - mostra o resumo (quantidade, peso e valor) e confere o formulário antes de enviar.
 */
const linhas = document.getElementById('linhas');
const modelo = document.getElementById('modelo-linha');
const resumo = document.getElementById('resumo');
const form   = document.getElementById('form-frete');
const caixaErro = document.getElementById('erro-js');

const selects = () => [...linhas.querySelectorAll('select')];
const brl = n => n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

function atualizar() {
  const usados = selects().map(s => s.value).filter(Boolean);
  let peso = 0, valor = 0;

  selects().forEach(s => {
    [...s.options].forEach(o => { o.disabled = o.value !== '' && usados.includes(o.value) && s.value !== o.value; });
    const op = s.selectedOptions[0];
    if (op && op.value) { peso += Number(op.dataset.peso); valor += Number(op.dataset.valor); }
  });

  resumo.textContent = usados.length
    ? `${usados.length} produto(s) • peso total ${peso.toLocaleString('pt-BR')} kg • valor dos produtos ${brl(valor)}`
    : 'Nenhum produto escolhido.';
  linhas.querySelectorAll('.btn-remover').forEach(b => b.disabled = selects().length === 1);
}

document.getElementById('btn-add').addEventListener('click', () => {
  linhas.appendChild(modelo.content.cloneNode(true));
  atualizar();
});

linhas.addEventListener('click', ev => {
  const botao = ev.target.closest('.btn-remover');
  if (botao && selects().length > 1) { botao.closest('.linha-produto').remove(); atualizar(); }
});
linhas.addEventListener('change', atualizar);

form.addEventListener('submit', ev => {
  let erro = '';
  if (!selects().some(s => s.value)) erro = 'Escolha pelo menos um produto para o frete.';
  else if (form.origem_id.value && form.origem_id.value === form.destino_id.value) erro = 'Origem e destino precisam ser endereços diferentes.';
  if (erro) {
    ev.preventDefault();
    caixaErro.textContent = erro;
    caixaErro.classList.remove('d-none');
    caixaErro.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
});

atualizar();
