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
    
    // Busca os relatórios pendentes para o coordenador
    $query = "SELECT i.id_frmInscricaoHae, 
                     d.Nome as professor,
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     r.status
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_Usuario d ON i.tb_Docentes_id_Docente = d.id_Docente
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              LEFT JOIN tb_relatorio r ON i.id_frmInscricaoHae = r.id_frmInscricaoHae
              WHERE c.id_docenteCoordenador = :id_coordenador
              AND j.status = 'APROVADO'
              ORDER BY i.id_frmInscricaoHae DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_coordenador' => $_SESSION['id_Docente']]);
    $relatorios = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erro ao buscar relatórios: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar os relatórios. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Relatórios</title>
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
        <a href="relatorio_coord.php" class="active">
            <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <h3 class="titulos">Projetos com relatórios aguardando deferimento</h3>
        <br>
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php else: ?>
            <?php if (empty($relatorios)): ?>
                <p>Não há relatórios pendentes no momento.</p>
            <?php else: ?>
                <table class="tbls">
                    <thead>
                        <tr>
                            <td>Inscrição</td>
                            <td>Professor</td>
                            <td>Projeto</td>
                            <td>Tipo HAE</td>
                            <td>Quantidade HAE</td>
                            <td>Status</td>
                            <td>Ações</td>
                            <td>Imprimir</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatorios as $relatorio): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($relatorio['id_frmInscricaoHae']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['professor']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['tipoHae']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['quantidadeHae']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['status'] ?? 'PENDENTE'); ?></td>
                                <td class="destaque">
                                    <img src="../imagens/editar.png" 
                                         onclick="avaliarRelatorio('<?php echo $relatorio['id_frmInscricaoHae']; ?>')">
                                </td>
                                <td>
                                    <img class="destaque" src="../imagens/imprimir.png" 
                                         onclick="imprimirRelatorio('<?php echo $relatorio['id_frmInscricaoHae']; ?>')">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Modal de Avaliação do Relatório -->
        <div id="modal-avaliacao" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="fecharModal()">&times;</span>
                <h2>Avaliar Relatório</h2>
                <form id="form-avaliacao" action="processa_avaliacao_relatorio.php" method="POST">
                    <input type="hidden" id="id_inscricao" name="id_inscricao">
                    
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select id="status" name="status" required>
                            <option value="DEFERIDO">Deferir</option>
                            <option value="CORRECAO">Solicitar Correção</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observacao">Observação:</label>
                        <textarea id="observacao" name="observacao" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit">Confirmar</button>
                        <button type="button" onclick="fecharModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    function avaliarRelatorio(idInscricao) {
        document.getElementById('id_inscricao').value = idInscricao;
        document.getElementById('modal-avaliacao').style.display = 'block';
    }

    function fecharModal() {
        document.getElementById('modal-avaliacao').style.display = 'none';
    }

    function imprimirRelatorio(idInscricao) {
        window.location.href = `imprimir_relatorio.php?id=${idInscricao}`;
    }

    // Fecha o modal se clicar fora dele
    window.onclick = function(event) {
        var modal = document.getElementById('modal-avaliacao');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>

    <style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 5px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-group textarea {
        height: 100px;
        resize: vertical;
    }

    .form-actions {
        text-align: right;
        margin-top: 20px;
    }

    .form-actions button {
        margin-left: 10px;
        padding: 8px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-actions button[type="submit"] {
        background-color: #4CAF50;
        color: white;
    }

    .form-actions button[type="button"] {
        background-color: #f44336;
        color: white;
    }
    </style>
</body>

</html>