/**
 * assets/js/app.js – JavaScript comum a todas as telas.
 * Máscaras de digitação: campos com data-mascara="cep" | "cnpj" | "telefone".
 */
const mascaras = {
  cep:      v => v.replace(/\D/g, '').slice(0, 8).replace(/^(\d{5})(\d)/, '$1-$2'),
  cnpj:     v => v.replace(/\D/g, '').slice(0, 14)
                   .replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
                   .replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2'),
  telefone: v => { v = v.replace(/\D/g, '').slice(0, 11);
                   return v.length > 10 ? v.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3')
                                        : v.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3'); }
};
document.querySelectorAll('[data-mascara]').forEach(campo => {
  const f = mascaras[campo.dataset.mascara];
  if (f) campo.addEventListener('input', () => { campo.value = f(campo.value); });
});
