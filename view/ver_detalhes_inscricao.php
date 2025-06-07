<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

// Verifica se foi passado um ID de inscrição
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID de inscrição inválido.";
    header("Location: aprovacao.php");
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca os detalhes completos da inscrição
    $query = "SELECT i.*, 
                     prof.Nome as professor,
                     prof.email as email_professor,
                     e.vigencia as edital,
                     c.Materia as curso,
                     j.status,
                     j.justificativa,
                     j.data_avaliacao
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              INNER JOIN tb_Editais e ON i.id_edital = e.id_edital
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              LEFT JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
              WHERE i.id_frmInscricaoHae = :id_inscricao";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_inscricao' => $_GET['id']]);
    
    $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$inscricao) {
        $_SESSION['erro'] = "Inscrição não encontrada.";
        header("Location: aprovacao.php");
        exit;
    }

    // Busca os horários de execução
    $queryHorarios = "SELECT * FROM tb_horarioExecHae 
                     WHERE tb_frm_inscricao_hae_id_frmInscricaoHae = :id_inscricao
                     ORDER BY diaSemana";
    $stmtHorarios = $conn->prepare($queryHorarios);
    $stmtHorarios->execute(['id_inscricao' => $_GET['id']]);
    $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes da inscrição: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes da inscrição.";
    header("Location: aprovacao.php");
    exit;
}

function formatarDiaSemana($dia) {
    $dias = [
        2 => 'Segunda-feira',
        3 => 'Terça-feira',
        4 => 'Quarta-feira',
        5 => 'Quinta-feira',
        6 => 'Sexta-feira',
        7 => 'Sábado'
    ];
    return $dias[$dia] ?? '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Detalhes da Inscrição</title>
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
        .status-reprovado { background-color: #dc3545; color: #fff; }

        .texto-projeto {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.6;
            color: #444;
        }

        .horarios-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .horarios-table th,
        .horarios-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .horarios-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
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
        <a href="aprovacao.php" class="active">
            <img src="../imagens/inscricoes.png" alt="Inscricoes"> <span>Inscrições</span>
        </a>
        <a href="relatorio_coord.php">
            <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="detalhes-container">
            <div class="detalhes-header">
                <h3>Detalhes da Inscrição HAE</h3>
                <span class="status-badge status-<?php echo strtolower($inscricao['status'] ?? 'pendente'); ?>">
                    <?php echo $inscricao['status'] ?? 'PENDENTE'; ?>
                </span>
            </div>

            <div class="secao">
                <h4>Informações do Professor</h4>
                <div class="info-item"><strong>Nome:</strong> <?php echo htmlspecialchars($inscricao['professor']); ?></div>
                <div class="info-item"><strong>E-mail:</strong> <?php echo htmlspecialchars($inscricao['email_professor']); ?></div>
            </div>

            <div class="secao">
                <h4>Informações do Projeto</h4>
                <div class="info-item"><strong>Título:</strong> <?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></div>
                <div class="info-item"><strong>Tipo HAE:</strong> <?php echo htmlspecialchars($inscricao['tipoHae']); ?></div>
                <div class="info-item"><strong>Quantidade HAE:</strong> <?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></div>
                <div class="info-item"><strong>Curso:</strong> <?php echo htmlspecialchars($inscricao['curso']); ?></div>
                <div class="info-item"><strong>Edital:</strong> <?php echo htmlspecialchars($inscricao['edital']); ?></div>
                <div class="info-item"><strong>Início do Projeto:</strong> <?php echo date('d/m/Y', strtotime($inscricao['inicioProjeto'])); ?></div>
                <div class="info-item"><strong>Fim do Projeto:</strong> <?php echo date('d/m/Y', strtotime($inscricao['fimProjeto'])); ?></div>
            </div>

            <div class="secao">
                <h4>Detalhes do Projeto</h4>
                <div class="info-item">
                    <strong>Metas:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['metasProjeto'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Objetivos:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['objetivoProjeto'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Justificativa:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['justificativaProjeto'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Metodologia:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['metodologia'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Recursos Materiais:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['recursosMateriais'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Resultados Esperados:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao['resultadoEsperado'])); ?></div>
                </div>
            </div>

            <div class="secao">
                <h4>Cronograma</h4>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <?php if (!empty($inscricao["cronogramaMes$i"])): ?>
                        <div class="info-item">
                            <strong>Mês <?php echo $i; ?>:</strong><br>
                            <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($inscricao["cronogramaMes$i"])); ?></div>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <div class="secao">
                <h4>Horários de Execução</h4>
                <table class="horarios-table">
                    <thead>
                        <tr>
                            <th>Dia da Semana</th>
                            <th>Horário Início</th>
                            <th>Horário Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($horarios as $horario): ?>
                            <tr>
                                <td><?php echo formatarDiaSemana($horario['diaSemana']); ?></td>
                                <td><?php echo substr($horario['horarioInicio'], 0, 5); ?></td>
                                <td><?php echo substr($horario['horarioFinal'], 0, 5); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($inscricao['status'] !== 'PENDENTE'): ?>
            <div class="secao">
                <h4>Avaliação do Coordenador</h4>
                <div class="info-item">
                    <strong>Status:</strong>
                    <span class="status-badge status-<?php echo strtolower($inscricao['status']); ?>">
                        <?php echo $inscricao['status']; ?>
                    </span>
                </div>
                <div class="info-item">
                    <strong>Data da Avaliação:</strong> 
                    <?php echo date('d/m/Y', strtotime($inscricao['data_avaliacao'])); ?>
                </div>
                <div class="info-item">
                    <strong>Justificativa:</strong><br>
                    <div class="texto-projeto">
                        <?php echo nl2br(htmlspecialchars($inscricao['justificativa'] ?? 'Nenhuma justificativa fornecida.')); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 30px;">
                <?php if ($inscricao['status'] === 'PENDENTE'): ?>
                    <a href="avaliar_inscricao.php?id=<?php echo $inscricao['id_frmInscricaoHae']; ?>" class="btn-voltar">
                        Voltar para Avaliação
                    </a>
                <?php else: ?>
                    <a href="aprovacao.php" class="btn-voltar">
                        Voltar para Lista de Inscrições
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html> 