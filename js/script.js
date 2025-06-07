function prepararRelatorio() {
    // Seleciona a tabela, o título e o formulário de relatório pelo ID correto
    const tabelaProfessor = document.getElementById("tableProfessor");
    const tituloProfessor = document.getElementById("tituloProfessor");
    const formularioProfessor = document.getElementById("formularioProfessor");

    // Verifica se os elementos existem antes de tentar exibir ou ocultar
    if (tabelaProfessor && formularioProfessor && tituloProfessor) {
        tabelaProfessor.style.display = "none";   // Esconde a tabela
        tituloProfessor.style.display = "none";   // Esconde o título
        formularioProfessor.style.display = "block";   // Exibe o formulário
    } else {
        console.error("Erro: Elemento de tabela, título ou formulário não encontrado.");
    }
}

function deferirRelatorio() {
    // Seleciona a tabela, o título e o formulário de relatório pelo ID correto
    const tabelaCoordenador = document.getElementById("tableCoordenador");
    const tituloCoordenador = document.getElementById("tituloCoordenador");
    const formularioCoordenador = document.getElementById("formularioCoordenador");

    // Verifica se os elementos existem antes de tentar exibir ou ocultar
    if (tabelaCoordenador && formularioCoordenador && tituloCoordenador) {
        tabelaCoordenador.style.display = "none";   // Esconde a tabela
        tituloCoordenador.style.display = "none";   // Esconde o título
        formularioCoordenador.style.display = "block";   // Exibe o formulário
    } else {
        console.error("Erro: Elemento de tabela, título ou formulário não encontrado.");
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

// Função para alternar a visibilidade do menu dropdown
function toggleDropdown() {
    const dropdownMenu = document.getElementById('dropdown-menu');
    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
}

// Fechar o dropdown quando clicar fora dele
document.addEventListener('click', function(event) {
    const userProfile = document.querySelector('.user-profile');
    const dropdownMenu = document.getElementById('dropdown-menu');
    
    if (!userProfile.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});

// Função para alterar visualização
function alterarVisualizacao() {
    // Implementar a lógica de alteração de visualização
    console.log('Alterando visualização...');
}

// Função para editar data
function editarData() {
    // Implementar a lógica de edição de data
    console.log('Editando data...');
}

// Função para imprimir linha separada
function imprimirLinhaSeparada(element) {
    const row = element.closest('tr');
    const content = row.innerText;
    const printWindow = window.open('', '', 'height=400,width=800');
    printWindow.document.write('<html><head><title>Impressão</title>');
    printWindow.document.write('</head><body>');
    printWindow.document.write('<h1>HORUS - Impressão</h1>');
    printWindow.document.write('<pre>' + content + '</pre>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Função para selecionar PDF
function selecionarPDF(element) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            // Aqui você pode implementar o upload do arquivo
            console.log('Arquivo selecionado:', file.name);
        }
    };
    input.click();
}

// Função para alterar visualização da tela de cadastro
function alterarVisualizacaoTelaCadastro() {
    // Implementar a lógica de alteração de visualização da tela de cadastro
    console.log('Alterando visualização da tela de cadastro...');
}

// Função para abrir o modal de justificativa
function verJustificativa() {
    document.body.classList.add('modal-active');

    // Remove qualquer modal existente antes de criar um novo
    const modalExistente = document.querySelector('.modal-overlay');
    if (modalExistente) {
        modalExistente.remove();
    }

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
}

// Função para fechar o modal
function fecharModal() {
    const modalOverlay = document.querySelector('.modal-overlay');
    if (modalOverlay) {
        modalOverlay.remove();
        document.body.classList.remove('modal-active');
    }
}

// Fecha o modal ao clicar fora dele
document.addEventListener('click', function(event) {
    const modalOverlay = document.querySelector('.modal-overlay');
    if (modalOverlay && event.target === modalOverlay) {
        fecharModal();
    }
});

function liberarCampoCadastro() {
    const nome = document.getElementById('professor');
    const telefone = document.getElementById('telefone');
    const btnSalvar = document.querySelector('.btn-salvarAlterarCadastro');
    const btnCancelar = document.querySelector('.btn-cancelarAlterarCadastro');
    const campoSenha = document.getElementById('campo-senha');
    const botoesAlterar = document.querySelector('.btns-Alterar');

     document.querySelector('.btn-salvarAlterarCadastro').style.display = 'block';
        document.querySelector('.btn-cancelarAlterarCadastro').style.display = 'block';
        document.querySelector('.btns-Alterar').style.display = 'none';
        
        

    if (nome.disabled) {
        nome.disabled = false;
        telefone.disabled = false;
        btnSalvar.style.display = 'inline-block';
        btnCancelar.style.display = 'inline-block';
        
        botoesAlterar.style.display = 'none';
        campoSenha.style.display = 'block';
    } else {
        alert("Cancele ou conclua a operação atual antes de alterar novamente.");
    }
}

// Função para salvar alterações de cadastro
function salvarAlteracao() {
    const nome = document.getElementById('professor').value;
    const telefone = document.getElementById('telefone').value;
    const id = document.getElementById('matricula').value;

    fetch('../controller/atualizar_perfil.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_Docente: id,
            Nome: nome,
            telefone: telefone
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Erro ao atualizar');
        return response.json();
    })
    .then(data => {
        alert(data.message || 'Dados atualizados com sucesso!');
        location.reload(); // Recarrega para refletir os dados atualizados (opcional)
    })
    .catch(error => {
        console.error('Erro ao salvar:', error);
        alert('Erro ao salvar as alterações.');
    });
}



// Função para cancelar alteração do cadastro
function cancelarAlteracaoCadastro() {
    const nome = document.getElementById('professor');
    const telefone = document.getElementById('telefone');
    const btnSalvar = document.querySelector('.btn-salvarAlterarCadastro');
    const btnCancelar = document.querySelector('.btn-cancelarAlterarCadastro');
    const campoSenha = document.getElementById('campo-senha');
    const botoesAlterar = document.querySelector('.btns-Alterar');
    nome.disabled = true;
    telefone.disabled = true;
    campoSenha.style.display = 'none';
    botoesAlterar.style.display = 'flex';
    btnSalvar.style.display = 'none';
    btnCancelar.style.display = 'none';
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
    const senhaAtual = document.getElementById('password').value;
    const novaSenha = document.getElementById('novaPassword').value;
    const confirmarSenha = document.getElementById('confirmarPassword').value;
    const id = document.getElementById('matricula').value;

    // Validação de campos vazios
    if (!senhaAtual || !novaSenha || !confirmarSenha) {
        alert("Preencha todos os campos de senha.");
        return;
    }

    // Verificação se nova senha e confirmação coincidem
    if (novaSenha !== confirmarSenha) {
        alert("A nova senha e a confirmação não coincidem.");
        return;
    }

    // Envio dos dados ao backend
    fetch('../controller/alterar_senha.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_Docente: id,
            senhaAtual: senhaAtual,
            novaSenha: novaSenha
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Senha alterada com sucesso!");
            cancelarAlteracaoSenha(); // oculta o formulário
        } else {
            alert(data.message || "Erro ao alterar a senha.");
        }
    })
    .catch(error => {
        console.error('Erro ao alterar senha:', error);
        alert("Erro ao alterar a senha.");
    });
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

