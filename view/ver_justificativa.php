<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
    header("Location: ../login.php");
    exit;
}

// Verifica se o ID da inscrição foi fornecido
if (!isset($_GET['id'])) {
    header("Location: inscricao.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca a justificativa da inscrição
    $query = "SELECT j.justificativa, j.status, j.data_avaliacao, i.tipoHae
              FROM tb_frm_inscricao_hae i
              LEFT JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
              WHERE i.id_frmInscricaoHae = :id_inscricao
              AND i.tb_Docentes_id_Docente = :id_docente";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_inscricao' => $_GET['id'],
        'id_docente' => $_SESSION['id_Docente']
    ]);
    $resultado = $stmt->fetch();
    
    if (!$resultado) {
        throw new Exception("Inscrição não encontrada ou você não tem permissão para visualizá-la.");
    }
    
} catch (Exception $e) {
    error_log("Erro ao buscar justificativa: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar a justificativa. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HORUS - Justificativa</title>
    <style>
        .modal-justificativa {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header-justificativa {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
        }

        .status.pendente {
            background-color: #ffd700;
            color: #000;
        }

        .status.aprovado {
            background-color: #4CAF50;
            color: white;
        }

        .status.reprovado {
            background-color: #f44336;
            color: white;
        }

        .conteudo-justificativa {
            margin: 20px 0;
            line-height: 1.6;
        }

        .data-avaliacao {
            color: #666;
            font-size: 0.9em;
            margin-top: 20px;
        }

        .botao-voltar {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .botao-voltar:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="modal-justificativa">
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
            <a href="inscricao.php" class="botao-voltar">Voltar</a>
        <?php else: ?>
            <div class="header-justificativa">
                <h2>Justificativa - <?php echo htmlspecialchars($resultado['tipoHae']); ?></h2>
                <div class="status <?php echo strtolower($resultado['status'] ?? 'pendente'); ?>">
                    <?php echo htmlspecialchars($resultado['status'] ?? 'PENDENTE'); ?>
                </div>
            </div>

            <div class="conteudo-justificativa">
                <?php if ($resultado['status'] === null): ?>
                    <p>Sua inscrição ainda está em análise. Aguarde a avaliação do coordenador.</p>
                <?php else: ?>
                    <p><?php echo nl2br(htmlspecialchars($resultado['justificativa'])); ?></p>
                    <?php if ($resultado['data_avaliacao']): ?>
                        <div class="data-avaliacao">
                            Avaliado em: <?php echo date('d/m/Y H:i', strtotime($resultado['data_avaliacao'])); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <a href="inscricao.php" class="botao-voltar">Voltar</a>
        <?php endif; ?>
    </div>
</body>
</html> 