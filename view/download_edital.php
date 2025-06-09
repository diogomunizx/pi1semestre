<?php
require_once '../config/session_config.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if (isset($_GET['id'])) {
    $id_edital = $_GET['id'];

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Busca o arquivo do edital
        $query = "SELECT arquivo_pdf FROM tb_Editais WHERE id_edital = :id_edital";
        $stmt = $conn->prepare($query);
        $stmt->execute(['id_edital' => $id_edital]);
        $edital = $stmt->fetch();

        if ($edital && !empty($edital['arquivo_pdf'])) {
            $arquivo = "../uploads/editais/" . $edital['arquivo_pdf'];
            
            if (file_exists($arquivo)) {
                // Define os headers para download do PDF
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($edital['arquivo_pdf']) . '"');
                header('Content-Length: ' . filesize($arquivo));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                
                // Lê e envia o arquivo
                readfile($arquivo);
                exit;
            }
        }
    } catch (Exception $e) {
        error_log("Erro ao fazer download do arquivo: " . $e->getMessage());
    }
}

// Se algo der errado, redireciona com erro
$_SESSION['erro'] = "Não foi possível fazer o download do arquivo.";
header("Location: editais_prof.php");
exit; 