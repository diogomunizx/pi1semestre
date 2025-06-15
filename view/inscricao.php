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
                     COALESCE(j.status, 'PENDENTE') as status,
                     j.justificativa
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
      margin: 30px auto;
      text-align: center;
      width: 100%;
      position: relative;
      z-index: 2;
    }

    .btn-nova-inscricao {
      display: inline-block !important;
      padding: 12px 24px !important;
      background-color: #4CAF50 !important;
      color: white !important;
      text-decoration: none !important;
      border-radius: 4px !important;
      font-weight: bold !important;
      font-size: 16px !important;
      transition: all 0.3s ease !important;
      border: none !important;
      cursor: pointer !important;
      box-shadow: 0 2px 4px rgba(0,0,0,0.2) !important;
    }

    .btn-nova-inscricao:hover {
      background-color: #45a049 !important;
      transform: translateY(-2px) !important;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
    }

    /* Ajustes na tabela */
    .tbls {
      width: 100%;
      table-layout: fixed;
      font-size: 0.9em;
    }

    .tbls td {
      padding: 8px 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .tbls td:nth-child(1) {
      width: 8%;
    }

    /* Inscrição */
    .tbls td:nth-child(2) {
      width: 15%;
    }

    /* Coordenador */
    .tbls td:nth-child(3) {
      width: 15%;
    }

    /* Tipo HAE */
    .tbls td:nth-child(4) {
      width: 10%;
    }

    /* Quantidade HAE */
    .tbls td:nth-child(5) {
      width: 25%;
    }

    /* Curso */
    .tbls td:nth-child(6) {
      width: 12%;
    }

    /* Status */
    .tbls td:nth-child(7) {
      width: 15%;
    }

    /* Ações */

    /* Estilo para os status */
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
      color: white;
    }

    .status-aprovado {
      background-color: #28a745;
    }

    .status-pendente {
      background-color: #ffc107;
    }

    .status-reprovado {
      background-color: #dc3545;
    }

    /* Estilos para o Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 600px;
      border-radius: 8px;
      position: relative;
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .modal-header {
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #ddd;
    }

    .modal-header h4 {
      margin: 0;
      color: #333;
      font-size: 1.2em;
    }

    .modal-body {
      margin-bottom: 20px;
      line-height: 1.5;
      color: #444;
      font-size: 1em;
    }

    .close-modal {
      position: absolute;
      right: 20px;
      top: 15px;
      font-size: 24px;
      cursor: pointer;
      color: #666;
      transition: color 0.3s ease;
    }

    .close-modal:hover {
      color: #000;
    }

    .img-edit {
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .img-edit:hover {
      transform: scale(1.1);
    }

    .btn-ver {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 4px;
      text-decoration: none;
      color: white;
      font-weight: bold;
      transition: all 0.3s ease;
      background-color: #6c757d;
    }

    .btn-ver:hover {
      background-color: #5a6268;
      transform: translateY(-2px);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      color: white;
    }
  </style>
</head>

<body>
  <header>
    <div class="header-content">
      <div class="user-profile" onclick="toggleDropdown()">
        <span><?php echo htmlspecialchars($_SESSION['Nome'][0]); ?></span>
        <div class="dropdown-menu" id="dropdown-menu">
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
    <a href="editais_prof.php">
      <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
    </a>
    <a href="relatorio_prof.php" class="active">
      <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
    </a>
    <a href="dashboard_professor.php">
      <img src="../imagens/dashboard2.png" alt="Dashboard"> <span>Dashboard</span>
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
              <td>Ações</td>
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
                <td>
                  <span class="status-badge status-<?php echo strtolower($inscricao['status']); ?>">
                    <?php
                    $status = strtoupper($inscricao['status']);
                    if ($status === 'APROVADO') echo 'Deferido';
                    elseif ($status === 'REPROVADO') echo 'Indeferido';
                    else echo $inscricao['status'];
                    ?>
                  </span>
                </td>
                <td>
                  <a href="ver_detalhes_inscricao_prof.php?id=<?php echo $inscricao['id_frmInscricaoHae']; ?>"
                    class="btn-ver">Ver Detalhes</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    <?php endif; ?>
    
    <div class="nova-inscricao">
      <a href="form_inscricao.php" class="btn-nova-inscricao">Nova Inscrição</a>
    </div>
  </main>

  <!-- Modal para Justificativa -->
  <div id="justificativaModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="fecharModal()">&times;</span>
      <div class="modal-header">
        <h4>Justificativa da Inscrição</h4>
      </div>
      <div class="modal-body" id="justificativaTexto">
      </div>
    </div>
  </div>

  <script>
    function verJustificativa(idInscricao, status, justificativa) {
      const modal = document.getElementById('justificativaModal');
      const textoJustificativa = document.getElementById('justificativaTexto');

      if (status === 'PENDENTE') {
        textoJustificativa.innerHTML = '<p style="color: #666;">O coordenador ainda não visualizou sua inscrição.</p>';
      } else {
        // Se não houver justificativa, mostra uma mensagem padrão
        const texto = justificativa ? justificativa.replace(/\\'/g, "'") : 'Nenhuma justificativa fornecida.';
        textoJustificativa.innerHTML = `<p>${texto}</p>`;
      }

      modal.style.display = 'block';
    }

    function fecharModal() {
      document.getElementById('justificativaModal').style.display = 'none';
    }

    // Fecha o modal se clicar fora dele
    window.onclick = function(event) {
      const modal = document.getElementById('justificativaModal');
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }

    // Fecha o modal ao pressionar ESC
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        document.getElementById('justificativaModal').style.display = 'none';
      }
    });

    function imprimirInscricao(idInscricao) {
      window.location.href = `imprimir_inscricao.php?id=${idInscricao}`;
    }
  </script>
  <script src="../js/script.js" defer></script>
</body>

</html>