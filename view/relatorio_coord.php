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
    
    // Busca os relatórios dos professores dos cursos que o coordenador coordena
    $query = "SELECT r.id_relatorioHae,
                     r.id_frmInscricaoHae,
                     r.data_entrega as dataEntrega,
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     prof.Nome as professor,
                     c.Materia as curso,
                     r.status
              FROM tb_relatorioHae r
              INNER JOIN tb_frm_inscricao_hae i ON r.id_frmInscricaoHae = i.id_frmInscricaoHae
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              WHERE c.id_docenteCoordenador = :id_coordenador
              ORDER BY r.data_entrega DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_coordenador' => $_SESSION['id_Docente']]);
    $relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erro ao buscar relatórios: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar os relatórios. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Relatórios</title>
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
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-aprovado { background-color: #28a745; }
        .status-pendente { background-color: #ffc107; color: #000; }
        .status-correcao { background-color: #dc3545; }

        .btn-avaliar, .btn-ver {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-avaliar {
            background-color: #007bff;
        }

        .btn-ver {
            background-color: #6c757d;
        }

        .btn-avaliar:hover, .btn-ver:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            color: white;
        }

        .btn-avaliar:hover {
            background-color: #0056b3;
        }

        .btn-ver:hover {
            background-color: #5a6268;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
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

        .avaliacao-form textarea {
            width: 100%;
            min-height: 100px;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .avaliacao-form button {
            margin: 5px;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-aprovar {
            background-color: #28a745;
            color: white;
        }

        .btn-correcao {
            background-color: #dc3545;
            color: white;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
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
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-fatec_itapira.png">
                    </a>
                </div>
                <div class="cps">
                    <a href="https://www.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-cps.png">
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
        <a href="relatorio_coord.php" class="active">
            <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <h3 class="titulos">Relatórios para Avaliação</h3>
        <br>
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php else: ?>
            <table class="tbls">
                <thead>
                    <tr>
                        <td>Professor</td>
                        <td>Curso</td>
                        <td>Projeto</td>
                        <td>Tipo HAE</td>
                        <td>Data Entrega</td>
                        <td>Status</td>
                        <td>Ações</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($relatorios)): ?>
                        <tr>
                            <td colspan="7">Nenhum relatório encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($relatorios as $relatorio): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($relatorio['professor']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['curso']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></td>
                                <td><?php echo htmlspecialchars($relatorio['tipoHae']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($relatorio['dataEntrega'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower($relatorio['status']); ?>">
                                        <?php echo $relatorio['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($relatorio['status'] === 'PENDENTE'): ?>
                                        <a href="avaliar_relatorio.php?id=<?php echo $relatorio['id_relatorioHae']; ?>" 
                                           class="btn-avaliar">Avaliar</a>
                                    <?php else: ?>
                                        <a href="ver_detalhes_relatorio.php?id=<?php echo $relatorio['id_relatorioHae']; ?>" 
                                           class="btn-ver">Ver Detalhes</a>
                                    <?php endif; ?>
                                    <a href="ver_detalhes_inscricao.php?id=<?php echo $relatorio['id_frmInscricaoHae']; ?>&from=relatorio" 
                                       class="btn-ver">Ver Inscrição</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Modal de Avaliação -->
        <div id="modalAvaliacao" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h4>Avaliação do Relatório</h4>
                <form id="avaliacaoForm" class="avaliacao-form" action="processa_avaliacao.php" method="POST">
                    <input type="hidden" id="id_relatorio" name="id_relatorio">
                    
                    <label for="observacoes">Observações:</label>
                    <textarea id="observacoes" name="observacoes" required></textarea>
                    
                    <div style="text-align: right;">
                        <button type="submit" name="acao" value="aprovar" class="btn-aprovar">Aprovar</button>
                        <button type="submit" name="acao" value="correcao" class="btn-correcao">Solicitar Correção</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    // Modal
    var modal = document.getElementById("modalAvaliacao");
    var span = document.getElementsByClassName("close")[0];

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function avaliarRelatorio(idRelatorio) {
        document.getElementById('id_relatorio').value = idRelatorio;
        modal.style.display = "block";
    }

    function verRelatorio(idRelatorio) {
        window.location.href = `ver_relatorio.php?id=${idRelatorio}`;
    }
    </script>

    <script src="../js/script.js" defer></script>
</body>

</html>