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
    
    // Busca os detalhes completos do relatório
    $query = "SELECT r.id_relatorioHae,
                     r.data_entrega as dataEntrega,
                     r.descricao_atividades as descricaoAtividades,
                     r.resultados_alcancados as resultadosObtidos,
                     r.observacoes_coordenador as observacoes,
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     prof.Nome as professor,
                     prof.email as email_professor,
                     r.status,
                     r.observacoes_coordenador as justificativa,
                     r.data_avaliacao as dataAvaliacao
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
    <title>HORUS - Detalhes do Relatório</title>
    <style>
        .detalhes-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .detalhes-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detalhes-header h3 {
            color: #2c3e50;
            margin: 0;
            font-size: 1.5em;
        }

        .secao {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .secao h4 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item {
            margin: 10px 0;
            color: #495057;
        }

        .info-item strong {
            color: #2c3e50;
            min-width: 150px;
            display: inline-block;
        }

        .btn-voltar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-voltar:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-pendente { background-color: #ffc107; color: #000; }
        .status-aprovado { background-color: #28a745; color: #fff; }
        .status-correcao { background-color: #dc3545; color: #fff; }

        .texto-relatorio {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.6;
            color: #444;
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
        <div class="detalhes-container">
            <div class="detalhes-header">
                <h3>Detalhes do Relatório HAE</h3>
                <span class="status-badge status-<?php echo strtolower($relatorio['status']); ?>">
                    <?php echo $relatorio['status']; ?>
                </span>
            </div>

            <div class="secao">
                <h4>Informações do Professor</h4>
                <div class="info-item"><strong>Nome:</strong> <?php echo htmlspecialchars($relatorio['professor']); ?></div>
                <div class="info-item"><strong>E-mail:</strong> <?php echo htmlspecialchars($relatorio['email_professor']); ?></div>
            </div>

            <div class="secao">
                <h4>Informações do Projeto</h4>
                <div class="info-item"><strong>Título:</strong> <?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></div>
                <div class="info-item"><strong>Tipo HAE:</strong> <?php echo htmlspecialchars($relatorio['tipoHae']); ?></div>
                <div class="info-item"><strong>Quantidade HAE:</strong> <?php echo htmlspecialchars($relatorio['quantidadeHae']); ?></div>
                <div class="info-item"><strong>Data de Entrega:</strong> <?php echo date('d/m/Y', strtotime($relatorio['dataEntrega'])); ?></div>
            </div>

            <div class="secao">
                <h4>Conteúdo do Relatório</h4>
                <div class="info-item">
                    <strong>Descrição das Atividades:</strong><br>
                    <div class="texto-relatorio"><?php echo nl2br(htmlspecialchars($relatorio['descricaoAtividades'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Resultados Obtidos:</strong><br>
                    <div class="texto-relatorio"><?php echo nl2br(htmlspecialchars($relatorio['resultadosObtidos'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Observações:</strong><br>
                    <div class="texto-relatorio"><?php echo nl2br(htmlspecialchars($relatorio['observacoes'])); ?></div>
                </div>
            </div>

            <?php if ($relatorio['status'] !== 'PENDENTE'): ?>
            <div class="secao">
                <h4>Avaliação do Coordenador</h4>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo strtolower($relatorio['status']); ?>">
                        <?php echo $relatorio['status']; ?>
                    </span>
                </div>
                <div class="info-item">
                    <strong>Data da Avaliação:</strong> 
                    <?php echo date('d/m/Y', strtotime($relatorio['dataAvaliacao'])); ?>
                </div>
                <div class="info-item">
                    <strong>Justificativa:</strong><br>
                    <div class="texto-relatorio">
                        <?php echo nl2br(htmlspecialchars($relatorio['justificativa'] ?? 'Nenhuma justificativa fornecida.')); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px;">
                <?php if ($relatorio['status'] === 'PENDENTE'): ?>
                    <a href="avaliar_relatorio.php?id=<?php echo $relatorio['id_relatorio']; ?>" class="btn-voltar">
                        Voltar para Avaliação
                    </a>
                <?php else: ?>
                    <a href="relatorio_coord.php" class="btn-voltar">
                        Voltar para Lista de Relatórios
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html> 