/* ===================================================
   CONFIGURAÇÕES INICIAIS E VARIÁVEIS GLOBAIS
   =================================================== */
const { DateTime, Interval } = luxon;

// Dias da semana e contadores de linhas por dia
const diasDaSemana = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
const contadores = Object.fromEntries(diasDaSemana.map(dia => [dia, 0]));

// Estado da aplicação
let diaAtualIndex = 0;
let emEdicao = false;

/* ===================================================
   FUNÇÕES DE CONTROLE VISUAL - AO ALTERAR DADOS
   =================================================== */

function aplicarEstiloCentralizado() {
  const gradeSemanal = document.querySelector('.grade-semanal');
  const diaAtual = document.getElementById(diasDaSemana[diaAtualIndex]);
  
  gradeSemanal.style.display = 'flex';
  gradeSemanal.style.justifyContent = 'center';
  gradeSemanal.style.alignItems = 'flex-start';
  gradeSemanal.style.flexWrap = 'wrap';
  
  diaAtual.style.width = '80%';
  diaAtual.style.maxWidth = '600px';
  diaAtual.style.margin = '0 auto';
}

function removerEstiloCentralizado() {
  const gradeSemanal = document.querySelector('.grade-semanal');
  
  gradeSemanal.style.display = '';
  gradeSemanal.style.justifyContent = '';
  gradeSemanal.style.alignItems = '';
  gradeSemanal.style.flexWrap = '';
  
  diasDaSemana.forEach(dia => {
    const elementoDia = document.getElementById(dia);
    if (elementoDia) {
      elementoDia.style.width = '';
      elementoDia.style.maxWidth = '';
      elementoDia.style.margin = '';
    }
  });
}

