<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

// Verifica se foi passado um ID de inscrição
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['erro'] = "ID de inscrição inválido.";
    header("Location: relatorio_prof.php");
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Busca os detalhes da inscrição
    $query = "SELECT i.*, 
                     prof.Nome as professor,
                     prof.email as email_professor,
                     e.vigencia as edital,
                     c.Materia as curso,
                     coord.Nome as coordenador
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              INNER JOIN tb_Editais e ON i.id_edital = e.id_edital
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              INNER JOIN tb_Usuario coord ON c.id_docenteCoordenador = coord.id_Docente
              WHERE i.id_frmInscricaoHae = :id_inscricao
              AND i.tb_Docentes_id_Docente = :id_docente";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_inscricao' => $_GET['id'],
        'id_docente' => $_SESSION['id_Docente']
    ]);

    $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inscricao) {
        $_SESSION['erro'] = "Inscrição não encontrada ou você não tem permissão para acessá-la.";
        header("Location: relatorio_prof.php");
        exit;
    }

    // Se for edição, busca o relatório existente
    if (isset($_GET['edit']) && $_GET['edit'] === 'true') {
        $queryRelatorio = "SELECT * FROM tb_relatorioHae 
                          WHERE id_frmInscricaoHae = :id_inscricao";
        $stmtRelatorio = $conn->prepare($queryRelatorio);
        $stmtRelatorio->execute(['id_inscricao' => $_GET['id']]);
        $relatorio = $stmtRelatorio->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes da inscrição: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes da inscrição.";
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
    <title>HORUS - Envio de Relatório</title>
    <style>
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-header h3 {
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

        .form-group input[type="date"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
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

        .btn-enviar {
            background-color: #28a745;
            color: white;
        }

        .btn-cancelar {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-enviar:hover {
            background-color: #218838;
        }

        .btn-cancelar:hover {
            background-color: #5a6268;
        }

        .field-description {
            font-size: 13px;
            color: #6c757d;
            margin: 5px 0 10px;
            font-style: italic;
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
            <img src="../imagens/dashboard2.png" alt="Dashboard"> <span>Dashboard</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="form-container">
            <div class="form-header">
                <h3><?php echo isset($_GET['edit']) ? 'Editar' : 'Novo'; ?> Relatório HAE</h3>
            </div>

            <div class="info-projeto">
                <p><strong>Professor:</strong> <span><?php echo htmlspecialchars($inscricao['professor']); ?></span></p>
                <p><strong>Curso:</strong> <span><?php echo htmlspecialchars($inscricao['curso']); ?></span></p>
                <p><strong>Tipo HAE:</strong> <span><?php echo htmlspecialchars($inscricao['tipoHae']); ?></span></p>
                <p><strong>Quantidade HAE:</strong> <span><?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></span></p>
                <p><strong>Título do Projeto:</strong> <span><?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></span></p>
                <p><strong>Coordenador:</strong> <span><?php echo htmlspecialchars($inscricao['coordenador']); ?></span></p>
            </div>

            <form action="processa_relatorio.php" method="POST" id="formRelatorio">
                <input type="hidden" name="id_inscricao" value="<?php echo $inscricao['id_frmInscricaoHae']; ?>">
                <?php if (isset($_GET['edit'])): ?>
                    <input type="hidden" name="edit" value="true">
                <?php endif; ?>

                <div class="form-group">
                    <label for="descricao_atividades">Descrição das Atividades Realizadas:</label>
                    <p class="field-description">Descreva detalhadamente as atividades desenvolvidas durante o projeto.</p>
                    <textarea name="descricao_atividades" id="descricao_atividades" required><?php echo isset($relatorio) ? htmlspecialchars($relatorio['descricao_atividades']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="resultados_alcancados">Resultados Alcançados:</label>
                    <p class="field-description">Descreva os resultados obtidos e como eles se relacionam com os objetivos iniciais do projeto.</p>
                    <textarea name="resultados_alcancados" id="resultados_alcancados" required><?php echo isset($relatorio) ? htmlspecialchars($relatorio['resultados_alcancados']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="data_entrega">Data de Entrega:</label>
                    <input type="date" id="data_entrega" name="data_entrega" required
                        value="<?php echo isset($relatorio) ? $relatorio['data_entrega'] : date('Y-m-d'); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-enviar">
                        <?php echo isset($_GET['edit']) ? 'Atualizar' : 'Enviar'; ?> Relatório
                    </button>
                    <a href="relatorio_prof.php" class="btn btn-cancelar">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define a data mínima como hoje
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById('data_entrega').min = hoje;

            // Validação do formulário
            const form = document.getElementById('formRelatorio');
            form.addEventListener('submit', function(e) {
                const descricao = document.getElementById('descricao_atividades').value.trim();
                const resultados = document.getElementById('resultados_alcancados').value.trim();
                const data = document.getElementById('data_entrega').value;

                if (!descricao || !resultados || !data) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return false;
                }

                return true;
            });
        });
    </script>

    <script src="../js/script.js" defer></script>
</body>

</html>