<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['id_Docente'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro de conexão com o banco']);
    exit;
}

$idUsuario = $_SESSION['id_Docente'];

$sql = "SELECT diaSemana, horarioInicio, horarioFim, instituicao FROM tb_HorasAulas WHERE id_Docente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$result = $stmt->get_result();
$dados = [];

while ($row = $result->fetch_assoc()) {
    $dados[] = $row;
}

echo json_encode($dados);