/* ===================================================
   FUNÇÕES DE GERENCIAMENTO DE HORÁRIOS
   =================================================== */

function adicionarLinha(dia) {
  const container = document.querySelector(`#linhas-${dia}`);
  const linhas = container.querySelectorAll('.linha-horario');

  // Verifica horários incompletos
  for (let linha of linhas) {
    const [inicio, fim] = linha.querySelectorAll('input[type="time"]');
    if (!inicio.value || !fim.value) {
      alert("Preencha todos os horários antes de adicionar uma nova linha.");
      return;
    }
  }

  // Cria nova linha
  contadores[dia]++;
  const num = contadores[dia];

  const novaLinha = document.createElement('div');
  novaLinha.classList.add('linha-horario');
  novaLinha.setAttribute('data-dia', dia);

  novaLinha.innerHTML = `
    <input type="time" name="horario_${dia}_${num}_inicio" onchange="ordenarLinhas('${dia}')">
    <input type="time" name="horario_${dia}_${num}_fim" onchange="ordenarLinhas('${dia}')">
    <select name="origem_${dia}_${num}">
      <option value="fatec">Fatec</option>
      <option value="outra_fatec">Outra Unidade</option>
      <option value="outra_instituicao">Aula Externa</option>
    </select>
    <button type="button" onclick="removerLinha(this)">Remover</button>
  `;

  container.appendChild(novaLinha);
  ordenarLinhas(dia);
}

function removerLinha(botao) {
  if (!emEdicao) return;
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
  verificarDiferencaEntreDias();
}

/* ===================================================
   FUNÇÕES DE VALIDAÇÃO
   =================================================== */

