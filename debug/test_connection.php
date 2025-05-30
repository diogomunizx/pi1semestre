<?php
require_once '../model/Database.php';

try {
    echo "<pre>";
    echo "Testando conexão com o banco de dados...\n\n";
    
    echo "Informações de conexão:\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Usuário: " . DB_USER . "\n\n";
    
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Conexão estabelecida com sucesso!\n\n";
    
    // Testa se a tabela existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'tb_Usuario'");
    $table = $stmt->fetch();
    
    if ($table) {
        echo "Tabela tb_Usuario encontrada!\n";
        
        // Conta quantos usuários existem
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tb_Usuario");
        $count = $stmt->fetch();
        echo "Total de usuários cadastrados: " . $count['total'] . "\n";
        
        // Mostra os valores possíveis para a coluna funcao
        $stmt = $pdo->query("SELECT DISTINCT funcao FROM tb_Usuario");
        $funcoes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "\nFunções encontradas no banco:\n";
        foreach ($funcoes as $funcao) {
            echo "- [" . $funcao . "]\n";
        }
    } else {
        echo "ERRO: Tabela tb_Usuario não encontrada!\n";
    }
    
    echo "</pre>";
} catch (Exception $e) {
    echo "<pre>";
    echo "Erro ao conectar com o banco de dados:\n";
    echo $e->getMessage() . "\n";
    echo "</pre>";
} 