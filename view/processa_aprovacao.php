<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Verifica se todos os campos necessários foram enviados
        if (!isset($_POST['id_inscricao'], $_POST['status'], $_POST['justificativa'])) {
            throw new Exception("Dados incompletos");
        }

        // Inicia a transação
        $conn->beginTransaction();

        // Verifica se já existe uma justificativa para esta inscrição
        $queryCheck = "SELECT id_justificativaHae FROM tb_justificativaHae 
                      WHERE id_frmInscricaoHae = :id_inscricao";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->execute(['id_inscricao' => $_POST['id_inscricao']]);
        $existingJustificativa = $stmtCheck->fetch();

        if ($existingJustificativa) {
            // Atualiza a justificativa existente
            $query = "UPDATE tb_justificativaHae 
                     SET justificativa = :justificativa,
                         status = :status,
                         data_avaliacao = NOW()
                     WHERE id_frmInscricaoHae = :id_inscricao";
        } else {
            // Insere nova justificativa
            $query = "INSERT INTO tb_justificativaHae 
                     (justificativa, status, data_avaliacao, id_frmInscricaoHae, id_docenteCoordenador)
                     VALUES 
                     (:justificativa, :status, NOW(), :id_inscricao, :id_coordenador)";
        }

        $stmt = $conn->prepare($query);
        $params = [
            'justificativa' => $_POST['justificativa'],
            'status' => $_POST['status'],
            'id_inscricao' => $_POST['id_inscricao'],
        ];

        if (!$existingJustificativa) {
            $params['id_coordenador'] = $_SESSION['id_Docente'];
        }

        $stmt->execute($params);

        // Confirma a transação
        $conn->commit();

        $_SESSION['mensagem'] = "Inscrição " . strtolower($_POST['status']) . " com sucesso!";
        header("Location: aprovacao.php");
        exit;

    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        error_log("Erro ao processar avaliação: " . $e->getMessage());
        $_SESSION['erro'] = "Ocorreu um erro ao processar a avaliação: " . $e->getMessage();
        header("Location: aprovacao.php");
        exit;
    }
} else {
    header("Location: aprovacao.php");
    exit;
} 