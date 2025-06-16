<?php
session_start();

//require_once '../config/session_config.php'; //tirar para acessar local

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';
require_once '../config/database.php';

$stmt = $conn->prepare("
    SELECT u.Nome, f.tituloProjeto, f.tipoHae, j.status 
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    INNER JOIN tb_Usuario AS u ON f.tb_Docentes_id_Docente = u.id_Docente
");
$stmt->execute();
$inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtProfessores = $conn->prepare("
    SELECT COUNT(DISTINCT tb_Docentes_id_Docente) AS total
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j
    ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    WHERE j.status = 'APROVADO'
");
$stmtProfessores->execute();
$professores = $stmtProfessores->fetch(PDO::FETCH_ASSOC);
$totalProfessores = $professores['total'] ?? 0;

// Soma total de horas de HAE aprovadas
$stmtHoras = $conn->prepare("
    SELECT SUM(f.quantidadeHae) AS total_horas
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j
    ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    WHERE j.status = 'APROVADO'
");
$stmtHoras->execute();
$horas = $stmtHoras->fetch(PDO::FETCH_ASSOC);
$totalHoras = $horas['total_horas'] ?? 0;

// Distribuição por tipo de HAE
$stmtDistribuicao = $conn->prepare("
    SELECT f.tipoHae, SUM(f.quantidadeHae) AS horas
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j
    ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    WHERE j.status = 'APROVADO'
    GROUP BY f.tipoHae
");
$stmtDistribuicao->execute();
$distribuicao = $stmtDistribuicao->fetchAll(PDO::FETCH_ASSOC);

$dadosDistribuicao = [
    'Coordenação' => 0,
    'Projetos' => 0,
    'Orientação' => 0
];

foreach ($distribuicao as $d) {
    $tipo = $d['tipoHae'];
    if (isset($dadosDistribuicao[$tipo])) {
        $dadosDistribuicao[$tipo] = $d['horas'];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="stylesheet" href="../estilos/dashboard_coordenador.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>HORUS - Dashboard Coordenador</title>

    <style>
.chart-wrapper {
    width: 100%;
    max-width: 500px;
    margin: 20px auto; /* margem pra dar espaço em cima e embaixo */
    height: 400px;
    position: relative;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* sombra leve */
    border-radius: 8px;
    background-color: #fff; /* fundo branco para destacar */
    padding: 15px; /* espaçamento interno */
}

canvas {
    width: 100% !important;
    height: 100% !important;
    display: block;
    border-radius: 8px; /* cantos arredondados no canvas */
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
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-fatec_itapira.png" alt="FATEC Itapira">
                    </a>
                </div>
                <div class="cps">
                    <a href="https://www.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-cps.png" alt="CPS">
                    </a>
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
    <a href="editais.php">
      <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
    </a>
    <a href="relatorio_coord.php">
      <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
    </a>
    <a href="dashboard_coordenador.php">
        <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
    </a>
    <a href="../login.php">
      <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

    <main>
    <div class="dashboard-container">
        <div class="chart-container">
            <div class="chart-header">
                <h2>Distribuição de HAE - 1º Semestre 2024</h2>

            </div>
            <div class="chart-wrapper">
                <canvas id="graficoHaeBarra"></canvas>
            </div>
            <div class="chart-summary">
                <div class="summary-item">
                    <h3>Professores com HAE</h3>
                    <p><?= $totalProfessores ?> professores</p>
                </div>
                <div class="summary-item">
                    <h3>Total de HAE</h3>
                    <p><?= $totalHoras ?> horas</p>
                </div>
            </div>
        </div>
        
    </div>  

            <div class="professors-table">
                <h3>Professores e Projetos</h3>
                <table>
                    <tr>
                        <th>Professor</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Status</th>
                    </tr>
                <?php foreach ($inscricoes as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['Nome']) ?></td>
                        <td><?= htmlspecialchars($i['tituloProjeto']) ?></td>
                        <td><?= htmlspecialchars($i['tipoHae']) ?></td>
                        <td><?= htmlspecialchars($i['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>
        </div>
    </main>


    <script src="../js/script.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

<script>
  Chart.register(ChartDataLabels);

  const ctx = document.getElementById('graficoHaeBarra').getContext('2d');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Coordenação', 'Projetos', 'Orientação'],
      datasets: [{
        data: [
          <?= $dadosDistribuicao['Coordenação'] ?>,
          <?= $dadosDistribuicao['Projetos'] ?>,
          <?= $dadosDistribuicao['Orientação'] ?>
        ],
        backgroundColor: ['#AE0C0D', '#0b8948', '#f39c12'],
        borderColor: ['#7C090A', '#066636', '#c87f0d'],
        borderWidth: 2,
        borderRadius: 6,
        maxBarThickness: 60
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        duration: 1500,
        easing: 'easeOutBounce'
      },
      plugins: {
        legend: {
          display: false
        },
        datalabels: {
          color: '#444',
          font: {
            weight: 'bold',
            size: 14
          },
          anchor: 'end',
          align: 'top',
          formatter: value => value
        },
        tooltip: {
          enabled: true,
          backgroundColor: '#333',
          titleFont: { size: 14, weight: 'bold' },
          bodyFont: { size: 13 },
          cornerRadius: 4
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1,
            font: { size: 14 }
          },
          grid: { color: '#eee' }
        },
        x: {
          ticks: { font: { size: 14 } },
          grid: { display: false }
        }
      }
    }
  });
</script>

</body>

</html>