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
    
    // Busca os editais
    $query = "SELECT e.*, f.Nome_Fantasia as unidade
              FROM tb_Editais e
              INNER JOIN tb_unidadeFatec f ON e.Unidade_Fatec_idUnidade_Fatec = f.id_unidadeFatec
              ORDER BY e.id_edital DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $editais = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao buscar editais: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar os editais. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Editais</title>
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
        <a class="inicio" href="index_prof.php">
            <img src="../imagens/home.png" alt="Início"> <span>Início</span>
        </a>
        <a href="inscricao.php" id="linkInscricao">
            <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
        </a>
        <a href="editais_prof.php" class="active">
            <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_prof.php">
            <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <h3 class="titulos">Editais</h3>
        <br>
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php else: ?>
            <?php if (empty($editais)): ?>
                <p>Não há editais cadastrados.</p>
            <?php else: ?>
                <table class="tbls">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Vigência</td>
                            <td>Início Inscrições</td>
                            <td>Fim Inscrições</td>
                            <td>Status</td>
                            <td>Unidade</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($editais as $edital): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($edital['id_edital']); ?></td>
                                <td><?php echo htmlspecialchars($edital['vigencia']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($edital['dataInicioInscricao'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($edital['dataFimInscricao'])); ?></td>
                                <td><?php echo htmlspecialchars($edital['edital_status']); ?></td>
                                <td><?php echo htmlspecialchars($edital['unidade']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <style>
    .tbls {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .tbls thead {
        background-color: #f5f5f5;
    }

    .tbls td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .tbls tbody tr:hover {
        background-color: #f9f9f9;
    }
    </style>
</body>

</html> 