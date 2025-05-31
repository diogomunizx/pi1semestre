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
    <title>HORUS - Relatório</title>
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
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img
                            src="../imagens/logo-fatec_itapira.png"></a>
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
        <table class="tbls" id="tableProfessor">
            <h3 class="titulos" id="tituloProfessor">Seus projetos para envios de relátórios</h3>
            <br>
            <thead>
                <tr>
                    <td>Inscrição</td>
                    <td>Coordenador</td>
                    <td>Projeto</td>
                    <td>Tipo HAE</td>
                    <td>Quantidade HAE</td>
                    <td>Status</td>
                    <td>Ações</td>
                    <td>Imprimir</td>
                    <td>Upload</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Coordenador GE</td>
                    <td>Estágio GE</td>
                    <td>Estágio Supervisionado</td>
                    <td>5</td>
                    <td>Pendente</td>
                    <td class="destaque"><img src="../imagens/relatorio.png" onclick="prepararRelatorio()"></td>
                    <td><img class="destaque" src="../imagens/imprimir.png" onclick="imprimirInscricao()"></td>
                    <td class="destaque"><img class="img-edit" src="../imagens/upload.png" onclick="selecionarPDF(this)"></td>
                </tr>
            </tbody>
        </table>
        <div id="formularioProfessor" class="form-container" style="display: none;">
            <!-- Indicadores de Progresso -->
            <div class="step-indicators">
                <div class="step-indicator active" data-step="1">1</div>
                <div class="step-indicator" data-step="2">2</div>
            </div>

            <!-- Barra de Progresso -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 50%"></div>
                </div>
            </div>

            <form class="form-relatorio" action="#" method="POST">
                <!-- Etapa 1: Informações do Professor -->
                <div class="form-steps active" id="step1">
                    <h4>Informações do Professor</h4>
                    <label for="professor">Nome:</label>
                    <input type="text" id="professor" name="professor" disabled value="<?php echo htmlspecialchars($_SESSION['Nome']); ?>" required><br>

                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" disabled value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required><br>

                    <label for="rg">R.G.:</label>
                    <input type="text" id="rg" name="rg" value="123456" required><br>

                    <label for="matricula">Matrícula:</label>
                    <input type="text" id="matricula" name="matricula" value="654321" required><br>

                    <label for="titulo_projeto">Título do projeto:</label>
                    <input type="text" id="titulo_projeto" name="titulo_projeto" value="Estágio GE" required><br>
                </div>

                <!-- Etapa 2: Relatório e Data -->
                <div class="form-steps" id="step2">
                    <h4>Relatório de Atividades</h4>
                    <div class="status-justificativa-section">
                        <label for="relatorio">Descrição das atividades:</label>
                        <textarea class="textarea-auto-ajuste" name="relatorio" rows="4" placeholder="Digite as atividades realizadas..."></textarea><br>

                        <label for="inicio_projeto">Data envio:</label>
                        <input type="date" id="envio_relatorio" name="envio_relatorio"><br>
                    </div>
                </div>

                <!-- Navegação entre etapas -->
                <div class="form-navigation">
                    <button type="button" class="nav-button prev" onclick="prevStep()" disabled>Anterior</button>
                    <button type="button" class="nav-button next" onclick="nextStep()">Próxima</button>
                    <button type="submit" class="nav-button submit" style="display: none;">Enviar</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html> 