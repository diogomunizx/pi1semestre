<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: relatorio_coord.php");
    exit;
}

// Verifica se todos os campos necessários foram enviados
if (!isset($_POST['id_relatorio']) || !isset($_POST['observacoes']) || !isset($_POST['acao'])) {
    $_SESSION['erro'] = "Todos os campos são obrigatórios.";
    header("Location: relatorio_coord.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Inicia a transação
    $conn->beginTransaction();

    // Verifica se o relatório existe e pertence a um curso coordenado pelo coordenador
    $queryVerifica = "SELECT r.id_relatorioHae 
                     FROM tb_relatorioHae r
                     INNER JOIN tb_frm_inscricao_hae i ON r.id_frmInscricaoHae = i.id_frmInscricaoHae
                     INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
                     WHERE r.id_relatorioHae = :id_relatorio
                     AND c.id_docenteCoordenador = :id_coordenador
                     AND r.status = 'PENDENTE'";
    
    $stmtVerifica = $conn->prepare($queryVerifica);
    $stmtVerifica->execute([
        'id_relatorio' => $_POST['id_relatorio'],
        'id_coordenador' => $_SESSION['id_Docente']
    ]);
    
    if (!$stmtVerifica->fetch()) {
        throw new Exception("Relatório não encontrado ou você não tem permissão para avaliá-lo.");
    }

    // Define o status baseado na ação
    $status = ($_POST['acao'] === 'aprovar') ? 'APROVADO' : 'CORRECAO';

    // Atualiza o relatório
    $query = "UPDATE tb_relatorioHae 
             SET status = :status,
                 observacoes_coordenador = :observacoes,
                 data_avaliacao = NOW()
             WHERE id_relatorioHae = :id_relatorio";

    $stmt = $conn->prepare($query);
    $stmt->execute([
        'status' => $status,
        'observacoes' => $_POST['observacoes'],
        'id_relatorio' => $_POST['id_relatorio']
    ]);

    // Confirma a transação
    $conn->commit();

    $_SESSION['mensagem'] = "Relatório " . ($status === 'APROVADO' ? 'aprovado' : 'enviado para correção') . " com sucesso!";
    header("Location: relatorio_coord.php");
    exit;

} catch (Exception $e) {
    // Em caso de erro, desfaz a transação
    if (isset($conn)) {
        $conn->rollBack();
    }
    error_log("Erro ao processar avaliação: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao processar a avaliação: " . $e->getMessage();
    header("Location: relatorio_coord.php");
    exit;
} 