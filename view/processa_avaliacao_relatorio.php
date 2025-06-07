<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

// Verifica se recebeu os dados necessários
if (!isset($_POST['id_relatorio']) || !isset($_POST['acao']) || !isset($_POST['justificativa'])) {
    $_SESSION['erro'] = "Dados incompletos para avaliação do relatório.";
    header("Location: relatorio_coord.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Define o status baseado na ação
    $status = ($_POST['acao'] === 'aprovar') ? 'APROVADO' : 'CORRECAO';
    
    // Atualiza o status e observações do relatório
    $query = "UPDATE tb_relatorioHae 
              SET status = :status,
                  observacoes_coordenador = :justificativa,
                  data_avaliacao = NOW()
              WHERE id_relatorioHae = :id_relatorio";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'id_relatorio' => $_POST['id_relatorio'],
        'status' => $status,
        'justificativa' => $_POST['justificativa']
    ]);
    
    $_SESSION['mensagem'] = "Relatório " . ($status === 'APROVADO' ? 'aprovado' : 'enviado para correção') . " com sucesso!";
    
} catch (Exception $e) {
    error_log("Erro ao processar avaliação do relatório: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao processar a avaliação do relatório.";
}

header("Location: relatorio_coord.php");
exit;
?> 