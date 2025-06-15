<?php
require_once '../config/session_config.php';

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

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
      <img src="../imagens/dashboard2.png" alt="Dashboard"> <span>Dashboard</span>
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
                    <div class="chart-legend">
                        <span class="legend-item">
                            <span class="legend-color coordenacao"></span>
                            Coordenação
                        </span>
                        <span class="legend-item">
                            <span class="legend-color projetos"></span>
                            Projetos
                        </span>
                        <span class="legend-item">
                            <span class="legend-color orientacao"></span>
                            Orientação
                        </span>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <canvas id="haeChart"></canvas>
                </div>
                <div class="chart-summary">
                    <div class="summary-item">
                        <h3>Professores com HAE</h3>
                        <p>8 professores</p>
                    </div>
                    <div class="summary-item">
                        <h3>Total de HAE</h3>
                        <p>48 horas</p>
                    </div>
                </div>
            </div>

            <div class="professors-table">
                <h3>Professores e Projetos</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Professor</th>
                            <th>Tipo de HAE</th>
                            <th>Projeto</th>
                            <th>Horas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ana Celia</td>
                            <td><span class="hae-type hae-orientacao">Estágio</span></td>
                            <td>Estágio DSM</td>
                            <td class="horas-column">6h</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                        <tr>
                            <td>João Silva</td>
                            <td><span class="hae-type hae-projetos">Projetos</span></td>
                            <td>Iniciação Científica</td>
                            <td class="horas-column">8h</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                        <tr>
                            <td>Maria Santos</td>
                            <td><span class="hae-type hae-coordenacao">Coordenação</span></td>
                            <td>Coordenação GE</td>
                            <td class="horas-column">10h</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                        <tr>
                            <td>Pedro Oliveira</td>
                            <td><span class="hae-type hae-orientacao">Orientação</span></td>
                            <td>TCC DSM</td>
                            <td class="horas-column">4h</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        // Dados do gráfico
        const ctx = document.getElementById('haeChart').getContext('2d');
        const data = {
            labels: ['Ana Celia', 'João Silva', 'Maria Santos', 'Pedro Oliveira'],
            datasets: [
                {
                    label: 'Coordenação',
                    data: [0, 0, 10, 0],
                    backgroundColor: '#AE0C0D',
                    borderRadius: 5,
                },
                {
                    label: 'Projetos',
                    data: [0, 8, 0, 0],
                    backgroundColor: '#0b8948',
                    borderRadius: 5,
                },
                {
                    label: 'Orientação',
                    data: [6, 0, 0, 4],
                    backgroundColor: '#f39c12',
                    borderRadius: 5,
                }
            ]
        };

        // Configuração do gráfico
        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 2
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw} horas`;
                            }
                        }
                    }
                }
            }
        };

        // Criar o gráfico
        new Chart(ctx, config);
    </script>
    <script src="../js/script.js" defer></script>
</body>

</html>