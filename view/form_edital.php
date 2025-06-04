<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

$edital = null;
$modo = "Novo";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Se for edição, busca os dados do edital
    if (isset($_GET['id'])) {
        $query = "SELECT * FROM tb_Editais WHERE id_edital = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['id' => $_GET['id']]);
        $edital = $stmt->fetch();
        
        if ($edital) {
            $modo = "Editar";
        }
    }

    // Busca as unidades FATEC
    $queryUnidades = "SELECT id_unidadeFatec, Nome_Fantasia FROM tb_unidadeFatec ORDER BY Nome_Fantasia";
    $stmtUnidades = $conn->prepare($queryUnidades);
    $stmtUnidades->execute();
    $unidades = $stmtUnidades->fetchAll();

} catch (Exception $e) {
    error_log("Erro ao carregar dados: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os dados. Por favor, tente novamente mais tarde.";
    header("Location: editais.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - <?php echo $modo; ?> Edital</title>
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
        <a href="editais.php" class="active">
            <img src="../imagens/edital.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_coord.php">
            <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <h3 class="titulos"><?php echo $modo; ?> Edital</h3>
        <br>

        <form action="processa_edital.php" method="POST" class="form-edital">
            <?php if ($edital): ?>
                <input type="hidden" name="id_edital" value="<?php echo $edital['id_edital']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="vigencia">Vigência:</label>
                <input type="text" id="vigencia" name="vigencia" required
                       value="<?php echo $edital ? $edital['vigencia'] : ''; ?>"
                       placeholder="Ex: 1º Semestre 2024">
            </div>

            <div class="form-group">
                <label for="dataInicioInscricao">Data de Início das Inscrições:</label>
                <input type="date" id="dataInicioInscricao" name="dataInicioInscricao" required
                       value="<?php echo $edital ? $edital['dataInicioInscricao'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="dataFimInscricao">Data de Término das Inscrições:</label>
                <input type="date" id="dataFimInscricao" name="dataFimInscricao" required
                       value="<?php echo $edital ? $edital['dataFimInscricao'] : ''; ?>">
            </div>

            <div class="form-group">
                <label for="unidade">Unidade FATEC:</label>
                <select id="unidade" name="unidade" required>
                    <option value="">Selecione uma unidade</option>
                    <?php foreach ($unidades as $unidade): ?>
                        <option value="<?php echo $unidade['id_unidadeFatec']; ?>"
                                <?php echo ($edital && $edital['Unidade_Fatec_idUnidade_Fatec'] == $unidade['id_unidadeFatec']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($unidade['Nome_Fantasia']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes" rows="4"><?php echo $edital ? $edital['observacoes'] : ''; ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-salvar">Salvar</button>
                <a href="editais.php" class="btn-cancelar">Cancelar</a>
            </div>
        </form>
    </main>

    <style>
    .form-edital {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-salvar,
    .btn-cancelar {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-salvar {
        background-color: #4CAF50;
        color: white;
    }

    .btn-cancelar {
        background-color: #f44336;
        color: white;
    }

    .btn-salvar:hover {
        background-color: #45a049;
    }

    .btn-cancelar:hover {
        background-color: #da190b;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.form-edital');
        form.addEventListener('submit', function(e) {
            const inicio = new Date(document.getElementById('dataInicioInscricao').value);
            const fim = new Date(document.getElementById('dataFimInscricao').value);

            if (fim < inicio) {
                e.preventDefault();
                alert('A data de término não pode ser anterior à data de início.');
            }
        });
    });
    </script>
</body>

</html> 