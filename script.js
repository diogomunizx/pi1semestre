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

function toggleDropdown() {
    const dropdown = document.getElementById("dropdown-menu");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

// Fecha o menu ao clicar fora dele
document.addEventListener("click", function (event) {
    const profile = document.querySelector(".user-profile");
    const dropdown = document.getElementById("dropdown-menu");
    if (!profile.contains(event.target)) {
        dropdown.style.display = "none";
    }
});


// Função para abrir o modal
function verJustificativa() {
    // Cria o elemento de overlay e modal
    const modalOverlay = document.createElement('div');
    modalOverlay.classList.add('modal-overlay');
    
    const modal = document.createElement('div');
    modal.classList.add('modal');
  
    // HTML do conteúdo do modal
    modal.innerHTML = `
      <label>Justificativa:</label>
      <p>Coordenador ainda não visualizou sua inscrição.</p>
      <span class="modal-close" onclick="fecharModal()">Fechar</span>
    `;
  
    // Adiciona o modal dentro do overlay e exibe na página
    modalOverlay.appendChild(modal);
    document.body.appendChild(modalOverlay);
  
    // Exibe o modal
    modalOverlay.style.display = 'flex';
  }
  
  // Função para fechar o modal
  function fecharModal() {
    const modalOverlay = document.querySelector('.modal-overlay');
    if (modalOverlay) {
      modalOverlay.remove();
    }
  }
  



