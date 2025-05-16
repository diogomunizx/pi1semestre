function prepararRelatorio() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaProfessor = document.getElementById("tableProfessor");
    const formularioProfessor = document.getElementById("formularioProfessor");

    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaProfessor && formularioProfessor) {
        tabelaProfessor.style.display = "none";   // Esconde 
        formularioProfessor.style.display = "block";   // Exibe o formulário
    } else {
        console.error("Erro: Elemento de tabela ou formulário não encontrado.");
    }
}

function deferirRelatorio() {
    // Seleciona a tabela e o formulário de relatório pelo ID correto
    const tabelaCoordenador = document.getElementById("tableCoordenador");
    const formularioCoordenador = document.getElementById("formularioCoordenador");

    // Verifica se ambos os elementos existem antes de tentar exibir ou ocultar
    if (tabelaCoordenador && formularioCoordenador) {
        tabelaCoordenador.style.display = "none";   // Esconde a tabela de inscrições
        formularioCoordenador.style.display = "block";   // Exibe o formulário de edição
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
    if (tabelaInscricoes && formularioEditar && tituloInscricao) {
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

    document.body.classList.add('modal-active');

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

function editarData() {

    document.body.classList.add('modal-active');

    // Cria o elemento de overlay e modal
    const modalOverlay = document.createElement('div');
    modalOverlay.classList.add('modal-overlay');

    const modal = document.createElement('div');
    modal.classList.add('modal');

    // HTML do conteúdo do modal
    modal.innerHTML = `
      <label for="cronograma">Data início:</label>
      <input type="date" id="cronograma" name="cronograma"><br>
      <br>
      <label for="cronograma">Data final:</label>
      <input type="date" id="cronograma" name="cronograma"><br>
      <span class="modal-close" onclick="fecharModal()">Alterar</span>
    `;

    // Adiciona o modal dentro do overlay e exibe na página
    modalOverlay.appendChild(modal);
    document.body.appendChild(modalOverlay);

    // Exibe o modal
    modalOverlay.style.display = 'flex';
}

function liberarCampoCadastro() {
    const nome = document.getElementById('professor');
    const email = document.getElementById('email');
    const telefone = document.getElementById('telefone');
    const btnSalvar = document.querySelector('.btn-salvarAlterarCadastro');
    const btnCancelar = document.querySelector('.btn-cancelarAlterarCadastro');
    const campoSenha = document.getElementById('campo-senha');
    const botoesAlterar = document.querySelector('.btns-Alterar');

    if (nome.disabled) {
        nome.disabled = false;
        email.disabled = false;
        telefone.disabled = false;
        campoSenha.style.display = 'block';
        btnSalvar.style.display = 'inline-block';
        btnCancelar.style.display = 'inline-block';
        botoesAlterar.style.display = 'none';
    } else {
        alert("Cancele ou conclua a operação atual antes de alterar novamente.");
    }
}

// Função para salvar alterações de cadastro
function salvarAlteracao() {
    const senha = document.getElementById('senha').value;

    if (senha.trim() === "") {
        alert("Por favor, informe sua senha para confirmar as alterações.");
        return;
    }

    // Aqui você enviaria as alterações para o backend

    alert("Cadastro alterado com sucesso!");
    document.getElementById('senha').value = '';
    cancelarAlteracaoCadastro();
}

// Função para cancelar alteração do cadastro
function cancelarAlteracaoCadastro() {
    const nome = document.getElementById('professor');
    const email = document.getElementById('email');
    const telefone = document.getElementById('telefone');
    const btnSalvar = document.querySelector('.btn-salvarAlterarCadastro');
    const btnCancelar = document.querySelector('.btn-cancelarAlterarCadastro');
    const campoSenha = document.getElementById('campo-senha');
    const botoesAlterar = document.querySelector('.btns-Alterar');

    nome.disabled = true;
    email.disabled = true;
    telefone.disabled = true;
    campoSenha.style.display = 'none';
    btnSalvar.style.display = 'none';
    btnCancelar.style.display = 'none';
    botoesAlterar.style.display = 'block';
    document.getElementById('senha').value = '';
}

// Função para abrir o formulário de alteração de senha
function liberarFormAlterarSenha() {
    const formCadastro = document.querySelector('.form-ajuste');
    const formSenha = document.querySelector('.form-AlterarSenha');

    formCadastro.style.display = 'none';
    formSenha.style.display = 'block';
}

// Função para salvar alteração de senha
function salvarAlteracaoSenha() {
    const password = document.getElementById('password').value;
    const novaPassword = document.getElementById('novaPassword').value;
    const confirmarPassword = document.getElementById('confirmarPassword').value;

    if (!password || !novaPassword || !confirmarPassword) {
        alert("Preencha todos os campos de senha.");
        return;
    }

    if (novaPassword !== confirmarPassword) {
        alert("A nova senha e a confirmação não coincidem.");
        return;
    }

    // Aqui você enviaria as senhas para o backend para validação e atualização

    alert("Senha alterada com sucesso!");
    cancelarAlteracaoSenha();
}

// Função para cancelar alteração de senha
function cancelarAlteracaoSenha() {
    const password = document.getElementById('password');
    const novaPassword = document.getElementById('novaPassword');
    const confirmarPassword = document.getElementById('confirmarPassword');
    const formCadastro = document.querySelector('.form-ajuste');
    const formSenha = document.querySelector('.form-AlterarSenha');

    password.value = '';
    novaPassword.value = '';
    confirmarPassword.value = '';

    formCadastro.style.display = 'block';
    formSenha.style.display = 'none';
}


// Seleciona todos os inputs dentro das divs com classe 'hae-projeto-linha'
const haeInputs = document.querySelectorAll('.hae-projeto-linha input');

// Adiciona um evento a cada input
haeInputs.forEach(input => {
    input.addEventListener('input', () => {
        // Limpa os outros inputs e adiciona a classe 'input-dimmed' neles
        haeInputs.forEach(otherInput => {
            if (otherInput !== input) {
                otherInput.value = ''; // Limpa o valor
                otherInput.classList.add('input-dimmed'); // Aplica o estilo de destaque claro
            } else {
                otherInput.classList.remove('input-dimmed'); // Remove o estilo do input preenchido
            }
        });

        // Garante que o valor do input não ultrapasse o máximo permitido
        if (parseInt(input.value, 10) > 12) {
            input.value = 12; // Ajusta automaticamente para 12 se ultrapassar
        }
    });

    // Remove o estilo ao focar novamente no input
    input.addEventListener('focus', () => {
        haeInputs.forEach(otherInput => {
            otherInput.classList.remove('input-dimmed');
        });
    });
});

// Seleciona todas as textareas com a classe textarea-auto-ajuste
const textareas = document.querySelectorAll('.textarea-auto-ajuste');

// Adiciona um evento para ajustar a altura ao digitar
textareas.forEach(textarea => {
    textarea.addEventListener('input', () => {
        textarea.style.height = 'auto'; // Reseta a altura
        textarea.style.height = textarea.scrollHeight + 'px'; // Ajusta para o conteúdo
    });
});

function verificarParcial() {
    const status = document.getElementById("status").value;
    const haeQuantidade = document.getElementById("hae-quantidade");

    // Exibe o campo de H.A.E. apenas se a opção "Parcial" for selecionada
    haeQuantidade.style.display = (status === "Parcial") ? "block" : "none";
}

document.addEventListener('DOMContentLoaded', function () {
    flatpickr(".hora-inteira", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        minTime: "07:00",
        maxTime: "22:00",
        minuteIncrement: 60,
        onChange: function(selectedDates, dateStr, instance) {
            // Dá um pequeno tempo (400ms) antes de fechar
            setTimeout(() => instance.close(), 3000);
        }
    });
});

// Function Imprimir
function imprimirInscricao() {
    const linha = event.target.closest("tr");
    const colunas = linha.querySelectorAll("td");

    const dados = `
        <div style="font-family: Arial, sans-serif; padding: 20px;">
            <h2 style="text-align: center;">Resumo da Inscrição</h2>
            <p><strong>Inscrição:</strong> ${colunas[0].innerText}</p>
            <p><strong>Professor/Coordenador:</strong> ${colunas[1].innerText}</p>
            <p><strong>Projeto:</strong> ${colunas[2].innerText}</p>
            <p><strong>Tipo HAE:</strong> ${colunas[3].innerText}</p>
            <p><strong>Quantidade HAE:</strong> ${colunas[4].innerText}</p>
            <p><strong>Status:</strong> ${colunas[5].innerText}</p>
        </div>
    `;

    const janela = window.open('', '_blank', 'width=800,height=600');
    janela.document.write(`
        <html>
            <head>
                <title>Impressão de Inscrição</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 40px;
                        text-align: center;
                    }
                    h1 {
                        font-size: 24px;
                        margin-bottom: 10px;
                    }
                    p {
                        font-size: 18px;
                    }
                </style>
            </head>
            <body>
                <h1>Título Imprimir</h1>
                <p>Está sendo processado...</p>
            </body>
        </html>
    `);

    janela.document.close();

    // Aguarda um curto período para exibir a mensagem antes de imprimir o conteúdo final
    setTimeout(() => {
        janela.document.body.innerHTML = dados;
        janela.print();
        janela.close();
    }, 1000);
}

const { DateTime, Interval } = luxon;

const diasDaSemana = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
const contadores = Object.fromEntries(diasDaSemana.map(dia => [dia, 0]));

function adicionarLinha(dia) {
  contadores[dia]++;
  const num = contadores[dia];
  const container = document.querySelector(`#linhas-${dia}`);

  const novaLinha = document.createElement('div');
  novaLinha.classList.add('linha-horario');
  novaLinha.setAttribute('data-dia', dia);

  novaLinha.innerHTML = `
    <input type="time" name="horario_${dia}_${num}_inicio" onchange="ordenarLinhas('${dia}')">
    <input type="time" name="horario_${dia}_${num}_fim" onchange="ordenarLinhas('${dia}')">
    <select name="origem_${dia}_${num}">
      <option value="fatec">Fatec</option>
      <option value="outra_fatec">Aula em outra Fatec</option>
      <option value="outra_instituicao">Aula em outra instituição</option>
    </select>
    <button type="button" onclick="removerLinha(this)">Remover</button>
  `;

  container.appendChild(novaLinha);
  ordenarLinhas(dia);
}

function removerLinha(botao) {
  const linha = botao.closest('.linha-horario');
  linha.remove();
}

function ordenarLinhas(dia) {
  const container = document.querySelector(`#linhas-${dia}`);
  const linhas = Array.from(container.querySelectorAll('.linha-horario'));

  linhas.sort((a, b) => {
    const aVal = a.querySelector('input[type="time"]')?.value || "99:99";
    const bVal = b.querySelector('input[type="time"]')?.value || "99:99";
    return aVal.localeCompare(bVal);
  });

  linhas.forEach(l => container.appendChild(l));

  verificarSobreposicao(dia);
  verificarDiferencaEntreDias(); // Novo
}

function verificarSobreposicao(dia) {
  const container = document.querySelector(`#linhas-${dia}`);
  const linhas = Array.from(container.querySelectorAll('.linha-horario'));

  const intervalos = [];

  for (let linha of linhas) {
    const inputs = linha.querySelectorAll('input[type="time"]');
    const inicioInput = inputs[0];
    const fimInput = inputs[1];

    const inicio = inicioInput?.value;
    const fim = fimInput?.value;

    if (!inicio || !fim) continue;

    const inicioTime = DateTime.fromFormat(inicio, "HH:mm");
    const fimTime = DateTime.fromFormat(fim, "HH:mm");

    if (!inicioTime.isValid || !fimTime.isValid || fimTime <= inicioTime) {
      alert("Horário inválido ou fim menor/igual ao início.");
      inicioInput.value = "";
      fimInput.value = "";
      return;
    }

    const novoIntervalo = Interval.fromDateTimes(inicioTime, fimTime);

    for (let intervalo of intervalos) {
      if (novoIntervalo.overlaps(intervalo)) {
        alert("Conflito detectado: horários sobrepostos em " + dia + ".");
        inicioInput.value = "";
        fimInput.value = "";
        return;
      }
    }

    intervalos.push(novoIntervalo);
  }
}

function verificarDiferencaEntreDias() {
  for (let i = 0; i < diasDaSemana.length - 1; i++) {
    const diaAtual = diasDaSemana[i];
    const diaSeguinte = diasDaSemana[i + 1];

    // Ignora sábado para segunda
    if (diaAtual === "sabado") continue;

    const containerAtual = document.querySelector(`#linhas-${diaAtual}`);
    const containerSeguinte = document.querySelector(`#linhas-${diaSeguinte}`);

    const linhasAtual = Array.from(containerAtual.querySelectorAll('.linha-horario'));
    const linhasSeguinte = Array.from(containerSeguinte.querySelectorAll('.linha-horario'));

    if (linhasAtual.length === 0 || linhasSeguinte.length === 0) continue;

    // Último horário de fim do dia atual
    const fimMaisTarde = linhasAtual.reduce((ultimo, linha) => {
      const fim = linha.querySelectorAll('input[type="time"]')[1]?.value;
      const fimTime = DateTime.fromFormat(fim, "HH:mm");
      return (!ultimo || fimTime > ultimo) ? fimTime : ultimo;
    }, null);

    // Primeiro horário de início do dia seguinte
    const inicioMaisCedo = linhasSeguinte.reduce((primeiro, linha) => {
      const inicio = linha.querySelectorAll('input[type="time"]')[0]?.value;
      const inicioTime = DateTime.fromFormat(inicio, "HH:mm");
      return (!primeiro || inicioTime < primeiro) ? inicioTime : primeiro;
    }, null);

    if (fimMaisTarde && inicioMaisCedo) {
      // Simula continuidade de dias
      const fimComData = DateTime.fromObject({ hour: fimMaisTarde.hour, minute: fimMaisTarde.minute });
      const inicioComData = DateTime.fromObject({ hour: inicioMaisCedo.hour, minute: inicioMaisCedo.minute }).plus({ days: 1 });

      const diff = inicioComData.diff(fimComData, 'hours').hours;

      if (diff < 8) {
        alert(`Conflito entre ${diaAtual} e ${diaSeguinte}: deve haver pelo menos 8 horas de intervalo.`);

        // Limpa o campo do início do dia seguinte
        for (let linha of linhasSeguinte) {
          const inicioInput = linha.querySelectorAll('input[type="time"]')[0];
          const inicioTime = DateTime.fromFormat(inicioInput.value, "HH:mm");
          if (inicioTime.equals(inicioMaisCedo)) {
            inicioInput.value = "";
            break;
          }
        }

        return;
      }
    }
  }
}



//teste