function prepararRelatorio() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaProfessor = document.getElementById("tableProfessor");
    const tabelaCoordenador= document.getElementById("tableCoordenador");
    const tituloCoordenador = document.getElementById("tituloCoordenador");
    const formularioProfessor = document.getElementById("formularioProfessor");
    
    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaProfessor && tabelaCoordenador && formularioProfessor && tituloCoordenador) {
        tabelaProfessor.style.display = "none";   // Esconde 
        tabelaCoordenador.style.display = "none";   // Esconde 
        tituloCoordenador.style.display = "none"; // Esconde 
        formularioProfessor.style.display = "block";   // Exibe o formulário
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}

function deferirRelatorio() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaCoordenador = document.getElementById("tableCoordenador");
    const formularioCoordenador = document.getElementById("formularioCoordenador");
    const tabelaProfessor = document.getElementById("tableProfessor");
    const tituloProfessor = document.getElementById("tituloProfessor");
    
    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaCoordenador && formularioCoordenador && tabelaProfessor && tituloProfessor) {
        tabelaCoordenador.style.display = "none";   // Esconde a tabela de inscrições
        formularioCoordenador.style.display = "block";   // Exibe o formulário de edição
        tabelaProfessor.style.display = "none";   // Esconde 
        tituloProfessor.style.display = "none"; // Esconde 
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}

function editarInscricao() {
    // Seleciona a tabela e o formulário de edição pelo ID ou classe específicos
    const tabelaInscricoes = document.getElementById("tableInscricao");
    const formularioEditar = document.getElementById("formulario-editar");
    const tituloInscricao = document.getElementById("tituloInscricao");
    
    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaInscricoes && formularioEditar && tituloInscricao ) {
        tabelaInscricoes.style.display = "none";   // Esconde a tabela de inscrições
        formularioEditar.style.display = "block";   // Exibe o formulário de edição
        tituloInscricao.style.display = "none";
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}

