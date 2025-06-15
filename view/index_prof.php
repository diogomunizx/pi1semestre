<?php
// index.php

// Inicia ou resume uma sessão existente. Fundamental para manter informações do usuário entre diferentes páginas,
// como o status de login, o ID do usuário, seu tipo (aluno, professor, etc.).
// Esta função deve ser chamada ANTES de qualquer saída HTML ser enviada ao navegador.
session_start();

require_once '../config/session_config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
    $_SESSION['login_error'] = "Por favor, faça login para acessar o sistema.";
    header("Location: ../login.php");
    exit;
}

// Verifica se é professor ou coordenador
$funcao = strtolower($_SESSION['funcao']);
if ($funcao !== 'professor' && $funcao !== 'coordenador' && $funcao !== 'prof/coord') {
    $_SESSION['login_error'] = "Acesso não autorizado.";
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca o edital mais recente
    $queryEdital = "SELECT id_edital FROM tb_Editais ORDER BY id_edital DESC LIMIT 1";
    $stmtEdital = $conn->query($queryEdital);
    $edital = $stmtEdital->fetch();
    $idEdital = $edital ? $edital['id_edital'] : null;

    // Busca as datas do cronograma
    if ($idEdital) {
        $queryCronograma = "SELECT tipo_evento, data_inicio, data_fim FROM tb_cronograma WHERE id_edital = :id_edital";
        $stmtCronograma = $conn->prepare($queryCronograma);
        $stmtCronograma->execute(['id_edital' => $idEdital]);
        $cronograma = $stmtCronograma->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Erro ao buscar cronograma: " . $e->getMessage());
}

function formatarData($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../estilos/style.css">
  <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
  <title>HORUS - Início</title>
  <style>
    .data-cell {
        min-width: 200px;
    }

    .periodo-datas {
        display: block;
        font-size: 14px;
        color: #2c3e50;
    }

    .status-atual {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        color: white;
        background-color: #28a745;
        margin-left: 10px;
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
    <h1>Painel do Professor</h1>
    <table class="tbls">
      <caption>
        <br>
      </caption>
      <thead>
        <tr>
          <th class="cece" scope="col" colspan="2">Cronograma</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Divulgação do Edital</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'divulgacao_edital';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Inscrições HAE Abertas</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'inscricoes_abertas';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Aprovações HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'aprovacoes';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Divulgação Lista de Aprovados</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'lista_aprovados';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Entrega de Relatórios HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'entrega_relatorios';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Aprovação de Relatórios HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'aprovacao_relatorios';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                    <?php 
                    $hoje = date('Y-m-d');
                    if ($hoje >= $evento['data_inicio'] && $hoje <= $evento['data_fim']): ?>
                        <span class="status-atual">ATUAL</span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
          </td>
        </tr>
      </tbody>
    </table>
  </main>
  <script src="../js/script.js" defer></script>
</body>

</html>