
// JavaScript para alternar a visibilidade da tabela de status

const toggleButtons = document.querySelectorAll('.alterar, .visualizar');
const secondTable = document.getElementById('secondTable');

toggleButtons.forEach(button => {
    button.addEventListener('click', function() {
        if (secondTable.style.display === 'none') {
            secondTable.style.display = 'block';  // Exibe a tabela
            botaonovocad.style.display = 'none';
        } else {
            secondTable.style.display = 'none';  // Esconde a tabela
            botaonovocad.style.display = 'block';
        }
    });
});



