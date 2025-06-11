<?php
define('DB_HOST', 'sql206.infinityfree.com');
define('DB_NAME', 'if0_39097196_horus');
define('DB_USER', 'if0_39097196');
define('DB_PASS', 'V9ymqZGOD3');
define('DB_CHARSET', 'utf8mb4');

try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

//Usado para testar localhost (usei para redefinir a senha)
/*
$host = 'localhost';
$dbname = 'if0_39097196_horus';
$user = 'root';
$pass = ''; // Deixe vazio no XAMPP local

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?> 
*/