<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
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
  <title>HORUS - Inscrição</title>
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
    <a class="inicio" href="index_prof.php">
      <img src="../imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="inscricao.php" id="linkInscricao">
      <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
    </a>
    <a href="relatorio_prof.php">
      <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
    </a>
    <a href="../login.php">
      <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

  <main>
    <h3 class="titulos">Suas Inscrições</h3>
    <br>
    <table class="tbls">
      <thead>
        <tr>
          <td>Inscrição</td>
          <td>Coordenador</td>
          <td>Tipo HAE</td>
          <td>Quantidade HAE</td>
          <td>Curso</td>
          <td>Status</td>
          <td>Justificativa</td>
          <td>Imprimir</td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td class="destaque"><img class="img-edit" src="../imagens/olho.png" onclick="verJustificativa()"></td>
          <td><img class="destaque" src="../imagens/imprimir.png" onclick="imprimirInscricao()"></td>
        </tr>
      </tbody>
    </table>
    <br>
    <div class="nova-inscricao">
      <a href="form_inscricao.php" class="btn-nova-inscricao">Nova Inscrição</a>
    </div>
  </main>
  
  <script src="../js/script.js" defer></script>
</body>

</html> 