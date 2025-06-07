<?php
header ('Content-Type: application/json');
require_once '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);

$id_item = $data['id_item'];
$id_usuario = $data['id_usuario'];
$data_antiga = $data['data_antiga'];
$data_nova = $data['data_nova'];

try {
    $stmt = $conn->prepare("INSERT INTO tb_dataAlteracoes (id_usuario, id_item, data_antiga, data_nova) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_usuario, $id_item, $data_antiga, $data_nova]);

    echo json_encode(['success' => true, 'message' => 'Data alterada com sucesso!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao alterar data: ' . $e->getMessage()]);
}
?>