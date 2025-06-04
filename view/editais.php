<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
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
        <a class="inicio" href="index_coord.php">
            <img src="../imagens/home.png" alt="Início"> <span>Início</span>
        </a>
        <a href="aprovacao.php" id="linkAprovacao">
            <img src="../imagens/inscricoes.png" alt="Inscricoes"> <span>Inscrições</span>
        </a>
        <a href="editais.php" class="active">
            <img src="../imagens/edital.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_coord.php">
            <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <h3 class="titulos">Gerenciamento de Editais</h3>
        <br>
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>

        <div class="acoes">
            <a href="form_edital.php" class="btn-novo">Novo Edital</a>
        </div>

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
                            <td>Ações</td>
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
                                <td class="acoes">
                                    <img src="../imagens/editar.png" alt="Editar" 
                                         onclick="editarEdital('<?php echo $edital['id_edital']; ?>')"
                                         class="icone-acao">
                                    <?php if ($edital['edital_status'] === 'ABERTO'): ?>
                                        <img src="../imagens/encerrar.png" alt="Encerrar" 
                                             onclick="encerrarEdital('<?php echo $edital['id_edital']; ?>')"
                                             class="icone-acao">
                                    <?php else: ?>
                                        <img src="../imagens/reabrir.png" alt="Reabrir" 
                                             onclick="reabrirEdital('<?php echo $edital['id_edital']; ?>')"
                                             class="icone-acao">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    </main>

    <script>
    function editarEdital(id) {
        window.location.href = `form_edital.php?id=${id}`;
    }

    function encerrarEdital(id) {
        if (confirm('Tem certeza que deseja encerrar este edital?')) {
            window.location.href = `processa_edital.php?acao=encerrar&id=${id}`;
        }
    }

    function reabrirEdital(id) {
        if (confirm('Tem certeza que deseja reabrir este edital?')) {
            window.location.href = `processa_edital.php?acao=reabrir&id=${id}`;
        }
    }
    </script>

    <style>
    .acoes {
        margin-bottom: 20px;
    }

    .btn-novo {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        text-decoration: none;
        border-radius: 4px;
    }

    .btn-novo:hover {
        background-color: #45a049;
    }

    .icone-acao {
        cursor: pointer;
        margin: 0 5px;
        width: 20px;
        height: 20px;
    }

    td.acoes {
        white-space: nowrap;
    }
    </style>
</body>

</html> 