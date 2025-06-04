<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se todos os campos necessários foram enviados
    if (!isset($_POST['id_inscricao']) || !isset($_POST['descricao_atividades']) || 
        !isset($_POST['resultados_alcancados']) || !isset($_POST['data_entrega'])) {
        $_SESSION['erro'] = "Todos os campos são obrigatórios.";
        header("Location: relatorio_prof.php");
        exit;
    }

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Inicia a transação
        $conn->beginTransaction();

        // Verifica se a inscrição existe e pertence ao professor
        $queryVerifica = "SELECT i.id_frmInscricaoHae 
                         FROM tb_frm_inscricao_hae i
                         INNER JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
                         WHERE i.id_frmInscricaoHae = :id_inscricao
                         AND i.tb_Docentes_id_Docente = :id_docente
                         AND j.status = 'APROVADO'";
        
        $stmtVerifica = $conn->prepare($queryVerifica);
        $stmtVerifica->execute([
            'id_inscricao' => $_POST['id_inscricao'],
            'id_docente' => $_SESSION['id_Docente']
        ]);
        
        if (!$stmtVerifica->fetch()) {
            throw new Exception("Inscrição não encontrada ou você não tem permissão para enviar relatório.");
        }

        // Verifica se já existe um relatório para esta inscrição
        $queryRelatorio = "SELECT id_relatorioHae, status 
                          FROM tb_relatorioHae 
                          WHERE id_frmInscricaoHae = :id_inscricao";
        $stmtRelatorio = $conn->prepare($queryRelatorio);
        $stmtRelatorio->execute(['id_inscricao' => $_POST['id_inscricao']]);
        $relatorioExistente = $stmtRelatorio->fetch();

        if ($relatorioExistente) {
            // Se existe e não está em correção, não permite novo envio
            if ($relatorioExistente['status'] !== 'CORRECAO') {
                throw new Exception("Já existe um relatório enviado para esta inscrição.");
            }
            
            // Atualiza o relatório existente
            $query = "UPDATE tb_relatorioHae 
                     SET descricao_atividades = :descricao,
                         resultados_alcancados = :resultados,
                         data_entrega = :data_entrega,
                         status = 'PENDENTE',
                         observacoes_coordenador = NULL,
                         data_avaliacao = NULL
                     WHERE id_relatorioHae = :id_relatorio";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                'descricao' => $_POST['descricao_atividades'],
                'resultados' => $_POST['resultados_alcancados'],
                'data_entrega' => $_POST['data_entrega'],
                'id_relatorio' => $relatorioExistente['id_relatorioHae']
            ]);
        } else {
            // Insere um novo relatório
            $query = "INSERT INTO tb_relatorioHae (
                        id_frmInscricaoHae,
                        descricao_atividades,
                        resultados_alcancados,
                        data_entrega,
                        status
                    ) VALUES (
                        :id_inscricao,
                        :descricao,
                        :resultados,
                        :data_entrega,
                        'PENDENTE'
                    )";

            $stmt = $conn->prepare($query);
            $stmt->execute([
                'id_inscricao' => $_POST['id_inscricao'],
                'descricao' => $_POST['descricao_atividades'],
                'resultados' => $_POST['resultados_alcancados'],
                'data_entrega' => $_POST['data_entrega']
            ]);
        }

        // Confirma a transação
        $conn->commit();

        $_SESSION['mensagem'] = "Relatório enviado com sucesso!";
        header("Location: relatorio_prof.php");
        exit;

    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação
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