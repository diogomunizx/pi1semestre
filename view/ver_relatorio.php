<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

// Verifica se foi passado um ID de relatório
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID de relatório inválido.";
    header("Location: " . (strtolower($_SESSION['funcao']) === 'professor' ? 'relatorio_prof.php' : 'relatorio_coord.php'));
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca os detalhes do relatório
    $query = "SELECT r.*, 
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     prof.Nome as professor,
                     coord.Nome as coordenador,
                     c.Materia as curso
              FROM tb_relatorioHae r
              INNER JOIN tb_frm_inscricao_hae i ON r.id_frmInscricaoHae = i.id_frmInscricaoHae
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              INNER JOIN tb_Usuario coord ON c.id_docenteCoordenador = coord.id_Docente
              WHERE r.id_relatorioHae = :id_relatorio";
              
    if (strtolower($_SESSION['funcao']) === 'professor') {
        $query .= " AND i.tb_Docentes_id_Docente = :id_docente";
    } elseif (strtolower($_SESSION['funcao']) === 'coordenador') {
        $query .= " AND c.id_docenteCoordenador = :id_docente";
    }
              
    $stmt = $conn->prepare($query);
    $params = ['id_relatorio' => $_GET['id']];
    $params['id_docente'] = $_SESSION['id_Docente'];
    $stmt->execute($params);
    
    $relatorio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$relatorio) {
        $_SESSION['erro'] = "Relatório não encontrado ou você não tem permissão para visualizá-lo.";
        header("Location: " . (strtolower($_SESSION['funcao']) === 'professor' ? 'relatorio_prof.php' : 'relatorio_coord.php'));
        exit;
    }
    
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes do relatório: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes do relatório.";
    header("Location: " . (strtolower($_SESSION['funcao']) === 'professor' ? 'relatorio_prof.php' : 'relatorio_coord.php'));
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
    <title>HORUS - Visualizar Relatório</title>
    <style>
        .relatorio-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detalhes-projeto {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .detalhes-relatorio {
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        
        .status-aprovado { background-color: #28a745; }
        .status-pendente { background-color: #ffc107; }
        .status-correcao { background-color: #dc3545; }
        
        .observacoes {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
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
        <a class="inicio" href="<?php echo strtolower($_SESSION['funcao']) === 'professor' ? 'index_prof.php' : 'index_coord.php'; ?>">
            <img src="../imagens/home.png" alt="Início"> <span>Início</span>
        </a>
        <?php if (strtolower($_SESSION['funcao']) === 'professor'): ?>
            <a href="inscricao.php">
                <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
            </a>
            <a href="relatorio_prof.php" class="active">
                <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
            </a>
        <?php else: ?>
            <a href="aprovacao.php">
                <img src="../imagens/inscricoes.png" alt="Aprovações"> <span>Aprovações</span>
            </a>
            <a href="relatorio_coord.php" class="active">
                <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
            </a>
        <?php endif; ?>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="relatorio-container">
            <h3>Visualizar Relatório</h3>
            
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
            <?php endif; ?>

            <div class="detalhes-projeto">
                <h4>Detalhes do Projeto</h4>
                <p><strong>Professor:</strong> <?php echo htmlspecialchars($relatorio['professor']); ?></p>
                <p><strong>Coordenador:</strong> <?php echo htmlspecialchars($relatorio['coordenador']); ?></p>
                <p><strong>Curso:</strong> <?php echo htmlspecialchars($relatorio['curso']); ?></p>
                <p><strong>Título do Projeto:</strong> <?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></p>
                <p><strong>Tipo HAE:</strong> <?php echo htmlspecialchars($relatorio['tipoHae']); ?></p>
                <p><strong>Quantidade HAE:</strong> <?php echo htmlspecialchars($relatorio['quantidadeHae']); ?></p>
            </div>

            <div class="detalhes-relatorio">
                <h4>Relatório</h4>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-<?php echo strtolower($relatorio['status']); ?>">
                        <?php echo $relatorio['status']; ?>
                    </span>
                </p>
                <p><strong>Data de Entrega:</strong> <?php echo date('d/m/Y', strtotime($relatorio['data_entrega'])); ?></p>
                
                <h5>Descrição das Atividades Realizadas:</h5>
                <p><?php echo nl2br(htmlspecialchars($relatorio['descricao_atividades'])); ?></p>
                
                <h5>Resultados Alcançados:</h5>
                <p><?php echo nl2br(htmlspecialchars($relatorio['resultados_alcancados'])); ?></p>
            </div>

            <?php if ($relatorio['observacoes_coordenador']): ?>
                <div class="observacoes">
                    <h4>Observações do Coordenador</h4>
                    <p><?php echo nl2br(htmlspecialchars($relatorio['observacoes_coordenador'])); ?></p>
                    <p><strong>Data da Avaliação:</strong> <?php echo date('d/m/Y H:i', strtotime($relatorio['data_avaliacao'])); ?></p>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px; text-align: center;">
                <a href="<?php echo strtolower($_SESSION['funcao']) === 'professor' ? 'relatorio_prof.php' : 'relatorio_coord.php'; ?>" 
                   class="btn-voltar" style="display: inline-block; padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">
                    Voltar
                </a>
            </div>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html> 