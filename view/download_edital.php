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
    error_log("=== Iniciando download do edital ID: " . $id_edital . " ===");
    error_log("Função do usuário: " . $_SESSION['funcao']);
    error_log("PHP Version: " . PHP_VERSION);
    error_log("Memory Limit: " . ini_get('memory_limit'));
    error_log("Max Execution Time: " . ini_get('max_execution_time'));
    error_log("Upload Max Filesize: " . ini_get('upload_max_filesize'));
    error_log("Post Max Size: " . ini_get('post_max_size'));

    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Busca o arquivo do edital
        $query = "SELECT arquivo_pdf, vigencia FROM tb_Editais WHERE id_edital = :id_edital";
        $stmt = $conn->prepare($query);
        $stmt->execute(['id_edital' => $id_edital]);
        $edital = $stmt->fetch();

        error_log("Dados do edital encontrados: " . print_r($edital, true));

        if ($edital && !empty($edital['arquivo_pdf'])) {
            // Define o caminho do arquivo usando o mesmo caminho relativo do upload
            $arquivo = "../uploads/editais/" . $edital['arquivo_pdf'];
            
            error_log("Caminho do arquivo: " . $arquivo);
            error_log("Diretório atual: " . getcwd());
            error_log("Permissões do diretório: " . substr(sprintf('%o', fileperms(dirname($arquivo))), -4));
            error_log("Permissões do arquivo: " . (file_exists($arquivo) ? substr(sprintf('%o', fileperms($arquivo)), -4) : 'arquivo não existe'));
            
            if (file_exists($arquivo)) {
                error_log("Arquivo encontrado, tamanho: " . filesize($arquivo) . " bytes");
                error_log("Arquivo é legível: " . (is_readable($arquivo) ? 'sim' : 'não'));
                
                // Verifica se o arquivo é realmente um PDF
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $arquivo);
                finfo_close($finfo);
                error_log("Tipo MIME do arquivo: " . $mime_type);
                
                if ($mime_type !== 'application/pdf') {
                    throw new Exception("O arquivo não é um PDF válido. Tipo MIME: " . $mime_type);
                }
                
                // Limpa qualquer saída anterior
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                // Define um nome de arquivo mais amigável para download
                $nome_arquivo = 'Edital_' . $edital['vigencia'] . '.pdf';
                $nome_arquivo = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nome_arquivo);
                
                // Define os headers para download do PDF
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $nome_arquivo . '"');
                header('Content-Length: ' . filesize($arquivo));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                header('X-Accel-Buffering: no'); // Desativa o buffering do nginx se estiver usando
                
                error_log("Headers definidos, iniciando download...");
                
                // Tenta abrir o arquivo em modo binário
                $handle = fopen($arquivo, 'rb');
                if ($handle === false) {
                    throw new Exception("Não foi possível abrir o arquivo para leitura");
                }
                
                // Lê e envia o arquivo em chunks
                while (!feof($handle)) {
                    $buffer = fread($handle, 8192);
                    if ($buffer === false) {
                        throw new Exception("Erro ao ler o arquivo");
                    }
                    echo $buffer;
                    flush();
                }
                
                fclose($handle);
                error_log("Download concluído com sucesso");
                exit;
            } else {
                error_log("ERRO: Arquivo não encontrado no caminho: " . $arquivo);
                throw new Exception("Arquivo não encontrado no servidor.");
            }
        } else {
            error_log("ERRO: Nenhum arquivo encontrado no banco para o edital ID: " . $id_edital);
            throw new Exception("Arquivo não encontrado no banco de dados.");
        }
    } catch (Exception $e) {
        error_log("ERRO CRÍTICO: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        $_SESSION['erro'] = "Não foi possível fazer o download do arquivo: " . $e->getMessage();
        header("Location: " . (strtolower($_SESSION['funcao']) === 'coordenador' ? 'editais.php' : 'editais_prof.php'));
        exit;
    }
} else {
    error_log("ERRO: ID do edital não fornecido na requisição");
    $_SESSION['erro'] = "ID do edital não fornecido.";
    header("Location: " . (strtolower($_SESSION['funcao']) === 'coordenador' ? 'editais.php' : 'editais_prof.php'));
    exit;
} 