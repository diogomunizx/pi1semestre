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

    // Processa ações de encerrar/reabrir edital
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['acao']) && isset($_GET['id'])) {
        $acao = $_GET['acao'];
        $id_edital = $_GET['id'];

        try {
            // Verifica se o edital existe e obtém a data de fim de inscrição
            $query = "SELECT edital_status, dataFimInscricao FROM tb_Editais WHERE id_edital = :id_edital";
            $stmt = $conn->prepare($query);
            $stmt->execute(['id_edital' => $id_edital]);
            $edital = $stmt->fetch();

            if (!$edital) {
                throw new Exception("Edital não encontrado.");
            }

            if ($acao === 'reabrir') {
                // Verifica se o período de inscrição ainda está vigente
                if (strtotime($edital['dataFimInscricao']) < strtotime('today')) {
                    throw new Exception("Não é possível reabrir este edital pois o período de inscrição já expirou.");
                }

                $query = "UPDATE tb_Editais SET edital_status = 'ABERTO' WHERE id_edital = :id_edital";
                $stmt = $conn->prepare($query);
                $stmt->execute(['id_edital' => $id_edital]);
                $_SESSION['mensagem'] = "Edital reaberto com sucesso!";
            } elseif ($acao === 'encerrar') {
                $query = "UPDATE tb_Editais SET edital_status = 'ENCERRADO' WHERE id_edital = :id_edital";
                $stmt = $conn->prepare($query);
                $stmt->execute(['id_edital' => $id_edital]);
                $_SESSION['mensagem'] = "Edital encerrado com sucesso!";
            } else {
                throw new Exception("Ação inválida.");
            }
        } catch (Exception $e) {
            error_log("Erro ao processar edital: " . $e->getMessage());
            $_SESSION['erro'] = $e->getMessage();
        }

        // Redireciona de volta para editais.php após a ação
        header("Location: editais.php");
        exit;
    } else {
        $_SESSION['erro'] = "Parâmetros inválidos.";
    }

    // Processa criação/edição de edital
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validação dos campos
        if (empty($_POST['vigencia']) || empty($_POST['dataInicioInscricao']) || 
            empty($_POST['dataFimInscricao']) || empty($_POST['unidade'])) {
            throw new Exception("Todos os campos obrigatórios devem ser preenchidos.");
        }

        // Validação das datas
        $dataInicio = new DateTime($_POST['dataInicioInscricao']);
        $dataFim = new DateTime($_POST['dataFimInscricao']);

        if ($dataFim < $dataInicio) {
            throw new Exception("A data de término não pode ser anterior à data de início.");
        }

        // Prepara os dados
        $dados = [
            'vigencia' => $_POST['vigencia'],
            'dataInicioInscricao' => $_POST['dataInicioInscricao'],
            'dataFimInscricao' => $_POST['dataFimInscricao'],
            'unidade' => $_POST['unidade'],
            'observacoes' => $_POST['observacoes'] ?? null
        ];

        // Verifica se é edição ou criação
        if (isset($_POST['id_edital'])) {
            // Edição
            $query = "UPDATE tb_Editais SET 
                        vigencia = :vigencia,
                        dataInicioInscricao = :dataInicioInscricao,
                        dataFimInscricao = :dataFimInscricao,
                        Unidade_Fatec_idUnidade_Fatec = :unidade,
                        observacoes = :observacoes
                     WHERE id_edital = :id_edital";
            
            $dados['id_edital'] = $_POST['id_edital'];
            
            $stmt = $conn->prepare($query);
            $stmt->execute($dados);

            $_SESSION['mensagem'] = "Edital atualizado com sucesso!";
        } else {
            // Criação
            $query = "INSERT INTO tb_Editais (
                        vigencia,
                        dataInicioInscricao,
                        dataFimInscricao,
                        Unidade_Fatec_idUnidade_Fatec,
                        observacoes,
                        edital_status
                    ) VALUES (
                        :vigencia,
                        :dataInicioInscricao,
                        :dataFimInscricao,
                        :unidade,
                        :observacoes,
                        'ABERTO'
                    )";
            
            $stmt = $conn->prepare($query);
            $stmt->execute($dados);

            $_SESSION['mensagem'] = "Edital criado com sucesso!";
        }

        header("Location: editais.php");
        exit;
    }

} catch (Exception $e) {
    error_log("Erro ao processar edital: " . $e->getMessage());
    $_SESSION['erro'] = "Erro ao processar edital: " . $e->getMessage();
    header("Location: editais.php");
    exit;
} 