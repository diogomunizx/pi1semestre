<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
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
  <title>Painel do Coordenador</title>
  <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
    </style>
</head>

<body>
  <header>
    <div class="header-content">
      <div class="user-profile" onclick="toggleDropdown()">
        <span><?php echo htmlspecialchars($_SESSION['Nome'][0]); ?></span>
        <div class="dropdown-menu" id="dropdown-menu">
          <a href="#" onclick="alterarVisualizacao()">Alterar Visualização</a>
          <a href="perfil_cadastro.php">Ajustes</a>
          <a href="perfil_Aulas.php">Minhas aulas</a>
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
    <a class="inicio" href="index_coord.php">
      <img src="../imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="aprovacao.php" id="linkAprovacao">
      <img src="../imagens/inscricoes.png" alt="Inscrições"> <span>Inscrições</span>
    </a>
    <a href="relatorio_coord.php">
      <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
    </a>
    <a href="../login.php">
      <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

  <main>
    <h1>Painel do Coordenador</h1>
    <table class="tbls">
      <caption>
        <br>
        <h3>Referente ao Edital 2025</h3>
        <br>
      </caption>
      <thead>
        <tr>
          <th class="cece" scope="col" colspan="5">Cronograma</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Incrições HAE abertas</td>
          <td>De 18/11/2024 à 13/01/2025</td>
          <td class="destaque"><img class="img-edit" src="../imagens/editar.png" onclick="editarData(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
        </tr>
        <tr>
          <td>Aprovações HAE</td>
          <td>De 14/01/2025 à 28/01/2025</td>
          <td class="destaque"><img class="img-edit" src="../imagens/editar.png" onclick="editarData(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
        </tr>
        <tr>
          <td>Divulgação Lista de Aprovados</td>
          <td>30/01/2025</td>
          <td class="destaque"><img class="img-edit" src="../imagens/editar.png" onclick="editarData(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
        </tr>
        <tr>
          <td>Entrega de Relatorios HAE</td>
          <td>De 24/06/2025 à 01/07/2025</td>
          <td class="destaque"><img class="img-edit" src="../imagens/editar.png" onclick="editarData(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
          <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
        </tr>
        <td>Aprovação de Relatorios HAE</td>
        <td>De 02/07/2025 à 10/07/2025</td>
        <td class="destaque"><img class="img-edit" src="../imagens/editar.png" onclick="editarData(this)"></td>
        <td class="destaque"><img class="img-edit" src="../imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
        <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
        </tr>
      </tbody>
    </table>
  </main>
  <script src="../js/script.js" defer></script>
</body>

</html>