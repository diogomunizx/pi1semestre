<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

// Verifica se foi passado um ID de relatório
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID de relatório inválido.";
    header("Location: relatorio_prof.php");
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
                     c.Materia as curso,
                     coord.Nome as coordenador,
                     coord.email as email_coordenador
              FROM tb_relatorioHae r
              INNER JOIN tb_frm_inscricao_hae i ON r.id_frmInscricaoHae = i.id_frmInscricaoHae
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              INNER JOIN tb_Usuario coord ON c.id_docenteCoordenador = coord.id_Docente
              WHERE r.id_relatorioHae = :id_relatorio
              AND i.tb_Docentes_id_Docente = :id_docente";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_relatorio' => $_GET['id'],
        'id_docente' => $_SESSION['id_Docente']
    ]);

    $relatorio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$relatorio) {
        $_SESSION['erro'] = "Relatório não encontrado.";
        header("Location: relatorio_prof.php");
        exit;
    }
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes do relatório: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes do relatório.";
    header("Location: relatorio_prof.php");
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            margin-right: 10px;
        }

        .btn-voltar:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-imprimir {
            display: inline-block;
            padding: 10px 20px;
            background-color: #17a2b8;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-imprimir:hover {
            background-color: #138496;
            transform: translateY(-2px);
        }

        .btn-inscricao {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-inscricao:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .acoes-container {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }

        .status-pendente {
            background-color: #ffc107;
            color: #000;
        }

        .status-aprovado {
            background-color: #28a745;
            color: #fff;
        }

        .status-reprovado {
            background-color: #dc3545;
            color: #fff;
        }

        .status-correcao {
            background-color: #dc3545;
            color: #fff;
        }

        .texto-projeto {
            white-space: pre-wrap;
            font-size: 14px;
            line-height: 1.6;
            color: #444;
        }

        @media print {

            header,
            .sidebar,
            .acoes-container {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            main {
                margin-left: 0;
                padding: 20px;
            }

            .detalhes-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            /* Garantir que textos escuros sejam impressos */
            * {
                color: #000 !important;
                text-shadow: none !important;
                background: transparent !important;
            }

            /* Quebrar URLs longas */
            a[href]:after {
                content: " (" attr(href) ")";
            }

            /* Manter cores do status */
            .status-badge {
                border: 1px solid #000;
            }
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
        <a class="inicio" href="index_prof.php">
            <img src="../imagens/home.png" alt="Início"> <span>Início</span>
        </a>
        <a href="inscricao.php">
            <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
        </a>
        <a href="editais_prof.php">
            <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_prof.php" class="active">
            <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
        </a>
        <a href="dashboard_professor.php">
            <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="detalhes-container">
            <div class="detalhes-header">
                <h3>Detalhes do Relatório HAE</h3>
                <span class="status-badge status-<?php echo strtolower($inscricao['status']); ?>">
                    <?php
                    if ($inscricao['status'] === 'APROVADO') echo 'Deferido';
                    elseif ($inscricao['status'] === 'REPROVADO') echo 'Indeferido';
                    else echo $inscricao['status'];
                    ?>
                </span>
            </div>

            <div class="secao">
                <h4>Informações do Coordenador</h4>
                <div class="info-item"><strong>Nome:</strong> <?php echo htmlspecialchars($relatorio['coordenador']); ?></div>
                <div class="info-item"><strong>E-mail:</strong> <?php echo htmlspecialchars($relatorio['email_coordenador']); ?></div>
            </div>

            <div class="secao">
                <h4>Informações do Projeto</h4>
                <div class="info-item"><strong>Título:</strong> <?php echo htmlspecialchars($relatorio['tituloProjeto']); ?></div>
                <div class="info-item"><strong>Tipo HAE:</strong> <?php echo htmlspecialchars($relatorio['tipoHae']); ?></div>
                <div class="info-item"><strong>Quantidade HAE:</strong> <?php echo htmlspecialchars($relatorio['quantidadeHae']); ?></div>
                <div class="info-item"><strong>Curso:</strong> <?php echo htmlspecialchars($relatorio['curso']); ?></div>
            </div>

            <div class="secao">
                <h4>Detalhes do Relatório</h4>
                <div class="info-item">
                    <strong>Data de Entrega:</strong>
                    <?php echo date('d/m/Y', strtotime($relatorio['data_entrega'])); ?>
                </div>
                <div class="info-item">
                    <strong>Descrição das Atividades:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($relatorio['descricao_atividades'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Resultados Alcançados:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($relatorio['resultados_alcancados'])); ?></div>
                </div>
                <div class="info-item">
                    <strong>Dificuldades Encontradas:</strong><br>
                    <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($relatorio['dificuldades_encontradas'])); ?></div>
                </div>
            </div>

            <?php if ($relatorio['status'] !== 'PENDENTE'): ?>
                <div class="secao">
                    <h4>Avaliação do Coordenador</h4>
                    <div class="info-item">
                        <strong>Status:</strong>
                        <span class="status-badge status-<?php echo strtolower($inscricao['status']); ?>">
                            <?php
                            if ($inscricao['status'] === 'APROVADO') echo 'Deferido';
                            elseif ($inscricao['status'] === 'REPROVADO') echo 'Indeferido';
                            else echo $inscricao['status'];
                            ?>
                        </span>
                    </div>
                    <?php if (!empty($relatorio['observacoes'])): ?>
                        <div class="info-item">
                            <strong>Observações:</strong><br>
                            <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($relatorio['observacoes'])); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($relatorio['observacoes_coordenador'])): ?>
                        <div class="info-item">
                            <strong>Justificativa do Coordenador:</strong><br>
                            <div class="texto-projeto"><?php echo nl2br(htmlspecialchars($relatorio['observacoes_coordenador'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="acoes-container">
                <a href="javascript:history.back()" class="btn-voltar">
                    Voltar
                </a>
                <a href="ver_detalhes_inscricao_prof.php?id=<?php echo $relatorio['id_frmInscricaoHae']; ?>" class="btn-inscricao">
                    Ver Inscrição Original
                </a>
                <a href="javascript:void(0)" onclick="window.print()" class="btn-imprimir">
                    Imprimir Relatório
                </a>
            </div>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html>