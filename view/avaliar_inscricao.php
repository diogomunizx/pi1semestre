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
    
    // Busca os detalhes da inscrição
    $query = "SELECT i.*, 
                     prof.Nome as professor,
                     c.Materia as curso,
                     e.vigencia as edital
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              LEFT JOIN tb_Editais e ON i.id_edital = e.id_edital
              WHERE i.id_frmInscricaoHae = :id_inscricao
              AND c.id_docenteCoordenador = :id_coordenador";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_inscricao' => $_GET['id'],
        'id_coordenador' => $_SESSION['id_Docente']
    ]);
    
    $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$inscricao) {
        $_SESSION['erro'] = "Inscrição não encontrada ou você não tem permissão para avaliá-la.";
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
    <title>HORUS - Avaliar Inscrição</title>
    <style>
        .avaliacao-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .detalhes-inscricao {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .form-avaliacao {
            margin-top: 20px;
        }
        
        .form-avaliacao textarea {
            width: 100%;
            min-height: 100px;
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .botoes-avaliacao {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .btn-aprovar {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-reprovar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .horarios-execucao {
            margin: 15px 0;
        }
        
        .horario-item {
            margin: 5px 0;
            padding: 5px;
            background: #f8f9fa;
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
        <a href="aprovacao.php" id="linkAprovacao" class="active">
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
        <div class="avaliacao-container">
            <h3>Avaliar Inscrição</h3>
            
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
            <?php endif; ?>

            <div class="detalhes-inscricao">
                <h4>Detalhes da Inscrição</h4>
                <p><strong>Professor:</strong> <?php echo htmlspecialchars($inscricao['professor']); ?></p>
                <p><strong>Curso:</strong> <?php echo htmlspecialchars($inscricao['curso']); ?></p>
                <p><strong>Edital:</strong> <?php echo htmlspecialchars($inscricao['edital']); ?></p>
                <p><strong>Tipo HAE:</strong> <?php echo htmlspecialchars($inscricao['tipoHae']); ?></p>
                <p><strong>Quantidade HAE:</strong> <?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></p>
                <p><strong>Título do Projeto:</strong> <?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></p>
                <p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($inscricao['inicioProjeto'])) . ' a ' . date('d/m/Y', strtotime($inscricao['fimProjeto'])); ?></p>
                
                <div class="horarios-execucao">
                    <h4>Horários de Execução</h4>
                    <?php foreach ($horarios as $horario): ?>
                        <div class="horario-item">
                            <?php echo formatarDiaSemana($horario['diaSemana']); ?>: 
                            <?php echo substr($horario['horarioInicio'], 0, 5); ?> às 
                            <?php echo substr($horario['horarioFinal'], 0, 5); ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h4>Detalhes do Projeto</h4>
                <p><strong>Metas:</strong> <?php echo nl2br(htmlspecialchars($inscricao['metasProjeto'])); ?></p>
                <p><strong>Objetivos:</strong> <?php echo nl2br(htmlspecialchars($inscricao['objetivoProjeto'])); ?></p>
                <p><strong>Justificativa:</strong> <?php echo nl2br(htmlspecialchars($inscricao['justificativaProjeto'])); ?></p>
                <p><strong>Recursos Materiais:</strong> <?php echo nl2br(htmlspecialchars($inscricao['recursosMateriais'])); ?></p>
                <p><strong>Resultados Esperados:</strong> <?php echo nl2br(htmlspecialchars($inscricao['resultadoEsperado'])); ?></p>
                <p><strong>Metodologia:</strong> <?php echo nl2br(htmlspecialchars($inscricao['metodologia'])); ?></p>

                <h4>Cronograma</h4>
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <?php if (!empty($inscricao["cronogramaMes$i"])): ?>
                        <p><strong>Mês <?php echo $i; ?>:</strong> <?php echo nl2br(htmlspecialchars($inscricao["cronogramaMes$i"])); ?></p>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <form class="form-avaliacao" action="processa_aprovacao.php" method="POST">
                <input type="hidden" name="id_inscricao" value="<?php echo $inscricao['id_frmInscricaoHae']; ?>">
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="APROVADO">Aprovar</option>
                        <option value="REPROVADO">Reprovar</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="justificativa">Justificativa:</label>
                    <textarea id="justificativa" name="justificativa" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit">Confirmar</button>
                    <a href="aprovacao.php" class="btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html> 