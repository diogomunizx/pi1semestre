<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
  header("Location: ../login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../estilos/style.css">
  <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
  <title>HORUS - Minhas Aulas</title>
</head>

<body>
  <header>
    <div class="header-content">
      <div class="user-profile" onclick="toggleDropdown()">
        <span><?php echo htmlspecialchars($_SESSION['Nome'][0]); ?></span>
        <div class="dropdown-menu" id="dropdown-menu">
          <a href="perfil_cadastro.php">Ajustes</a>
          <a href="perfil_Aulas.php">Minhas Aulas</a>
        </div>
      </div>
      <div class="institutions">
        <div class="fatec">
          <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-fatec_itapira.png"></a>
        </div>
        <div class="cps">
          <a href="https://www.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-cps.png"></a>
        </div>
      </div>
    </div>
  </header>

  <nav class="sidebar">
    <div class="logo-container">
      <a href="#">
        <img src="../imagens/logo-horus.png" alt="Logo HORUS">
      </a>
    </div>
    <?php if (strtolower($_SESSION['funcao']) === 'professor'): ?>
      <a class="inicio" href="index_prof.php">
        <img src="../imagens/home.png" alt="Início"> <span>Início</span>
      </a>
      <a href="inscricao.php" id="linkInscricao">
        <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
      </a>
      <a href="editais.php">
        <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
      </a>
      <a href="relatorio_prof.php">
        <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
      </a>
      <a href="dashboard_professor.php">
        <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
      </a>
    <?php else: ?>
      <a class="inicio" href="index_coord.php">
        <img src="../imagens/home.png" alt="Início"> <span>Início</span>
      </a>
      <a href="aprovacao.php" id="linkAprovacao">
        <img src="../imagens/inscricoes.png" alt="Inscrições"> <span>Inscrições</span>
      </a>
      <a href="editais.php">
        <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
      </a>
      <a href="relatorio_coord.php">
        <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
      </a>
      <a href="dashboard_coordenador.php">
        <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
      </a>
    <?php endif; ?>
    <a href="../login.php">
      <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

  <main>
    <div class="grade-semanal">
      <div class="dia" id="segunda">
        <h3>Segunda-feira</h3>
        <div class="linhas-horario" id="linhas-segunda"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('segunda')">+ Adicionar horário</button>
      </div>

      <div class="dia" id="terca">
        <h3>Terça-feira</h3>
        <div class="linhas-horario" id="linhas-terca"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('terca')">+ Adicionar horário</button>
      </div>

      <div class="dia" id="quarta">
        <h3>Quarta-feira</h3>
        <div class="linhas-horario" id="linhas-quarta"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('quarta')">+ Adicionar horário</button>
      </div>

      <div class="dia" id="quinta">
        <h3>Quinta-feira</h3>
        <div class="linhas-horario" id="linhas-quinta"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('quinta')">+ Adicionar horário</button>
      </div>

      <div class="dia" id="sexta">
        <h3>Sexta-feira</h3>
        <div class="linhas-horario" id="linhas-sexta"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('sexta')">+ Adicionar horário</button>
      </div>

      <div class="dia" id="sabado">
        <h3>Sábado</h3>
        <div class="linhas-horario" id="linhas-sabado"></div>
        <button class="botao-adicionar" onclick="adicionarLinha('sabado')">+ Adicionar horário</button>
      </div>
    </div>

    <div class="botoes-controle">
      <button id="retornar-dia" style="display: none;" onclick="retornarDia()">Retornar</button>
      <button id="alterar-dados" onclick="habilitarEdicao()">Alterar dados</button>
    </div>
  </main>

  <script src="../js/script.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/luxon@3/build/global/luxon.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {

      document.querySelectorAll('.botao-adicionar').forEach(btn => btn.style.display = 'none');

      fetch("../controller/buscar_horarios.php")
        .then(res => res.json())
        .then(dados => {
          // seu código atual para processar os dados
        })
        .catch(error => {
          console.error("Erro ao carregar horários:", error);
        });


      fetch("../controller/buscar_horarios.php")
        .then(res => res.json())
        .then(dados => {
          const mapaDias = {
            1: 'segunda',
            2: 'terca',
            3: 'quarta',
            4: 'quinta',
            5: 'sexta',
            6: 'sabado'
          };

          console.log("Horários recebidos:", dados);
          dados.forEach(horario => {
            const dia = mapaDias[horario.diaSemana];
            const container = document.querySelector(`#linhas-${dia}`);
            if (!container) return;

            // Corrigir formato dos horários para "HH:mm"
            const horarioInicio = (horario.horarioInicio && horario.horarioInicio !== '00:00:00') ?
              horario.horarioInicio.substring(0, 5) :
              '';

            const horarioFim = (horario.horarioFim && horario.horarioFim !== '00:00:00') ?
              horario.horarioFim.substring(0, 5) :
              '';

            const linha = document.createElement("div");
            linha.classList.add("linha-horario");
            linha.setAttribute("data-dia", dia);

            linha.innerHTML = `
          <input type="time" value="${horarioInicio}" disabled>
          <input type="time" value="${horarioFim}" disabled>
          <select disabled>
            <option value="fatec" ${horario.instituicao === 'fatec' ? 'selected' : ''}>Fatec</option>
            <option value="outra_fatec" ${horario.instituicao === 'outra_fatec' ? 'selected' : ''}>Outra Unidade</option>
            <option value="outra_instituicao" ${horario.instituicao === 'outra_instituicao' ? 'selected' : ''}>Aula Externa</option>
          </select>
          <button type="button" class="btn-remover" style="display:none;" onclick="removerLinha(this)">Remover</button>
        `;

            container.appendChild(linha);
          });
        })
        .catch(error => {
          console.error("Erro ao carregar horários:", error);
        });
    });
  </script>
</body>

</html>