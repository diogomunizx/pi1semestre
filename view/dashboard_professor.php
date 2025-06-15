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
                    <canvas id="hoursDistributionChart"></canvas>
                </div>
            </div>

            <!-- Tabela de Projetos HAE -->
            <div class="professors-table">
                <h3>Meus Projetos HAE</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Projeto</th>
                            <th>Tipo</th>
                            <th>Horas Semanais</th>
                            <th>Período</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Estágio DSM</td>
                            <td><span class="hae-type hae-orientacao">Estágio</span></td>
                            <td class="horas-column">4h</td>
                            <td>Fev/2024 - Jun/2024</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                        <tr>
                            <td>Iniciação Científica</td>
                            <td><span class="hae-type hae-projetos">Projetos</span></td>
                            <td class="horas-column">4h</td>
                            <td>Fev/2024 - Jun/2024</td>
                            <td><span class="status status-active">Ativo</span></td>
                        </tr>
                    </tbody>
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

    <script>
        // Gráfico de Distribuição de Horas
        const ctx = document.getElementById('hoursDistributionChart').getContext('2d');
        const data = {
            labels: ['Fatec Itapira', 'Fatec Mogi Mirim'],
            datasets: [{
                data: [24, 8],
                backgroundColor: ['#AE0C0D', '#0b8948'],
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