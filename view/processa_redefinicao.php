<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM tokens_redefinicao WHERE token = ? AND expira_em > NOW()");
    $stmt->execute([$token]);
    $tokenData = $stmt->fetch();

    if ($tokenData) {
        $email = $tokenData['email'];
        $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE tb_Usuario SET senha = ? WHERE email = ?");
        $stmt->execute([$senhaHash, $email]);

        $conn->prepare("DELETE FROM tokens_redefinicao WHERE token = ?")->execute([$token]);

        echo "Senha redefinida com sucesso.";
    } else {
        echo "Token inválido ou expirado.";
    }
}
?>
