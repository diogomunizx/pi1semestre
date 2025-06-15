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
    
    // Busca as inscrições que precisam ser aprovadas por este coordenador
    $query = "SELECT i.id_frmInscricaoHae,
                     prof.Nome as professor,
                     i.tipoHae,
                     i.quantidadeHae,
                     i.tituloProjeto,
                     e.vigencia as edital,
                     COALESCE(j.status, 'PENDENTE') as status
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              INNER JOIN tb_Usuario prof ON i.tb_Docentes_id_Docente = prof.id_Docente
              LEFT JOIN tb_Editais e ON i.id_edital = e.id_edital
              LEFT JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
              WHERE c.id_docenteCoordenador = :id_coordenador
              ORDER BY i.id_frmInscricaoHae DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_coordenador' => $_SESSION['id_Docente']]);
    $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug para ver os dados
    error_log("Coordenador ID: " . $_SESSION['id_Docente']);
    error_log("Inscrições encontradas: " . print_r($inscricoes, true));
    
} catch (Exception $e) {
    error_log("Erro ao buscar inscrições: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar as inscrições. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Aprovação de HAE</title>
    <style>
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
        .status-reprovado { background-color: #dc3545; }

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
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-fatec_itapira.png"></a>
                </div>
                <div class="cps">
                    <a href="https://www.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-cps.png"></a>
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
        <h3 class="titulos">Inscrições para Aprovação</h3>
        <br>
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="erro"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php elseif (empty($inscricoes)): ?>
            <p>Não há inscrições pendentes para aprovação.</p>
        <?php else: ?>
            <table class="tbls">
                <thead>
                    <tr>
                        <td>Inscrição</td>
                        <td>Professor</td>
                        <td>Tipo HAE</td>
                        <td>Quantidade HAE</td>
                        <td>Edital</td>
                        <td>Título do Projeto</td>
                        <td>Status</td>
                        <td>Ações</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscricoes as $inscricao): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($inscricao['id_frmInscricaoHae']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['professor']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['tipoHae']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['edital']); ?></td>
                            <td><?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($inscricao['status']); ?>">
                                    <?php 
                                        $status = strtoupper($inscricao['status']);
                                        if ($status === 'APROVADO') echo 'Deferido';
                                        elseif ($status === 'REPROVADO') echo 'Indeferido';
                                        else echo $inscricao['status'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($inscricao['status'] === 'PENDENTE'): ?>
                                    <a href="avaliar_inscricao.php?id=<?php echo $inscricao['id_frmInscricaoHae']; ?>" 
                                       class="btn-avaliar">Avaliar</a>
                                <?php else: ?>
                                    <a href="ver_detalhes_inscricao.php?id=<?php echo $inscricao['id_frmInscricaoHae']; ?>" 
                                       class="btn-ver">Ver Detalhes</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>

    <script src="../js/script.js" defer></script>
</body>

</html>