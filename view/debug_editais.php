<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Busca todos os editais
    $query = "SELECT * FROM tb_Editais ORDER BY id_edital DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $editais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Dados dos Editais</h2>";
    echo "<pre>";
    print_r($editais);
    echo "</pre>";

    // Mostra a estrutura da tabela
    $query = "DESCRIBE tb_Editais";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $estrutura = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Estrutura da Tabela</h2>";
    echo "<pre>";
    print_r($estrutura);
    echo "</pre>";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?> 