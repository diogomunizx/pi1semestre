<?php
$servername = "sql206.infinityfree.com";
$username = "if0_39097196";
$password = "V9ymqZGOD3";
$dbname = "if0_39097196_horus";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// echo "Conexão realizada com sucesso!";
?>