function verificarSobreposicao(dia) {
  const container = document.querySelector(`#linhas-${dia}`);
  const linhas = Array.from(container.querySelectorAll('.linha-horario'));
  const intervalos = [];
  let horasTotais = 0;

  for (let linha of linhas) {
    const [inicioInput, fimInput] = linha.querySelectorAll('input[type="time"]');
    const inicio = inicioInput?.value;
    const fim = fimInput?.value;

    if (!inicio || !fim) continue;

    const inicioTime = DateTime.fromFormat(inicio, "HH:mm");
    const fimTime = DateTime.fromFormat(fim, "HH:mm");

    // Validação básica do horário
    if (!inicioTime.isValid || !fimTime.isValid || fimTime <= inicioTime) {
      alert("Horário inválido ou fim menor/igual ao início.");
      inicioInput.value = "";
      fimInput.value = "";
      return;
    }

    // Verificação de horas totais 
    const duracaoHoras = fimTime.diff(inicioTime, 'hours').hours;
    horasTotais += duracaoHoras;

    if (horasTotais > 8) {
      alert(`Você não pode trabalhar mais que 8 horas por dia. Limite excedido em ${dia}.`);
      inicioInput.value = "";
      fimInput.value = "";
      return;
    }

    // Verificação de sobreposição
    const novoIntervalo = Interval.fromDateTimes(inicioTime, fimTime);
    for (let intervalo of intervalos) {
      if (novoIntervalo.overlaps(intervalo)) {
        alert(`Conflito detectado: horários sobrepostos em ${dia}.`);
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
    
    // Não verifica de sábado para segunda
    if (diaAtual === "sabado") continue;

    // Obtém containers e linhas
    const containerAtual = document.querySelector(`#linhas-${diaAtual}`);
    const containerSeguinte = document.querySelector(`#linhas-${diaSeguinte}`);
    const linhasAtual = Array.from(containerAtual.querySelectorAll('.linha-horario'));
    const linhasSeguinte = Array.from(containerSeguinte.querySelectorAll('.linha-horario'));

    if (linhasAtual.length === 0 || linhasSeguinte.length === 0) continue;

    // Encontra o último horário do dia atual
    const fimMaisTarde = linhasAtual.reduce((ultimo, linha) => {
      const fim = linha.querySelectorAll('input[type="time"]')[1]?.value;
      const fimTime = DateTime.fromFormat(fim, "HH:mm");
      return (!ultimo || fimTime > ultimo) ? fimTime : ultimo;
    }, null);

    // Encontra o primeiro horário do dia seguinte
    const inicioMaisCedo = linhasSeguinte.reduce((primeiro, linha) => {
      const inicio = linha.querySelectorAll('input[type="time"]')[0]?.value;
      const inicioTime = DateTime.fromFormat(inicio, "HH:mm");
      return (!primeiro || inicioTime < primeiro) ? inicioTime : primeiro;
    }, null);

    if (fimMaisTarde && inicioMaisCedo) {
      const fimComData = DateTime.fromObject({ hour: fimMaisTarde.hour, minute: fimMaisTarde.minute });
      const inicioComData = DateTime.fromObject({ 
        hour: inicioMaisCedo.hour, 
        minute: inicioMaisCedo.minute 
      }).plus({ days: 1 });
      
      const diff = inicioComData.diff(fimComData, 'hours').hours;

      // Verifica intervalo mínimo de 11 horas
      if (diff < 11) {
        alert(`Você precisa ter um intervalo mínimo de 11 horas de descanso entre ${diaAtual} e ${diaSeguinte}.`);

        // Remove o horário problemático
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

/* ===================================================
   FUNÇÕES DE CONTROLE DE EDIÇÃO
   =================================================== */

function inicializarContadores() {
  diasDaSemana.forEach(dia => {
    const container = document.querySelector(`#linhas-${dia}`);
    const linhas = Array.from(container.querySelectorAll('.linha-horario'));
    let maxIndex = -1;

    for (let linha of linhas) {
      const inputs = linha.querySelectorAll('input[type="time"]');
      for (let input of inputs) {
        const match = input.name.match(new RegExp(`horario_${dia}_(\\d+)_inicio`));
        if (match) {
          const index = parseInt(match[1]);
          if (index > maxIndex) maxIndex = index;
        }
      }
    }

    contadores[dia] = maxIndex + 1;
  });
}

function habilitarEdicao() {
  emEdicao = true;
  diaAtualIndex = 0;
  
  // Configura estado inicial da edição
  document.querySelectorAll('.dia').forEach(div => div.style.display = 'none');
  document.querySelectorAll('.botao-adicionar').forEach(btn => btn.style.display = 'none');
  document.querySelectorAll('input, select').forEach(el => el.disabled = false);
  document.querySelectorAll('.linha-horario button').forEach(botao => {
    botao.style.display = 'inline-block';
  });

  // Mostra apenas o dia atual
  const diaAtual = diasDaSemana[diaAtualIndex];
  document.getElementById(diaAtual).style.display = 'block';
  document.querySelector(`#${diaAtual} .botao-adicionar`).style.display = 'inline-block';

  // Configura botões
  const botaoAlterar = document.getElementById('alterar-dados');
  botaoAlterar.textContent = 'Avançar';
  botaoAlterar.onclick = avancarDia;
  document.getElementById('retornar-dia').style.display = 'none';
  
  aplicarEstiloCentralizado();
}

function avancarDia() {
  if (!emEdicao) return;

  const diaAtual = diasDaSemana[diaAtualIndex];

  if (existeHorarioIncompleto(diaAtual)) {
    alert("Por favor, preencha todos os horários antes de continuar.");
    return;
  }

  // Esconde dia atual
  document.getElementById(diaAtual).style.display = 'none';
  document.querySelector(`#${diaAtual} .botao-adicionar`).style.display = 'none';
  
  diaAtualIndex++;

  // Atualiza interface
  if (diaAtualIndex > 0) {
    document.getElementById('retornar-dia').style.display = 'inline-block';
  }

  if (diaAtualIndex < diasDaSemana.length) {
    const proximoDia = diasDaSemana[diaAtualIndex];
    document.getElementById(proximoDia).style.display = 'block';
    document.querySelector(`#${proximoDia} .botao-adicionar`).style.display = 'inline-block';
    aplicarEstiloCentralizado();
  }

  // Atualiza botão principal
  if (diaAtualIndex === diasDaSemana.length - 1) {
    document.getElementById('alterar-dados').textContent = 'Salvar alterações';
  }

  if (diaAtualIndex === diasDaSemana.length) {
    salvarAlteracoes();
  }
}

function retornarDia() {
  if (!emEdicao || diaAtualIndex <= 0) return;

  const diaAtual = diasDaSemana[diaAtualIndex];

  if (existeHorarioIncompleto(diaAtual)) {
    alert("Por favor, preencha todos os horários antes de retornar.");
    return;
  }

  // Esconde dia atual
  document.getElementById(diaAtual).style.display = 'none';
  document.querySelector(`#${diaAtual} .botao-adicionar`).style.display = 'none';
  
  diaAtualIndex--;

  // Mostra dia anterior
  const diaAnterior = diasDaSemana[diaAtualIndex];
  document.getElementById(diaAnterior).style.display = 'block';
  document.querySelector(`#${diaAnterior} .botao-adicionar`).style.display = 'inline-block';

  // Atualiza botões
  document.getElementById('alterar-dados').textContent = 'Avançar';
  if (diaAtualIndex === 0) {
    document.getElementById('retornar-dia').style.display = 'none';
  }
  
  aplicarEstiloCentralizado();
}

function existeHorarioIncompleto(dia) {
  const container = document.querySelector(`#linhas-${dia}`);
  const linhas = Array.from(container.querySelectorAll('.linha-horario'));

  return linhas.some(linha => {
    const inputs = linha.querySelectorAll('input[type="time"]');
    const inicio = inputs[0]?.value;
    const fim = inputs[1]?.value;
    return !inicio || !fim;
  });
}

/* ===================================================
   FUNÇÕES DE PERSISTÊNCIA (COM REDIRECIONAMENTO FIXO)
   =================================================== */

// Placeholder para salvar alterações, ajuste conforme sua lógica
async function salvarAlteracoes() {
  const ultimoDia = diasDaSemana[diasDaSemana.length - 1];

  // 1. Verifica se há horário incompleto no último dia
  if (existeHorarioIncompleto(ultimoDia)) {
    alert("Por favor, preencha todos os horários antes de salvar.");
    diaAtualIndex = diasDaSemana.length - 1;

    document.querySelectorAll('.dia').forEach(div => div.style.display = 'none');
    document.getElementById(ultimoDia).style.display = 'block';
    document.querySelector(`#${ultimoDia} .botao-adicionar`).style.display = 'inline-block';

    document.getElementById('alterar-dados').textContent = 'Salvar alterações';
    document.getElementById('retornar-dia').style.display = 'inline-block';
    return;
  }

  // 2. Mapeamento e coleta dos dados da grade semanal
  const mapaDias = {
    'segunda': 1,
    'terca': 2,
    'quarta': 3,
    'quinta': 4,
    'sexta': 5,
    'sabado': 6
  };

  const dadosParaSalvar = [];

  for (const [diaNome, diaNumero] of Object.entries(mapaDias)) {
    const container = document.querySelector(`#linhas-${diaNome}`);
    const linhas = container.querySelectorAll('.linha-horario');

    linhas.forEach(linha => {
      const [inicioInput, fimInput] = linha.querySelectorAll('input[type="time"]');
      const selectInstituicao = linha.querySelector('select');

      if (!inicioInput || !fimInput || !selectInstituicao) return;

      const horarioInicio = inicioInput.value;
      const horarioFim = fimInput.value;
      const instituicao = selectInstituicao.value;

      if (horarioInicio && horarioFim && instituicao) {
        dadosParaSalvar.push({
          diaSemana: diaNumero,
          horarioInicio,
          horarioFim,
          instituicao
        });
      }
    });
  }

  // 3. Envia os dados via fetch para salvar_horarios.php
  try {
    const resposta = await fetch('../controller/salvar_horarios.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ horarios: dadosParaSalvar })
    });

    const resultado = await resposta.json();

    if (resultado.sucesso) {
      alert("Horários salvos com sucesso!");
      location.reload();
    } else {
      alert("Erro ao salvar: " + resultado.mensagem);
    }
  } catch (error) {
    console.error("Erro ao salvar horários:", error);
    alert("Erro ao salvar os dados. Verifique sua conexão ou tente novamente.");
    return;
    
  }

  // 4. Retorna ao modo visualização
  emEdicao = false;

  document.querySelectorAll('.dia').forEach(div => div.style.display = 'block');
  document.querySelectorAll('.botao-adicionar').forEach(btn => btn.style.display = 'none');

  document.querySelectorAll('input, select').forEach(el => el.disabled = true);
  document.querySelectorAll('.linha-horario button').forEach(botao => {
    botao.style.display = 'none';
  });

  document.getElementById('retornar-dia').style.display = 'none';

  const botaoAlterar = document.getElementById('alterar-dados');
  botaoAlterar.textContent = 'Alterar dados';
  botaoAlterar.onclick = habilitarEdicao;
}
z


/* ===================================================
   INICIALIZAÇÃO
   =================================================== */

async function carregarHorariosSalvos() {
  try {
    const resposta = await fetch('buscar_horario.php');
    if (!resposta.ok) throw new Error("Erro ao carregar os horários");

    const dados = await resposta.json();

    // Mapeia 1 a 6 para 'segunda' a 'sabado'
    const mapaDias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
    const horariosPorDia = {};

    // Agrupa os horários por dia
    dados.forEach(item => {
      const nomeDia = mapaDias[item.diaSemana - 1]; // Subtrai 1 porque começa no índice 0
      if (!horariosPorDia[nomeDia]) horariosPorDia[nomeDia] = [];
      horariosPorDia[nomeDia].push(item);
    });

    // Preenche os campos com os dados recebidos
    Object.entries(horariosPorDia).forEach(([dia, horarios]) => {
      horarios.forEach(horario => {
        adicionarLinha(dia); // Cria a linha

        const container = document.querySelector(`#linhas-${dia}`);
        const linhas = container.querySelectorAll('.linha-horario');
        const ultimaLinha = linhas[linhas.length - 1];

        const [inputInicio, inputFim] = ultimaLinha.querySelectorAll('input[type="time"]');
        const selectOrigem = ultimaLinha.querySelector('select');

        inputInicio.value = horario.horarioInicio.substring(0, 5);
        inputFim.value = horario.horarioFim.substring(0, 5);

        // Define valor do select
        switch ((horario.instituicao || '').toLowerCase()) {
          case 'fatec':
          case '':
            selectOrigem.value = 'fatec';
            break;
          case 'outra unidade':
          case 'outra_fatec':
            selectOrigem.value = 'outra_fatec';
            break;
          default:
            selectOrigem.value = 'outra_instituicao';
            break;
        }
      });
    });

  } catch (erro) {
    console.error("Erro ao carregar horários:", erro);
  }
}
