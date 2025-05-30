<?php
require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Consulta todos os usuários
    $stmt = $pdo->query("SELECT id_Docente, usuario, Nome, funcao FROM tb_Usuario");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    echo "=== Usuários cadastrados ===\n\n";
    
    if (empty($usuarios)) {
        echo "Nenhum usuário encontrado na tabela tb_Usuario\n";
    } else {
        foreach ($usuarios as $user) {
            echo "ID: " . $user['id_Docente'] . "\n";
            echo "Usuário: " . $user['usuario'] . "\n";
            echo "Nome: " . $user['Nome'] . "\n";
            echo "Função: [" . $user['funcao'] . "]\n";
            echo "Função (minúsculo): [" . strtolower($user['funcao']) . "]\n";
            echo "Função (maiúsculo): [" . strtoupper($user['funcao']) . "]\n";
            echo str_repeat("-", 50) . "\n";
        }
    }
    
    // Mostra a estrutura da tabela
    echo "\n=== Estrutura da tabela ===\n\n";
    $stmt = $pdo->query("DESCRIBE tb_Usuario");
    $colunas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($colunas as $coluna) {
        echo "Coluna: " . $coluna['Field'] . "\n";
        echo "Tipo: " . $coluna['Type'] . "\n";
        echo "Nulo?: " . $coluna['Null'] . "\n";
        echo "Chave: " . $coluna['Key'] . "\n";
        echo "Padrão: " . ($coluna['Default'] ?? 'NULL') . "\n";
        echo str_repeat("-", 50) . "\n";
    }
    
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<pre>";
    echo "Erro ao consultar banco de dados:\n";
    echo $e->getMessage() . "\n";
    echo "\nInformações de conexão:\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Usuário: " . DB_USER . "\n";
    echo "</pre>";
} 