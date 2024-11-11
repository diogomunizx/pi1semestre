function prepararRelatorio() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaInscricoes = document.querySelector(".tbls");
    const formularioEditar = document.getElementById("formulario-editar");
    
    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaInscricoes && formularioEditar) {
        tabelaInscricoes.style.display = "none";   // Esconde a tabela de inscrições
        formularioEditar.style.display = "block";   // Exibe o formulário de edição
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}

function cancelarEdicao() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaInscricoes = document.querySelector(".tbls");
    const formularioEditar = document.getElementById("formulario-editar");

    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaInscricoes && formularioEditar) {
        tabelaInscricoes.style.display = "table";   // Exibe a tabela de inscrições
        formularioEditar.style.display = "none";    // Esconde o formulário de edição
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}
