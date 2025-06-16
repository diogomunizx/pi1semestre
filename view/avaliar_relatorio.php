<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

// Verifica se foi passado um ID de relatório
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID de relatório inválido.";
    header("Location: relatorio_coord.php");
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Busca os detalhes do relatório
    $query = "SELECT r.id_relatorioHae, 
                     r.data_entrega as dataEntrega,
                     r.descricao_atividades as descricaoAtividades,
                     r.resultados_alcancados as resultadosObtidos,
                     r.observacoes_coordenador as observacoes,
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     prof.Nome as professor,
                     r.status
              FROM tb_relatorioHae r
              INNER JOIN tb_frm_inscricao_hae i ON r.id_frmInscricaoHae = i.id_frmInscricaoHae
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              WHERE r.id_relatorioHae = :id_relatorio";

    $stmt = $conn->prepare($query);
    $stmt->execute(['id_relatorio' => $_GET['id']]);

    $relatorio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$relatorio) {
        $_SESSION['erro'] = "Relatório não encontrado.";
        header("Location: relatorio_coord.php");
        exit;
    }
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes do relatório: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes do relatório.";
    header("Location: relatorio_coord.php");
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
    <title>HORUS - Avaliar Relatório</title>
    <style>
        .avaliacao-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .avaliacao-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .avaliacao-header h3 {
            color: #2c3e50;
            margin: 0;
            font-size: 1.5em;
        }

        .info-projeto {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-projeto p {
            margin: 10px 0;
            color: #495057;
            display: flex;
            justify-content: space-between;
        }

        .info-projeto strong {
            color: #2c3e50;
            min-width: 150px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-group textarea {
            width: 100%;
            min-height: 120px;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .form-group textarea:focus {
            border-color: #28a745;
            outline: none;
        }

        .avaliacao-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn-avaliar {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-aprovar {
            background-color: #28a745;
            color: white;
        }

        .btn-correcao {
            background-color: #ffc107;
            color: black;
        }

        .btn-avaliar:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-aprovar:hover {
            background-color: #218838;
        }

        .btn-correcao:hover {
            background-color: #e0a800;
        }

        .status-atual {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }

        .status-pendente {
            background-color: #ffc107;
            color: #000;
        }

        .btn-ver-completa {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-ver-completa:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
            <img src="../imagens/inscricoes.png" alt="Inscricoes"> <span>Inscrições</span>
        </a>
        <a href="editais.php">
            <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_coord.php" class="active">
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
        <div class="avaliacao-container">
            <div class="avaliacao-header">
                <h3>Avaliação do Relatório HAE</h3>
                <span class="status-atual status-pendente">Status: PENDENTE</span>
            </div>

            <div class="info-projeto">
                <p><strong>Professor:</strong> <span><?php echo htmlspecialchars($relatorio['professor']); ?></span></p>
                <p><strong>Tipo HAE:</strong> <span><?php echo htmlspecialchars($relatorio['tipoHae']); ?></span></p>
                <p><strong>Quantidade HAE:</strong> <span><?php echo htmlspecialchars($relatorio['quantidadeHae']); ?></span></p>
                <p><strong>Título do Projeto:</strong> <span><?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></span></p>
                <p><strong>Data de Entrega:</strong> <span><?php echo date('d/m/Y', strtotime($relatorio['dataEntrega'])); ?></span></p>
            </div>

            <div class="acoes-container">
                <div class="acoes-icones">
                    <a href="ver_detalhes_relatorio.php?id=<?php echo $relatorio['id_relatorioHae']; ?>"
                        class="btn-ver-completa">Ver Relatório Completo</a>
                </div>
            </div>

            <form action="processa_avaliacao_relatorio.php" method="POST">
                <input type="hidden" name="id_relatorio" value="<?php echo $relatorio['id_relatorioHae']; ?>">

                <div class="form-group">
                    <label for="justificativa">Justificativa da Avaliação:</label>
                    <textarea id="justificativa" name="justificativa" required
                        placeholder="Digite aqui sua justificativa para a aprovação ou solicitação de correção deste relatório..."></textarea>
                </div>

                <div class="avaliacao-actions">
                    <button type="submit" name="acao" value="aprovar" class="btn-avaliar btn-aprovar">Aprovar Relatório</button>
                    <button type="submit" name="acao" value="correcao" class="btn-avaliar btn-correcao">Solicitar Correção</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html>