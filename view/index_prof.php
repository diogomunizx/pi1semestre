<?php
// index.php

// Inicia ou resume uma sessão existente. Fundamental para manter informações do usuário entre diferentes páginas,
// como o status de login, o ID do usuário, seu tipo (aluno, professor, etc.).
// Esta função deve ser chamada ANTES de qualquer saída HTML ser enviada ao navegador.
session_start();

// Verifica se a variável 'id' está definida na sessão.
// 'isset()' é uma função que verifica se uma variável existe e não é nula.
// $_SESSION é um array superglobal que armazena dados da sessão.
// Se 'id' existe na sessão, significa que o usuário já realizou o login anteriormente.
if (isset($_SESSION['id_Docente'])) {
  // Se o usuário já está logado, ele não precisa ver a página de login novamente.
  // Então, vamos redirecioná-lo para o painel apropriado com base no seu 'tipo'.

  // Verifica o valor da variável 'tipo' armazenada na sessão.
  if ($dados['funcao'] == 'coordenador') {
    header("Location: ../view/index_coord.html");
  } elseif ($dados['funcao'] == 'professor') {
    header("Location: ../view/index_prof.html");
  } elseif ($dados['tipo'] == 'aluno') {
    header("Location: ../view/dashboard_aluno.php");
  }
  // 'exit' (ou 'die()') é crucial após um redirecionamento com 'header()'.
  // Ele garante que nenhum código PHP ou HTML subsequente seja executado,
  // o que poderia causar erros ou comportamentos inesperados, já que o navegador
  // já foi instruído a ir para outra página.
  exit;
}
// Se o código chegou até aqui, significa que $_SESSION['id'] não está definida,
// ou seja, o usuário ainda não está logado. Portanto, o HTML da página de login será exibido.
?>



<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="estilos/style.css">
  <link rel="icon" type="image/png" href="imagens/logo-horus.png">
  <title>HORUS - Início</title>
</head>

<body>
  <header>
    <div class="header-content">
      <div class="user-profile" onclick="toggleDropdown()">
        <span>M</span>
        <div class="dropdown-menu" id="dropdown-menu">
          <a href="#" onclick="alterarVisualizacao()">Alterar Visualização</a>
          <a href="perfil_cadastro.html" onclick="alterarVisualizacaoTelaCadastro()">Ajustes</a>
          <a href="perfil_Aulas.html" onclick="alterarVisualizacaoTelaCadastro()">Minhas aulas</a>
        </div>
      </div>
      <div class="institutions">
        <div class="fatec">
          <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img src="imagens/logo-fatec_itapira.png"></a>
        </div>
        <div class="cps">
          <a href="https://www.cps.sp.gov.br/" target="_blank"><img src="imagens/logo-cps.png"></a>
        </div>
      </div>
    </div>
  </header>


  <nav class="sidebar">
    <div class="logo-container">
      <a href="#">
        <img src="imagens/logo-horus.png" alt="Logo HORUS">
      </a>
    </div>
    <a class="inicio" href="index_prof.html">
      <img src="imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="inscricao.html" id="linkInscricao">
      <img src="imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
    </a>
    <a href="relatorio_prof.html">
      <img src="imagens/relat.png" alt="Relatório"> <span>Relatório</span>
    </a>
    <a href="login.html">
      <img src="imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

  <main>
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
          <td class="destaque"><img class="img-edit" src="imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
        </tr>
        <tr>
          <td>Aprovações HAE</td>
          <td>De 14/01/2025 à 28/01/2025</td>
          <td class="destaque"><img class="img-edit" src="imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
        </tr>
        <tr>
          <td>Divulgação Lista de Aprovados</td>
          <td>30/01/2025</td>
          <td class="destaque"><img class="img-edit" src="imagens/imprimir.png" onclick="imprimirLinhaSeparada(this)"></td>
        </tr>
        <tr>
          <td>Entrega de Relatorios HAE</td>
          <td>De 24/06/2025 à 01/07/2025</td>
        </tr>
        <td>Aprovação de Relatorios HAE</td>
        <td>De 02/07/2025 à 10/07/2025</td>
        </tr>
      </tbody>
    </table>
  </main>
  <script src="script.js" defer></script>



</body>

</html>