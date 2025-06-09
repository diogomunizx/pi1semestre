<?php
require_once '../config/session_config.php';

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['arquivo_pdf']) && isset($_POST['id_edital'])) {
    $id_edital = $_POST['id_edital'];
    $arquivo = $_FILES['arquivo_pdf'];

    // Verifica se é um PDF
    if ($arquivo['type'] !== 'application/pdf') {
        $_SESSION['erro'] = "Por favor, envie apenas arquivos PDF.";
        header("Location: editais.php");
        exit;
    }

    // Cria o diretório se não existir
    $diretorio = "../uploads/editais/";
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    // Gera um nome único para o arquivo
    $nome_arquivo = uniqid() . '_' . $arquivo['name'];
    $caminho_arquivo = $diretorio . $nome_arquivo;

    try {
        // Move o arquivo para o diretório
        if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            // Atualiza o banco de dados
            $query = "UPDATE tb_Editais SET arquivo_pdf = :arquivo WHERE id_edital = :id_edital";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                'arquivo' => $nome_arquivo,
                'id_edital' => $id_edital
            ]);

            $_SESSION['mensagem'] = "Arquivo do edital enviado com sucesso!";
        } else {
            throw new Exception("Erro ao mover o arquivo.");
        }
    } catch (Exception $e) {
        error_log("Erro ao fazer upload do arquivo: " . $e->getMessage());
        $_SESSION['erro'] = "Ocorreu um erro ao enviar o arquivo. Por favor, tente novamente.";
    }
} else {
    $_SESSION['erro'] = "Requisição inválida.";
}

header("Location: editais.php");
exit; 