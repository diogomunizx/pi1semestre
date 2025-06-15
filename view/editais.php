<?php
require_once '../config/session_config.php';

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
    $query = "SELECT e.*, f.Nome_Fantasia as unidade, e.arquivo_pdf
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
    <style>
        /* Garantir que os estilos do menu não sejam sobrescritos */
        .sidebar {
            position: fixed !important;
            z-index: 999 !important;
        }
        .sidebar a {
            display: flex !important;
            align-items: center !important;
        }
        .sidebar a img {
            width: 30px !important;
            height: 30px !important;
            margin-right: 15px !important;
        }
        /* Estilos originais da página */
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
        /* Novos estilos para os botões de ação */
        .btn-acao {
            padding: 6px 12px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .btn-editar {
            background-color: #2196F3;
        }
        .btn-editar:hover {
            background-color: #1976D2;
        }
        .btn-encerrar {
            background-color: #f44336;
        }
        .btn-encerrar:hover {
            background-color: #d32f2f;
        }
        .btn-reabrir {
            background-color: #4CAF50;
        }
        .btn-reabrir:hover {
            background-color: #388E3C;
        }
        td.acoes {
            white-space: nowrap;
            text-align: center;
        }
        /* Estilos para upload de arquivo */
        .upload-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .file-input {
            max-width: 200px;
        }
        .btn-upload {
            background-color: #17a2b8;
        }
        .btn-upload:hover {
            background-color: #138496;
        }
        .arquivo-nome {
            font-size: 14px;
            color: #28a745;
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
      <img src="../imagens/dashboard2.png" alt="Dashboard"> <span>Dashboard</span>
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
                            <td>Arquivo</td>
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
                                <td>
                                    <?php if (!empty($edital['arquivo_pdf'])): ?>
                                        <span class="arquivo-nome"><?php echo basename($edital['arquivo_pdf']); ?></span>
                                    <?php else: ?>
                                        <form action="upload_edital.php" method="post" enctype="multipart/form-data" class="upload-form">
                                            <input type="hidden" name="id_edital" value="<?php echo $edital['id_edital']; ?>">
                                            <input type="file" name="arquivo_pdf" accept=".pdf" required class="file-input">
                                            <button type="submit" class="btn-acao btn-upload">Upload</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <button class="btn-acao btn-editar" 
                                            onclick="editarEdital('<?php echo $edital['id_edital']; ?>')">
                                        Editar
                                    </button>
                                    <?php if ($edital['edital_status'] === 'ABERTO'): ?>
                                        <button class="btn-acao btn-encerrar"
                                                onclick="encerrarEdital('<?php echo $edital['id_edital']; ?>')">
                                            Encerrar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-acao btn-reabrir"
                                                onclick="reabrirEdital('<?php echo $edital['id_edital']; ?>')">
                                            Reabrir
                                        </button>
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

    <script src="../js/script.js" defer></script>
</body>

</html> 