<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Verifica se todos os campos necessários foram enviados
        if (!isset($_POST['id_inscricao'], $_POST['descricao_atividades'], 
                  $_POST['resultados_alcancados'], $_POST['data_entrega'])) {
            throw new Exception("Dados incompletos");
        }

        // Inicia a transação
        $conn->beginTransaction();

        // Verifica se é uma edição ou novo relatório
        if (isset($_POST['edit']) && $_POST['edit'] === 'true') {
            $query = "UPDATE tb_relatorioHae 
                     SET descricao_atividades = :descricao,
                         resultados_alcancados = :resultados,
                         data_entrega = :data,
                         status = 'PENDENTE'
                     WHERE id_frmInscricaoHae = :id_inscricao";
        } else {
            $query = "INSERT INTO tb_relatorioHae 
                     (descricao_atividades, resultados_alcancados, data_entrega, 
                      status, id_frmInscricaoHae)
                     VALUES 
                     (:descricao, :resultados, :data, 'PENDENTE', :id_inscricao)";
        }

        $stmt = $conn->prepare($query);
        $params = [
            'descricao' => $_POST['descricao_atividades'],
            'resultados' => $_POST['resultados_alcancados'],
            'data' => $_POST['data_entrega'],
            'id_inscricao' => $_POST['id_inscricao']
        ];

        $stmt->execute($params);

        // Confirma a transação
        $conn->commit();

        $_SESSION['mensagem'] = isset($_POST['edit']) ? 
            "Relatório atualizado com sucesso!" : 
            "Relatório enviado com sucesso!";
        header("Location: relatorio_prof.php");
        exit;

    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        error_log("Erro ao processar relatório: " . $e->getMessage());
        $_SESSION['erro'] = "Ocorreu um erro ao processar o relatório: " . $e->getMessage();
        header("Location: relatorio_prof.php");
        exit;
    }
} else {
    header("Location: relatorio_prof.php");
    exit;
} 