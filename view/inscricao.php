<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca as inscrições do professor logado com o nome do coordenador correto
    $query = "SELECT i.id_frmInscricaoHae, 
                     coord.Nome as coordenador,
                     i.tipoHae,
                     i.quantidadeHae,
                     c.Materia as curso,
                     COALESCE(j.status, 'PENDENTE') as status
              FROM tb_frm_inscricao_hae i
              LEFT JOIN tb_cursos c ON i.id_curso = c.id_curso
              LEFT JOIN tb_Usuario coord ON c.id_docenteCoordenador = coord.id_Docente
              LEFT JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
              WHERE i.tb_Docentes_id_Docente = :id_docente
              ORDER BY i.id_frmInscricaoHae DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_docente' => $_SESSION['id_Docente']]);
    $inscricoes = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao buscar inscrições: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar as inscrições. Por favor, tente novamente mais tarde.";
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
  <style>
    .nova-inscricao {
      margin-top: 20px;
      text-align: center;
    }

    .btn-nova-inscricao {
      display: inline-block;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .btn-nova-inscricao:hover {
      background-color: #45a049;
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
    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
    <?php endif; ?>
    <?php if (isset($erro)): ?>
        <div class="erro"><?php echo $erro; ?></div>
    <?php else: ?>
        <?php if (empty($inscricoes)): ?>
            <p>Você ainda não possui nenhuma inscrição cadastrada.</p>
        <?php else: ?>
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
                <?php foreach ($inscricoes as $inscricao): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($inscricao['id_frmInscricaoHae']); ?></td>
                      <td><?php echo htmlspecialchars($inscricao['coordenador']); ?></td>
                      <td><?php echo htmlspecialchars($inscricao['tipoHae']); ?></td>
                      <td><?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></td>
                      <td><?php echo htmlspecialchars($inscricao['curso']); ?></td>
                      <td><?php echo htmlspecialchars($inscricao['status']); ?></td>
                      <td class="destaque">
                          <img class="img-edit" src="../imagens/olho.png" 
                               onclick="verJustificativa('<?php echo $inscricao['id_frmInscricaoHae']; ?>')">
                      </td>
                      <td>
                          <img class="destaque" src="../imagens/imprimir.png" 
                               onclick="imprimirInscricao('<?php echo $inscricao['id_frmInscricaoHae']; ?>')">
                      </td>
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
    <br>
    <div class="nova-inscricao">
      <a href="form_inscricao.php" class="btn-nova-inscricao">Nova Inscrição</a>
    </div>
  </main>
  
  <script>
  function verJustificativa(idInscricao) {
      // Implementar visualização da justificativa
      window.location.href = `ver_justificativa.php?id=${idInscricao}`;
  }

  function imprimirInscricao(idInscricao) {
      // Implementar impressão da inscrição
      window.location.href = `imprimir_inscricao.php?id=${idInscricao}`;
  }
  </script>
  <script src="../js/script.js" defer></script>
</body>

</html> 