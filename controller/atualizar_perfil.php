<?php
require_once '../model/Usuario.php';

header('Content-Type: application/json');

// Lê os dados enviados via JSON
$input = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados esperados foram recebidos
if (!isset($input['id_Docente'], $input['Nome'], $input['telefone'])) {
    echo json_encode(['error' => 'Dados incompletos']);
    exit;
}

try {
    $usuario = new Usuario();
    $resultado = $usuario->atualizarPerfil($input['id_Docente'], $input['Nome'], $input['telefone']);

    if ($resultado) {
        echo json_encode(['message' => 'Perfil atualizado com sucesso!']);
    } else {
        echo json_encode(['error' => 'Falha ao atualizar perfil']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
};
