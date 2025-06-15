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
                     prof.email as email_professor,
                     e.vigencia as edital,
                     c.Materia as curso,
                     COALESCE(j.status, 'PENDENTE') as status
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
} catch (Exception $e) {
    error_log("Erro ao buscar detalhes da inscrição: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os detalhes da inscrição.";
    header("Location: aprovacao.php");
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
    <title>HORUS - Avaliar Inscrição</title>
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

        .btn-reprovar {
            background-color: #dc3545;
            color: white;
        }

        .btn-avaliar:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-aprovar:hover {
            background-color: #218838;
        }

        .btn-reprovar:hover {
            background-color: #c82333;
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
        <a href="aprovacao.php" class="active">
            <img src="../imagens/inscricoes.png" alt="Inscricoes"> <span>Inscrições</span>
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
        <div class="avaliacao-container">
            <div class="avaliacao-header">
                <h3>Avaliação de Inscrição HAE</h3>
                <span class="status-atual status-pendente">Status: PENDENTE</span>
            </div>

            <div class="info-projeto">
                <p><strong>Professor:</strong> <span><?php echo htmlspecialchars($inscricao['professor']); ?></span></p>
                <p><strong>Curso:</strong> <span><?php echo htmlspecialchars($inscricao['curso']); ?></span></p>
                <p><strong>Tipo HAE:</strong> <span><?php echo htmlspecialchars($inscricao['tipoHae']); ?></span></p>
                <p><strong>Quantidade HAE:</strong> <span><?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></span></p>
                <p><strong>Título do Projeto:</strong> <span><?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></span></p>
                <p><strong>Edital:</strong> <span><?php echo htmlspecialchars($inscricao['edital']); ?></span></p>
            </div>

            <div class="acoes-container">
                <div class="acoes-icones">
                    <a href="ver_detalhes_inscricao.php?id=<?php echo $inscricao['id_frmInscricaoHae']; ?>"
                        class="btn-ver-completa">Ver Inscrição Completa</a>
                </div>
            </div>

            <form action="processa_aprovacao.php" method="POST" id="formAvaliacao">
                <input type="hidden" name="id_inscricao" value="<?php echo $inscricao['id_frmInscricaoHae']; ?>">

                <div class="form-group">
                    <label for="justificativa">Justificativa da Avaliação:</label>
                    <textarea id="justificativa" name="justificativa" required
                        placeholder="Digite aqui sua justificativa para a aprovação ou reprovação desta inscrição..."></textarea>
                </div>

                <div class="avaliacao-actions">
                    <button type="submit" name="acao" value="aprovar" class="btn-avaliar btn-aprovar">Aprovar Inscrição</button>
                    <button type="submit" name="acao" value="reprovar" class="btn-avaliar btn-reprovar">Reprovar Inscrição</button>
                </div>
            </form>
        </div>
    </main>

    <script src="../js/script.js" defer></script>
    <script>
        // Adiciona validação do formulário
        document.getElementById('formAvaliacao').addEventListener('submit', function(e) {
            const justificativa = document.getElementById('justificativa').value.trim();

            if (!justificativa) {
                e.preventDefault();
                alert('Por favor, preencha a justificativa antes de aprovar ou reprovar a inscrição.');
                return false;
            }

            return true;
        });
    </script>
</body>

</html>