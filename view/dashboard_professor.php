<?php
//Ainda não esta 100% integrado com o banco de dados, ainda ha algumas alterações para fazer
session_start();

//require_once '../config/session_config.php'; //tirar para acessar local

if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';
require_once '../config/database.php';

$idDocente = $_SESSION['id_Docente'];

$stmt = $conn->prepare("
    SELECT f.tituloProjeto, f.tipoHae, f.inicioProjeto, f.fimProjeto, j.status 
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    WHERE f.tb_Docentes_id_Docente = ? AND j.status = 'APROVADO'
");
$stmt->execute([$idDocente]);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta para pegar as unidades e a soma das horas aprovadas para este docente
$stmtUnidades = $conn->prepare("
    SELECT u.Nome_Fantasia, SUM(f.quantidadeHae) AS totalHoras
    FROM tb_frm_inscricao_hae AS f
    INNER JOIN tb_justificativaHae AS j ON f.id_frmInscricaoHae = j.id_frmInscricaoHae
    INNER JOIN tb_unidadefatec_docente AS ud ON ud.id_Docente = f.tb_Docentes_id_Docente
    INNER JOIN tb_unidadefatec AS u ON ud.id_unidadefatec = u.id_unidadefatec
    WHERE f.tb_Docentes_id_Docente = ? AND j.status = 'APROVADO'
    GROUP BY u.Nome_Fantasia
");
$stmtUnidades->execute([$idDocente]);
$dadosUnidades = $stmtUnidades->fetchAll(PDO::FETCH_ASSOC);

// Preparar arrays para labels e dados do gráfico
$labelsUnidades = [];
$valoresUnidades = [];

foreach ($dadosUnidades as $dado) {
    $labelsUnidades[] = $dado['Nome_Fantasia'];
    $valoresUnidades[] = (int)$dado['totalHoras'];
}

// Cores padrão para até 10 unidades, pode expandir à vontade
$cores = ['#AE0C0D', '#0b8948', '#f39c12', '#1f77b4', '#ff7f0e', '#2ca02c', '#d62728', '#9467bd', '#8c564b', '#e377c2'];
// Se houver mais unidades que cores disponíveis, repetir as cores (ou criar lógica mais avançada)
$backgroundColors = [];
for ($i = 0; $i < count($labelsUnidades); $i++) {
    $backgroundColors[] = $cores[$i % count($cores)];
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="stylesheet" href="../estilos/dashboard_professor.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>HORUS - Dashboard Professor</title>
</head>
<style>
        .chart-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 0%;
            height: 500px;
            position: auto;
        }
    </style>
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
        <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
    </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="dashboard-container">
            <!-- Cartões de Resumo -->
            <div class="summary-cards">
                <div class="summary-card total-hours">
                    <div class="card-content">
                        <h3>Total de Horas Semanais</h3>
                        <p class="card-value">32h</p>
                    </div>
                </div>
                <div class="summary-card hae-hours">
                    <div class="card-content">
                        <h3>Horas em Projetos HAE</h3>
                        <p class="card-value">8h</p>
                    </div>
                </div>
            </div>

            <!-- Gráfico de Distribuição de Horas -->
            <div class="chart-container">
                <div class="chart-header">
                    <h2>Distribuição de Horas por Unidade</h2>
                </div>
                <div class="chart-wrapper">
                    <canvas id="hoursDistributionChart" style="width: 100%; max-width: 100%; max-height: 100%; height: 100%;"></canvas>
                </div>
            </div>

            <!-- Tabela de Projetos HAE -->
            <div class="professors-table">
                <h3>Meus Projetos HAE</h3>
                <table>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Status</th>
                    </tr>
                <?php foreach ($projetos as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['tituloProjeto']) ?></td>
                        <td><?= htmlspecialchars($p['tipoHae']) ?></td>
                        <td><?= htmlspecialchars($p['inicioProjeto']) ?></td>
                        <td><?= htmlspecialchars($p['fimProjeto']) ?></td>
                        <td><?= htmlspecialchars($p['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </div>

            <!-- Detalhamento de Horas -->
            <div class="hours-details">
                <div class="hours-card">
                    <h3>Fatec Itapira</h3>
                    <div class="hours-row">
                        <span>Aulas</span>
                        <span class="hours-value">16h</span>
                    </div>
                    <div class="hours-row">
                        <span>Projetos HAE</span>
                        <span class="hours-value">8h</span>
                    </div>
                </div>
                <div class="hours-card">
                    <h3>Fatec Mogi Mirim</h3>
                    <div class="hours-row">
                        <span>Aulas</span>
                        <span class="hours-value">8h</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

   

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('hoursDistributionChart').getContext('2d');

const data = {
    labels: ['Fatec Itapira', 'Fatec Mogi Mirim', 'Outra Unidade'],
    datasets: [{
        data: [24, 8, 5],  // DADOS FIXOS para teste
        backgroundColor: ['#AE0C0D', '#0b8948', '#f39c12'],
        borderRadius: 5,
    }]
};

const config = {
    type: 'pie',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 14
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.raw}h`;
                    }
                }
            }
        }
    }
};

new Chart(ctx, config);
</script>

    <script src="../js/script.js" defer></script>
</body>

</html> 