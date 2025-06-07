<?php
require_once '../model/Usuario.php';
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);
$id = $dados['id_Docente'] ?? null;
$senhaAtual = $dados['senhaAtual'] ?? '';
$novaSenha = $dados['novaSenha'] ?? '';

if (!$id || !$senhaAtual || !$novaSenha) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

$usuario = new Usuario();
$dadosUsuario = $usuario->buscarPorId($id);

if (!$dadosUsuario) {
    echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    exit;
}

// Comparação direta sem hash
if ($senhaAtual !== $dadosUsuario['senha']) {
    echo json_encode(['success' => false, 'message' => 'Senha atual incorreta']);
    exit;
}

// Atualiza a senha diretamente
if ($usuario->atualizarSenha($id, $novaSenha)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar senha']);
}